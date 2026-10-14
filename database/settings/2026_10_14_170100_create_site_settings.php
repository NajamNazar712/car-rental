<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Default settings values. These are production-safe defaults; edit them in Admin → Settings.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'DriveEasy Car Rental');
        $this->migrator->add('general.tagline', 'Premium and economy car rental with free delivery');
        $this->migrator->add('general.currency_code', 'AED');
        $this->migrator->add('general.currency_symbol', 'AED');
        $this->migrator->add('general.maintenance_banner', null);
        $this->migrator->add('general.logo_light', null);
        $this->migrator->add('general.logo_dark', null);
        $this->migrator->add('general.favicon', null);
        $this->migrator->add('general.primary_color', '#0f4c81');
        $this->migrator->add('general.default_phone_country', '+971');

        $this->migrator->add('contact.phones', ['+971 50 000 0000']);
        $this->migrator->add('contact.emails', ['info@example.com']);
        $this->migrator->add('contact.address', 'Office 101, Example Tower, Sheikh Zayed Road, Dubai, UAE');
        $this->migrator->add('contact.map_embed_url', 'https://www.google.com/maps?q=Dubai&output=embed');
        $this->migrator->add('contact.working_hours', [
            ['day' => 'Monday – Friday', 'hours' => '08:00 – 22:00'],
            ['day' => 'Saturday', 'hours' => '09:00 – 21:00'],
            ['day' => 'Sunday', 'hours' => '10:00 – 18:00'],
        ]);

        $this->migrator->add('whatsapp.enabled', true);
        $this->migrator->add('whatsapp.numbers', [['label' => 'Bookings', 'number' => '+971500000000']]);
        $this->migrator->add('whatsapp.template_general', 'Hello! I would like to know more about renting a car.');
        $this->migrator->add('whatsapp.template_car', 'Hello! I am interested in renting the {car} ({year}). {url}');
        $this->migrator->add('whatsapp.template_booking', 'Hello! I just submitted booking request {reference}. Could you please confirm?');

        $this->migrator->add('social.facebook', null);
        $this->migrator->add('social.instagram', null);
        $this->migrator->add('social.tiktok', null);
        $this->migrator->add('social.x', null);
        $this->migrator->add('social.youtube', null);
        $this->migrator->add('social.linkedin', null);

        $this->migrator->add('notifications.booking_recipients', ['bookings@example.com']);
        $this->migrator->add('notifications.send_customer_confirmation', true);

        $this->migrator->add('integrations.ga4_id', null);
        $this->migrator->add('integrations.gtm_id', null);
        $this->migrator->add('integrations.meta_pixel_id', null);
        $this->migrator->add('integrations.turnstile_site_key', null);
        $this->migrator->addEncrypted('integrations.turnstile_secret_key', null);

        $this->migrator->add('booking.show_estimated_price', true);
        $this->migrator->add('booking.min_rental_days', 1);
        $this->migrator->add('booking.terms_page_url', '/terms');
    }
};
