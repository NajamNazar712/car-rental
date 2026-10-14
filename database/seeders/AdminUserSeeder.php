<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.');
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->fill([
            'name' => env('ADMIN_NAME', 'Super Admin'),
            'is_active' => true,
        ]);

        if (! $user->exists) {
            $user->password = $password;
            $user->email_verified_at = now();
        }

        $user->save();
        $user->syncRoles([User::ROLE_SUPER_ADMIN]);
    }
}
