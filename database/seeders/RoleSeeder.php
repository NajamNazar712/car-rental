<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates roles and assigns Shield-generated permissions.
 * super_admin bypasses all checks via Shield's Gate::before, so it needs no explicit permissions.
 */
final class RoleSeeder extends Seeder
{
    /** Resources (Shield subject names) admins may NOT manage. */
    private const ADMIN_EXCLUDED = ['User', 'Role', 'Activity'];

    /** Resources a booking agent may fully manage (minus delete). */
    private const AGENT_MANAGE = ['Booking', 'ContactMessage'];

    /** Resources a booking agent may only read. */
    private const AGENT_READ = ['Car', 'Location', 'Addon'];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Make sure every resource/page/widget permission exists.
        Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin', '--option' => 'permissions', '--no-interaction' => true]);

        foreach (User::PANEL_ROLES as $name) {
            Role::findOrCreate($name, 'web');
        }

        $all = Permission::query()->pluck('name');

        $adminPermissions = $all->reject(fn (string $p): bool => $this->matchesSubject($p, self::ADMIN_EXCLUDED));
        Role::findByName(User::ROLE_ADMIN, 'web')->syncPermissions($adminPermissions);

        $agentPermissions = $all->filter(function (string $p): bool {
            if ($this->matchesSubject($p, self::AGENT_MANAGE)) {
                return ! str_contains(strtolower($p), 'delete');
            }

            if ($this->matchesSubject($p, self::AGENT_READ)) {
                return (bool) preg_match('/^(View|ViewAny|view|view_any)[_:]/', $p);
            }

            return str_starts_with($p, 'View:Dashboard') || str_starts_with($p, 'page_Dashboard');
        });
        Role::findByName(User::ROLE_BOOKING_AGENT, 'web')->syncPermissions($agentPermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Shield v4 permission names look like "ViewAny:Car"; older style "view_any_car". Support both.
     *
     * @param  list<string>  $subjects
     */
    private function matchesSubject(string $permission, array $subjects): bool
    {
        foreach ($subjects as $subject) {
            if (str_ends_with($permission, ':'.$subject)) {
                return true;
            }

            $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $subject));
            if (preg_match('/_'.preg_quote($snake, '/').'$/', $permission)) {
                return true;
            }
        }

        return false;
    }
}
