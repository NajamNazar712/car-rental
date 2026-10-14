<?php

namespace App\Filament\Resources\Cars\Schemas;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Feature;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CarForm
{
    /** Rich editor toolbar used across the admin: bold, italic, lists, links, h2/h3 only. */
    public const RICH_TOOLBAR = [['bold', 'italic', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList'], ['undo', 'redo']];

    private const IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Car')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        self::basicTab(),
                        self::pricingTab(),
                        self::specsTab(),
                        self::featuresTab(),
                        self::mediaTab(),
                        self::contentTab(),
                        self::seoTab(),
                    ]),
            ]);
    }

    private static function basicTab(): Tab
    {
        return Tab::make('Basic info')
            ->columns(2)
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, string $operation): void {
                        if ($operation === 'create' || blank($get('slug'))) {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->alphaDash()
                    ->maxLength(180)
                    ->unique(ignoreRecord: true),
                Select::make('category_id')
                    ->label('Category')
                    ->options(fn (): array => Category::query()->ordered()->get()->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                Select::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('car_model_id', null)),
                Select::make('car_model_id')
                    ->label('Model')
                    ->options(fn (Get $get): array => CarModel::query()
                        ->where('brand_id', $get('brand_id'))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->disabled(fn (Get $get): bool => blank($get('brand_id')))
                    ->searchable()
                    ->required(),
                TextInput::make('year')
                    ->numeric()
                    ->minValue(1990)
                    ->maxValue((int) date('Y') + 1)
                    ->default((int) date('Y'))
                    ->required(),
                Select::make('status')
                    ->options(CarStatus::class)
                    ->default(CarStatus::Active)
                    ->required(),
                Toggle::make('is_featured')->label('Featured on home page')->inline(false),
            ]);
    }

    private static function pricingTab(): Tab
    {
        $money = fn (string $name, string $label) => TextInput::make($name)
            ->label($label)
            ->numeric()
            ->minValue(0)
            ->step('0.01');

        return Tab::make('Pricing')
            ->columns(3)
            ->schema([
                $money('price_day', 'Price per day')->required(),
                $money('price_week', 'Price per week'),
                $money('price_month', 'Price per month'),
                $money('offer_price_day', 'Offer price per day')
                    ->helperText('Shown instead of the daily price, with the original struck through.')
                    ->lt('price_day'),
                $money('deposit', 'Security deposit'),
            ]);
    }

    private static function specsTab(): Tab
    {
        return Tab::make('Specs')
            ->columns(3)
            ->schema([
                Select::make('transmission')->options(Transmission::class)->default(Transmission::Automatic)->required(),
                Select::make('fuel_type')->options(FuelType::class)->default(FuelType::Petrol)->required(),
                TextInput::make('seats')->numeric()->minValue(1)->maxValue(60)->default(5)->required(),
                TextInput::make('doors')->numeric()->minValue(1)->maxValue(6)->default(4)->required(),
                TextInput::make('luggage')->label('Luggage (bags)')->numeric()->minValue(0)->maxValue(30),
                TextInput::make('engine')->maxLength(60)->placeholder('2.0L Turbo'),
                TextInput::make('colour')->maxLength(40),
                TextInput::make('mileage_limit_per_day')->label('Mileage limit / day (km)')->numeric()->minValue(0),
            ]);
    }

    private static function featuresTab(): Tab
    {
        return Tab::make('Features')
            ->schema([
                CheckboxList::make('features')
                    ->relationship('features', 'id', fn ($query) => $query->orderBy('sort_order'))
                    ->getOptionLabelFromRecordUsing(fn (Feature $record): string => (string) $record->name)
                    ->columns(3)
                    ->bulkToggleable(),
            ]);
    }

    private static function mediaTab(): Tab
    {
        return Tab::make('Media')
            ->schema([
                SpatieMediaLibraryFileUpload::make('main')
                    ->label('Main image')
                    ->collection('main')
                    ->image()
                    ->acceptedFileTypes(self::IMAGE_TYPES)
                    ->maxSize(5120)
                    ->imageEditor(),
                SpatieMediaLibraryFileUpload::make('gallery')
                    ->collection('gallery')
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->panelLayout('grid')
                    ->image()
                    ->acceptedFileTypes(self::IMAGE_TYPES)
                    ->maxSize(5120)
                    ->maxFiles(Car::GALLERY_MAX)
                    ->helperText('Up to '.Car::GALLERY_MAX.' images, 5 MB each. Drag to reorder.'),
            ]);
    }

    private static function contentTab(): Tab
    {
        return Tab::make('Content')
            ->schema([
                Textarea::make('short_description')->rows(2)->maxLength(300),
                RichEditor::make('description')->toolbarButtons(self::RICH_TOOLBAR),
                RichEditor::make('rental_terms')->toolbarButtons(self::RICH_TOOLBAR),
            ]);
    }

    private static function seoTab(): Tab
    {
        return Tab::make('SEO')
            ->schema([
                TextInput::make('meta_title')->maxLength(70)->helperText('Leave empty to generate from the car name.'),
                Textarea::make('meta_description')->rows(2)->maxLength(170),
            ]);
    }
}
