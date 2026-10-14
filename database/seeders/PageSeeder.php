<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;

/**
 * Essential: every CMS page and its sections with placeholder English content.
 * Idempotent — existing pages/sections are left untouched so admin edits survive re-seeding.
 */
final class PageSeeder extends Seeder
{
    public function run(): void
    {
        config(['media-library.queue_conversions_by_default' => false]);

        foreach ($this->pages() as $key => $definition) {
            $page = Page::query()->firstOrCreate(['key' => $key], [
                'title' => ['en' => $definition['title']],
                'meta_title' => ['en' => $definition['meta_title'] ?? null],
                'meta_description' => ['en' => $definition['meta_description']],
            ]);

            foreach ($definition['sections'] as $order => $section) {
                $model = PageSection::query()->firstOrNew(['page_id' => $page->id, 'key' => $section['key']]);

                if ($model->exists) {
                    continue;
                }

                $model->fill([
                    'type' => $section['type'],
                    'content' => ['en' => $section['content']],
                    'sort_order' => $order,
                    'is_visible' => true,
                ])->save();

                foreach ($section['images'] ?? [] as $i => [$label, $rgb, $w, $h, $car]) {
                    $model->addMedia(PlaceholderImage::make($label, $rgb, $w, $h, $car))
                        ->usingFileName("{$key}-{$section['key']}-".($i + 1).'.jpg')
                        ->withCustomProperties(['alt' => ucfirst(strtolower($label))])
                        ->toMediaCollection('images');
                }
            }
        }
    }

