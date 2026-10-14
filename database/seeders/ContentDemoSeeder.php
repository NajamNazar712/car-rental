<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Service;
use App\Models\Testimonial;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;

/**
 * Demo: services, testimonials, FAQs (skipped per table when records already exist).
 */
final class ContentDemoSeeder extends Seeder
{
    public function run(): void
    {
        config(['media-library.queue_conversions_by_default' => false]);

        $this->services();
        $this->testimonials();
        $this->faqs();
    }

    private function services(): void
    {
        if (Service::query()->exists()) {
            return;
        }

        $services = [
            ['Daily & weekly rental', 'Flexible short-term rentals from one day, with free delivery and collection anywhere in the city.', 'heroicon-o-calendar-days', [15, 76, 129]],
            ['Monthly & long-term leasing', 'Lower monthly rates with maintenance, insurance and replacement cars included.', 'heroicon-o-clock', [30, 100, 80]],
            ['Chauffeur service', 'Professional, multilingual drivers for business travel, events and sightseeing.', 'heroicon-o-user', [60, 40, 100]],
            ['Airport delivery', 'Your car waiting at arrivals — no queues, no shuttle buses.', 'heroicon-o-paper-airplane', [120, 70, 20]],
        ];

        foreach ($services as $i => [$title, $description, $icon, $rgb]) {
            $service = Service::query()->create([
                'title' => ['en' => $title],
                'description' => ['en' => $description],
                'icon' => $icon,
                'sort_order' => $i,
                'is_active' => true,
            ]);
            $service->addMedia(PlaceholderImage::make(strtoupper($title), $rgb, 1200, 800, true))
                ->usingFileName("service-{$i}.jpg")
                ->toMediaCollection('image');
        }
    }

    private function testimonials(): void
    {
        if (Testimonial::query()->exists()) {
            return;
        }

        $items = [
            ['Sarah M.', 'Booking was so easy — I sent a request in the evening and the car was at my hotel the next morning. Spotless and exactly as pictured.', 5],
            ['Ahmed K.', 'Rented the Nissan Patrol for a desert weekend. Great condition, fair price and the team answered every question on WhatsApp.', 5],
            ['James T.', 'Used them for a month-long work trip. The monthly rate was the best I found and they swapped the car for a service without any fuss.', 5],
            ['Olga P.', 'Friendly staff and a lovely car for our anniversary. Delivery to the airport was right on time.', 5],
            ['Rahul S.', 'Transparent pricing — the final price matched the estimate. Will definitely rent again.', 4],
            ['Maria L.', 'Child seat was already fitted when the car arrived. Small detail, big difference with two kids!', 5],
        ];

        foreach ($items as $i => [$name, $content, $rating]) {
            Testimonial::query()->create([
                'customer_name' => $name,
                'content' => ['en' => $content],
                'rating' => $rating,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }

    private function faqs(): void
    {
        if (Faq::query()->exists()) {
            return;
        }

        $faqs = [
            ['Booking', 'Is my booking confirmed when I submit the form?', 'No. The form sends a booking request. Our team checks availability and confirms the booking and final price by phone, email or WhatsApp.'],
            ['Booking', 'How far in advance should I book?', 'We recommend 2–3 days ahead, especially for luxury cars and holidays, but same-day rentals are often possible.'],
            ['Booking', 'Can I change or cancel my booking?', 'Yes. Contact us as early as possible and we will update or cancel your booking free of charge before delivery.'],
            ['Documents', 'What documents do I need?', 'A valid driving licence, your passport or national ID, and a credit card for the security deposit.'],
            ['Documents', 'Can tourists rent a car?', 'Yes. Visitors from most countries can drive with their home licence or an International Driving Permit.'],
            ['Payment', 'How much is the security deposit?', 'It depends on the car and is shown on each car page. It is refunded within 14 days after the car is returned.'],
            ['Payment', 'Which payment methods do you accept?', 'Credit and debit cards, bank transfer and cash. Payment is arranged when your booking is confirmed.'],
            ['Insurance', 'Is insurance included?', 'Yes. All rentals include comprehensive insurance. Full cover with zero excess is available as an add-on.'],
            ['Delivery', 'Do you deliver the car?', 'Yes, we deliver and collect at your home, hotel, office or the airport. Delivery within the city is free.'],
            ['Delivery', 'What happens if the car breaks down?', 'Call our 24/7 support line. We provide roadside assistance and a replacement car if needed.'],
        ];

        foreach ($faqs as $i => [$group, $question, $answer]) {
            Faq::query()->create([
                'group' => ['en' => $group],
                'question' => ['en' => $question],
                'answer' => ['en' => "<p>{$answer}</p>"],
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }
}
