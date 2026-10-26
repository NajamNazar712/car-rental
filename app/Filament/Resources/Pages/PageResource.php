<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\RelationManagers\SectionsRelationManager;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * CMS pages are fixed (seeded by key); admins edit titles, SEO and sections, but never create/delete.
 */
class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    /** Hidden from navigation: website content is static (scope change). Code, routes and data are kept. */
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('key')->disabled()->dehydrated(false),
                        TextInput::make('title')->required()->maxLength(120),
                    ]),
                Section::make('SEO')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextInput::make('meta_title')->maxLength(70)->helperText('Leave empty to use the page title.'),
                        Textarea::make('meta_description')->rows(2)->maxLength(170),
                        SpatieMediaLibraryFileUpload::make('og_image')
                            ->label('Social sharing image')
                            ->collection('og_image')
                            ->image()
                            ->maxSize(5120)
                            ->helperText('Recommended 1200×630.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('key')->badge()->color('gray'),
                TextColumn::make('sections_count')->counts('sections')->label('Sections'),
                TextColumn::make('updated_at')->since()->label('Updated'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [
            SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
