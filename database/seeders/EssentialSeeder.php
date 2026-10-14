<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Data production needs: roles, admin user (and, from Milestone 3, pages, sections, settings).
 * Run alone in production: php artisan db:seed --class=EssentialSeeder --force
 */
final class EssentialSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
