<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Data production needs: roles + permissions, the super admin, CMS pages/sections and menus.
 * Default settings are created by the settings migrations (database/settings).
 * Run alone in production: php artisan db:seed --class=EssentialSeeder --force
 */
final class EssentialSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            PageSeeder::class,
            MenuSeeder::class,
        ]);
    }
}