    /** @return array<string, array<string, mixed>> */
    private function pages(): array
    {
        $legal = '<p>This is placeholder text. Replace it with your own legal content in Admin → Website Content → Pages.</p>';

        return [
            'home' => [
                'title' => 'Home',
                'meta_title' => 'Car Rental in Dubai — Economy to Luxury Cars',
                'meta_description' => 'Rent economy, SUV, luxury and sports cars with free delivery, transparent prices and 24/7 WhatsApp support.',
                'sections' => [
                    [
                        'key' => 'hero', 'type' => 'hero',
                        'content' => ['slides' => [
                            ['heading' => 'Rent your perfect car today', 'subheading' => 'From city runabouts to luxury SUVs — delivered to your door, with no hidden fees.', 'primary_label' => 'Browse our fleet', 'primary_url' => '/fleet', 'show_whatsapp' => true, 'whatsapp_label' => 'Chat on WhatsApp'],
                            ['heading' => 'Luxury cars for every occasion', 'subheading' => 'Arrive in style with our premium Mercedes-Benz, BMW and Porsche collection.', 'primary_label' => 'View luxury cars', 'primary_url' => '/fleet/category/luxury', 'show_whatsapp' => true, 'whatsapp_label' => 'Ask on WhatsApp'],
                            ['heading' => 'Weekly and monthly deals', 'subheading' => 'Save more the longer you drive. Flexible long-term rentals with free maintenance.', 'primary_label' => 'Request a quote', 'primary_url' => '/booking', 'show_whatsapp' => false, 'whatsapp_label' => ''],
                        ]],
                        'images' => [
                            ['Your journey starts here', [15, 76, 129], 1920, 1080, true],
                            ['Luxury collection', [25, 25, 35], 1920, 1080, true],
                            ['Long term deals', [120, 70, 20], 1920, 1080, true],
                        ],
                    ],
                    ['key' => 'quick_search', 'type' => 'quick_search', 'content' => ['heading' => 'Find your car', 'subheading' => 'Choose a category or brand to see available cars.']],
                    ['key' => 'featured_cars', 'type' => 'featured_cars', 'content' => ['heading' => 'Featured cars', 'subheading' => 'Hand-picked favourites from our fleet, ready to drive.']],
                    ['key' => 'categories', 'type' => 'categories', 'content' => ['heading' => 'Browse by category', 'subheading' => 'Whatever the trip, we have the right car.']],
                    [
                        'key' => 'why_us', 'type' => 'why_us',
                        'content' => ['heading' => 'Why rent with us', 'subheading' => 'Simple, transparent and on your schedule.', 'items' => [
                            ['icon' => 'heroicon-o-truck', 'title' => 'Free delivery', 'text' => 'We deliver and collect your car at your home, hotel or the airport.'],
                            ['icon' => 'heroicon-o-banknotes', 'title' => 'No hidden fees', 'text' => 'Clear daily, weekly and monthly prices with insurance included.'],
                            ['icon' => 'heroicon-o-shield-check', 'title' => 'Fully insured', 'text' => 'Every rental includes comprehensive insurance and roadside assistance.'],
                            ['icon' => 'heroicon-o-chat-bubble-left-right', 'title' => '24/7 support', 'text' => 'Reach our team any time by phone or WhatsApp.'],
                        ]],
                    ],
                    [
                        'key' => 'how_it_works', 'type' => 'how_it_works',
                        'content' => ['heading' => 'How it works', 'subheading' => 'Book your car in three easy steps.', 'steps' => [
                            ['title' => 'Choose your car', 'text' => 'Browse the fleet and pick the car that suits your trip.'],
                            ['title' => 'Send a booking request', 'text' => 'Tell us your dates and pickup location — it takes a minute.'],
                            ['title' => 'We confirm and deliver', 'text' => 'Our team confirms by phone or WhatsApp and delivers your car.'],
                        ]],
                    ],
                    ['key' => 'brands', 'type' => 'brands', 'content' => ['heading' => 'Brands we offer', 'subheading' => '']],
                    ['key' => 'testimonials', 'type' => 'testimonials', 'content' => ['heading' => 'What our customers say', 'subheading' => '']],
                    [
                        'key' => 'cta', 'type' => 'cta',
                        'content' => ['heading' => 'Ready to hit the road?', 'text' => 'Send a booking request now and our team will confirm within 30 minutes during working hours.', 'primary_label' => 'Book now', 'primary_url' => '/booking', 'show_whatsapp' => true, 'whatsapp_label' => 'Chat on WhatsApp'],
                        'images' => [['Ready to drive', [11, 58, 99], 1920, 800, true]],
                    ],
                ],
            ],
            'about' => [
                'title' => 'About Us',
                'meta_description' => 'Learn about our car rental company, our fleet and our commitment to great service.',
                'sections' => [
                    [
                        'key' => 'intro', 'type' => 'intro',
                        'content' => ['heading' => 'Driving you forward since 2012', 'body' => '<p>We are a locally owned car rental company offering a modern fleet of economy, family, luxury and sports cars. Our mission is simple: make renting a car easy, transparent and friendly.</p><p>Every car is regularly serviced, fully insured and delivered clean to your door.</p>'],
                        'images' => [['Our team', [15, 76, 129], 1200, 900, false]],
                    ],
                    [
                        'key' => 'stats', 'type' => 'stats',
                        'content' => ['heading' => '', 'items' => [
                            ['number' => '12+', 'label' => 'Years of experience'],
                            ['number' => '150+', 'label' => 'Cars in our fleet'],
                            ['number' => '25,000+', 'label' => 'Happy customers'],
                            ['number' => '24/7', 'label' => 'Customer support'],
                        ]],
                    ],
                    ['key' => 'mission', 'type' => 'text', 'content' => ['heading' => 'Our mission', 'text' => 'To give every customer a reliable car, an honest price and service that makes them want to come back.']],
                    [
                        'key' => 'gallery', 'type' => 'gallery',
                        'content' => ['heading' => 'Our showroom'],
                        'images' => array_map(fn (int $n): array => ["Showroom {$n}", [20 + 15 * $n, 60, 110], 1200, 900, $n % 2 === 0], range(1, 6)),
                    ],
                ],
            ],
            'services' => [
                'title' => 'Services',
                'meta_description' => 'Daily, weekly and monthly car rental, chauffeur service, airport delivery and corporate leasing.',
                'sections' => [
                    ['key' => 'intro', 'type' => 'intro', 'content' => ['heading' => 'Our services', 'body' => '<p>More than car rental — everything you need to get around comfortably.</p>']],
                    ['key' => 'services', 'type' => 'services_grid', 'content' => ['heading' => 'What we offer', 'subheading' => '']],
                    ['key' => 'cta', 'type' => 'cta', 'content' => ['heading' => 'Need something specific?', 'text' => 'Tell us what you need and we will tailor a rental for you.', 'primary_label' => 'Contact us', 'primary_url' => '/contact', 'show_whatsapp' => true, 'whatsapp_label' => 'WhatsApp us']],
                ],
            ],
            'faq' => [
                'title' => 'FAQ & Rental Terms',
                'meta_description' => 'Answers to common questions about renting a car, documents, deposits, insurance and our rental terms.',
                'sections' => [
                    ['key' => 'intro', 'type' => 'intro', 'content' => ['heading' => 'Frequently asked questions', 'body' => '<p>Can’t find your answer? Contact us on WhatsApp and we will help right away.</p>']],
                    ['key' => 'faqs', 'type' => 'faq_list', 'content' => ['heading' => 'Questions & answers', 'subheading' => '']],
                    ['key' => 'terms', 'type' => 'rich_text', 'content' => ['heading' => 'Rental terms', 'body' => '<h3>Driver requirements</h3><ul><li>Minimum age 21 (25 for luxury and sports cars).</li><li>Valid driving licence held for at least one year.</li><li>Passport or national ID.</li></ul><h3>Deposit</h3><p>A refundable security deposit is taken at delivery and released within 14 days after the car is returned.</p><h3>Fuel and mileage</h3><p>Cars are delivered with a full tank and should be returned the same way. A daily mileage limit applies as shown on each car.</p>']],
                ],
            ],
            'contact' => [
                'title' => 'Contact Us',
                'meta_description' => 'Get in touch by phone, email or WhatsApp. We reply quickly.',
                'sections' => [
                    ['key' => 'intro', 'type' => 'intro', 'content' => ['heading' => 'Get in touch', 'body' => '<p>Questions about a car or a booking? Call, email or WhatsApp us — or send a message using the form.</p>']],
                    ['key' => 'form', 'type' => 'contact_form', 'content' => ['heading' => 'Send us a message', 'subheading' => 'We usually reply within an hour during working hours.']],
                ],
            ],
            'fleet' => [
                'title' => 'Our Fleet',
                'meta_description' => 'Browse our full fleet of rental cars — filter by category, brand, transmission, seats and price.',
                'sections' => [
                    ['key' => 'intro', 'type' => 'intro', 'content' => ['heading' => 'Our fleet', 'body' => '<p>Filter by category, brand or price to find the right car.</p>']],
                ],
            ],
            'booking' => [
                'title' => 'Book a Car',
                'meta_description' => 'Send a booking request in a minute. Our team confirms availability and price.',
                'sections' => [
                    ['key' => 'intro', 'type' => 'intro', 'content' => ['heading' => 'Request a booking', 'body' => '<p>Fill in your details and we will confirm availability and the final price by phone, email or WhatsApp. No payment is taken online.</p>']],
                ],
            ],
            'privacy' => [
                'title' => 'Privacy Policy',
                'meta_description' => 'How we collect, use and protect your personal information.',
                'sections' => [
                    ['key' => 'content', 'type' => 'rich_text', 'content' => ['heading' => 'Privacy policy', 'body' => $legal.'<h2>Information we collect</h2><p>When you send a booking request or message we collect your name, phone number, email and booking details.</p><h2>How we use it</h2><p>Only to handle your request and contact you about it.</p>']],
                ],
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'meta_description' => 'Terms and conditions for using our website and rental services.',
                'sections' => [
                    ['key' => 'content', 'type' => 'rich_text', 'content' => ['heading' => 'Terms and conditions', 'body' => $legal.'<h2>Booking requests</h2><p>A booking request is not a confirmed reservation until our team confirms it.</p>']],
                ],
            ],
        ];
    }
}
