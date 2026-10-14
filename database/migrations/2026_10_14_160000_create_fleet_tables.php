<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('car_models', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['brand_id', 'slug']);
        });

        Schema::create('features', function (Blueprint $table): void {
            $table->id();
            $table->json('name');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cars', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('car_model_id')->constrained()->restrictOnDelete();
            $table->json('name');
            $table->string('slug')->unique();
            $table->smallInteger('year');
            $table->string('transmission', 20);
            $table->string('fuel_type', 20);
            $table->unsignedTinyInteger('seats');
            $table->unsignedTinyInteger('doors');
            $table->unsignedTinyInteger('luggage')->nullable();
            $table->string('engine')->nullable();
            $table->string('colour')->nullable();
            $table->unsignedInteger('mileage_limit_per_day')->nullable();
            $table->decimal('price_day', 10, 2);
            $table->decimal('price_week', 10, 2)->nullable();
            $table->decimal('price_month', 10, 2)->nullable();
            $table->decimal('offer_price_day', 10, 2)->nullable();
            $table->decimal('deposit', 10, 2)->nullable();
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->json('rental_terms')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->integer('sort_order')->default(0);
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('car_feature', function (Blueprint $table): void {
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->primary(['car_id', 'feature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_feature');
        Schema::dropIfExists('cars');
        Schema::dropIfExists('features');
        Schema::dropIfExists('car_models');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
