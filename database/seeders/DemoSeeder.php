<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Demo content (fleet, testimonials, FAQs...). Never required in production.
 */
final class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FleetDemoSeeder::class,
        ]);
    }
}
