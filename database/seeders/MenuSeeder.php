<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MenuLocation;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

/**
 * Essential: default header and footer menus (only seeded when no menu items exist).
 */
final class MenuSeeder extends Seeder
{
    public function run(): void
    {
        if (MenuItem::query()->exists()) {
            return;
        }

        $menus = [
            MenuLocation::Header->value => [
                ['Home', '/'], ['Fleet', '/fleet'], ['Services', '/services'], ['About', '/about'], ['FAQ', '/faq'], ['Contact', '/contact'],
            ],
            MenuLocation::Footer->value => [
                ['Our fleet', '/fleet'], ['About us', '/about'], ['FAQ & rental terms', '/faq'], ['Privacy policy', '/privacy'], ['Terms & conditions', '/terms'],
            ],
        ];

        foreach ($menus as $location => $items) {
            foreach ($items as $i => [$label, $url]) {
                MenuItem::query()->create([
                    'location' => $location,
                    'label' => ['en' => $label],
                    'url' => $url,
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }
    }
}
