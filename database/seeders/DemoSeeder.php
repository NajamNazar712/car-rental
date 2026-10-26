<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Demo content (fleet, services, testimonials, FAQs). Never required in production.
 * Staff users and their roles are created manually in Users & Access.
 */
final class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FleetDemoSeeder::class,
            ContentDemoSeeder::class,
        ]);
    }
}
