<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Content\SectionTypeRegistry;
use App\Models\PageSection;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

/**
 * Page sections: fixed set per page (each type maps to a Blade component); editable content,
 * visibility and order. The form is built from the section type registry.
 */
class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Sections';

    public function form(Schema $schema): Schema
    {
        /** @var PageSection|null $section */
        $section = $schema->getRecord();
        $type = $section?->sectionType();

        $components = [
            Toggle::make('is_visible')->label('Visible on the website'),
        ];

        if ($type !== null) {
            $components[] = Group::make($type::form())->statePath('localized_content');

            if ($type::maxImages() > 0) {
                $components[] = Section::make('Images')
                    ->description($type::imagesHelp())
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('images')
                            ->hiddenLabel()
                            ->collection('images')
                            ->multiple($type::maxImages() > 1)
                            ->maxFiles($type::maxImages())
                            ->reorderable()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120),
                    ]);
            }
        }

        return $schema->columns(1)->components($components);
    }

    public function table(Table $table): Table
    {
        $registry = app(SectionTypeRegistry::class);

        return $table
            ->recordTitleAttribute('key')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->modifyQueryUsing(fn ($query) => $query->with('media'))
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->collection('images')
                    ->conversion('thumb')
                    ->imageHeight(36)
                    ->stacked()
                    ->limit(3),
                TextColumn::make('key')->weight('bold'),
                TextColumn::make('type')
                    ->formatStateUsing(function (string $state) use ($registry): string {
                        $type = $registry->get($state);

                        return $type !== null ? $type::label() : $state;
                    })
                    ->badge()
                    ->color('gray'),
                TextColumn::make('heading')
                    ->label('Heading')
                    ->state(fn (PageSection $record): ?string => $record->get('heading') ?? $record->get('slides.0.heading'))
                    ->limit(50),
                ToggleColumn::make('is_visible')->label('Visible'),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->modalWidth(Width::FourExtraLarge)
                    ->modalHeading(fn (PageSection $record): string => 'Edit section: '.$record->key),
                Action::make('preview')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('gray')
                    ->url(fn (): ?string => $this->previewUrl(), shouldOpenInNewTab: true)
                    ->visible(fn (): bool => $this->previewUrl() !== null),
            ]);
    }

    private function previewUrl(): ?string
    {
        $key = $this->getOwnerRecord()->key;
        $path = $key === 'home' ? '' : $key;

        return url(config('locales.default').'/'.$path);
    }
}
