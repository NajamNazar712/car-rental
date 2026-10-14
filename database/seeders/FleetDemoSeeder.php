<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Feature;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class FleetDemoSeeder extends Seeder
{
    private const CATEGORIES = [
        'Economy' => 'Affordable, fuel-efficient cars for everyday driving around the city.',
        'Sedan' => 'Comfortable saloons with space for the whole family and their luggage.',
        'SUV' => 'Spacious, high-riding vehicles ready for city streets and desert roads.',
        'Luxury' => 'Premium cars with refined interiors for business and special occasions.',
        'Sports' => 'High-performance coupes and convertibles for an unforgettable drive.',
        'Van' => 'People carriers and vans for groups, events and airport transfers.',
    ];

    private const BRANDS = [
        'Toyota' => ['Yaris', 'Camry', 'Land Cruiser', 'Hiace'],
        'Nissan' => ['Sunny', 'Altima', 'Patrol'],
        'Hyundai' => ['Accent', 'Elantra', 'Tucson'],
        'Kia' => ['Picanto', 'Sportage', 'Carnival'],
        'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'V-Class'],
        'BMW' => ['3 Series', '5 Series', 'X5'],
        'Porsche' => ['911', 'Cayenne'],
        'Range Rover' => ['Sport', 'Vogue'],
    ];

    private const FEATURES = [
        ['Air conditioning', 'heroicon-o-sun'],
        ['Bluetooth', 'heroicon-o-signal'],
        ['Apple CarPlay / Android Auto', 'heroicon-o-device-phone-mobile'],
        ['Navigation system', 'heroicon-o-map'],
        ['Rear camera', 'heroicon-o-camera'],
        ['Parking sensors', 'heroicon-o-viewfinder-circle'],
        ['Cruise control', 'heroicon-o-forward'],
        ['Leather seats', 'heroicon-o-sparkles'],
        ['Sunroof', 'heroicon-o-square-2-stack'],
        ['Keyless entry', 'heroicon-o-key'],
        ['USB charging', 'heroicon-o-bolt'],
        ['Child seat compatible', 'heroicon-o-user-group'],
    ];

    /** [category, brand, model, year, transmission, fuel, seats, doors, luggage, engine, colour, day, week, month, offer, deposit, featured, rgb] */
    private const CARS = [
        ['Economy', 'Toyota', 'Yaris', 2024, 'automatic', 'petrol', 5, 4, 2, '1.5L', 'White', 99, 590, 1990, 89, 1000, true, [40, 110, 170]],
        ['Economy', 'Kia', 'Picanto', 2024, 'automatic', 'petrol', 4, 4, 1, '1.2L', 'Red', 79, 470, 1590, null, 1000, false, [170, 40, 50]],
        ['Economy', 'Nissan', 'Sunny', 2023, 'automatic', 'petrol', 5, 4, 2, '1.6L', 'Silver', 89, 530, 1790, null, 1000, false, [90, 100, 115]],
        ['Sedan', 'Toyota', 'Camry', 2024, 'automatic', 'hybrid', 5, 4, 3, '2.5L Hybrid', 'Black', 179, 1090, 3490, 159, 1500, true, [25, 30, 40]],
        ['Sedan', 'Hyundai', 'Elantra', 2024, 'automatic', 'petrol', 5, 4, 3, '2.0L', 'Blue', 139, 840, 2690, null, 1500, false, [30, 70, 150]],
        ['SUV', 'Nissan', 'Patrol', 2024, 'automatic', 'petrol', 7, 5, 4, '5.6L V8', 'Pearl White', 499, 2990, 9490, 449, 3000, true, [120, 125, 135]],
        ['SUV', 'Hyundai', 'Tucson', 2024, 'automatic', 'petrol', 5, 5, 3, '2.0L', 'Grey', 199, 1190, 3790, null, 2000, false, [70, 80, 90]],
        ['Luxury', 'Mercedes-Benz', 'S-Class', 2024, 'automatic', 'petrol', 5, 4, 3, '3.0L Turbo', 'Obsidian Black', 1199, 7190, 22990, null, 5000, true, [20, 22, 28]],
        ['Luxury', 'BMW', '5 Series', 2024, 'automatic', 'petrol', 5, 4, 3, '2.0L Turbo', 'Alpine White', 599, 3590, 11490, 549, 3000, true, [60, 90, 130]],
        ['Sports', 'Porsche', '911', 2023, 'automatic', 'petrol', 4, 2, 1, '3.0L Twin Turbo', 'Guards Red', 1899, 11390, null, null, 10000, true, [180, 30, 30]],
        ['Sports', 'Range Rover', 'Sport', 2024, 'automatic', 'hybrid', 5, 5, 4, '3.0L P440e', 'Santorini Black', 1299, 7790, 24990, 1199, 5000, false, [35, 40, 45]],
        ['Van', 'Toyota', 'Hiace', 2023, 'manual', 'diesel', 13, 4, 8, '2.8L Diesel', 'White', 349, 2090, 6690, null, 2000, false, [110, 130, 150]],
    ];

    public function run(): void
    {
        // Generate image conversions immediately so the demo works without a queue worker.
        config(['media-library.queue_conversions_by_default' => false]);

        $categories = [];
        $sort = 0;
        foreach (self::CATEGORIES as $name => $description) {
            $categories[$name] = Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => ['en' => $name], 'description' => ['en' => $description], 'sort_order' => $sort++, 'is_active' => true],
            );
        }

        $brands = [];
        $models = [];
        $sort = 0;
        foreach (self::BRANDS as $brandName => $modelNames) {
            $brand = Brand::query()->updateOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName, 'sort_order' => $sort++, 'is_active' => true],
            );
            if (! $brand->hasMedia('logo')) {
                $brand->addMedia(PlaceholderImage::make(strtoupper($brandName), [55, 65, 81], 600, 300))
                    ->usingFileName(Str::slug($brandName).'.jpg')
                    ->toMediaCollection('logo');
            }
            $brands[$brandName] = $brand;

            foreach ($modelNames as $modelName) {
                $models[$brandName][$modelName] = CarModel::query()->updateOrCreate(
                    ['brand_id' => $brand->id, 'slug' => Str::slug($modelName)],
                    ['name' => $modelName, 'is_active' => true],
                );
            }
        }

        $featureIds = [];
        foreach (self::FEATURES as $i => [$name, $icon]) {
            $feature = Feature::query()->firstOrNew(['icon' => $icon]);
            $feature->fill(['name' => ['en' => $name], 'sort_order' => $i])->save();
            $featureIds[] = $feature->id;
        }

        foreach (self::CARS as $i => $row) {
            [$cat, $brandName, $modelName, $year, $trans, $fuel, $seats, $doors, $luggage, $engine, $colour,
                $day, $week, $month, $offer, $deposit, $featured, $rgb] = $row;

            $name = "{$brandName} {$modelName}";
            $slug = Str::slug("{$name} {$year}");

            $car = Car::withTrashed()->updateOrCreate(['slug' => $slug], [
                'category_id' => $categories[$cat]->id,
                'brand_id' => $brands[$brandName]->id,
                'car_model_id' => $models[$brandName][$modelName]->id,
                'name' => ['en' => $name],
                'year' => $year,
                'transmission' => Transmission::from($trans),
                'fuel_type' => FuelType::from($fuel),
                'seats' => $seats,
                'doors' => $doors,
                'luggage' => $luggage,
                'engine' => $engine,
                'colour' => $colour,
                'mileage_limit_per_day' => 250,
                'price_day' => $day,
                'price_week' => $week,
                'price_month' => $month,
                'offer_price_day' => $offer,
                'deposit' => $deposit,
                'short_description' => ['en' => "Rent the {$year} {$name} — {$engine}, {$seats} seats, {$colour}."],
                'description' => ['en' => "<p>The {$year} {$name} combines comfort, reliability and style. It is delivered clean, fully insured and ready to drive.</p><h3>Why choose this car</h3><ul><li>Well maintained and regularly serviced</li><li>Comprehensive insurance included</li><li>Free delivery within the city</li></ul>"],
                'rental_terms' => ['en' => '<ul><li>Minimum driver age: 21 years (25 for luxury and sports cars)</li><li>Valid driving licence and passport or ID required</li><li>Security deposit refunded within 14 days after return</li><li>Additional kilometres charged per the rental agreement</li></ul>'],
                'status' => CarStatus::Active,
                'is_featured' => $featured,
                'sort_order' => $i,
            ]);

            $car->features()->sync(collect($featureIds)->shuffle()->take(random_int(5, 9))->all());

            if (! $car->hasMedia('main')) {
                $car->addMedia(PlaceholderImage::make(strtoupper($name), $rgb, 1600, 1000, car: true))
                    ->usingFileName($slug.'.jpg')
                    ->withCustomProperties(['alt' => "{$year} {$name}"])
                    ->toMediaCollection('main');

                foreach (['Front view', 'Interior', 'Rear view'] as $n => $view) {
                    $shade = array_map(fn (int $v): int => max(0, $v - 15 * ($n + 1)), $rgb);
                    $car->addMedia(PlaceholderImage::make(strtoupper("{$name} - {$view}"), $shade, 1600, 1000, car: $n !== 1))
                        ->usingFileName("{$slug}-".($n + 1).'.jpg')
                        ->withCustomProperties(['alt' => "{$year} {$name} {$view}"])
                        ->toMediaCollection('gallery');
                }
            }
        }
    }
}
