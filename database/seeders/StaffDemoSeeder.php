<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo: one user per non-super role, for checking permissions. Never run in production.
 */
final class StaffDemoSeeder extends Seeder
{
    public function run(): void
    {
        $staff = [
            ['manager@carrental.test', 'Demo Manager', 'Manager@12345', User::ROLE_ADMIN],
            ['agent@carrental.test', 'Demo Booking Agent', 'Agent@12345', User::ROLE_BOOKING_AGENT],
        ];

        foreach ($staff as [$email, $name, $password, $role]) {
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => $password, 'is_active' => true, 'email_verified_at' => now()],
            );
            $user->syncRoles([$role]);
        }
    }
}
