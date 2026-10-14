<?php

namespace App\Filament\Resources\Cars\Tables;

use App\Enums\CarStatus;
use App\Filament\Support\Translatable;
use App\Models\Car;
use App\Models\Category;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('main')
                    ->label('')
                    ->collection('main')
                    ->conversion('thumb')
                    ->imageWidth(80)
                    ->imageHeight(50),
                TextColumn::make('name')
                    ->searchable(query: Translatable::search('name'))
                    ->description(fn (Car $record): string => (string) $record->year),
                TextColumn::make('brand.name')->sortable()->searchable(),
                TextColumn::make('carModel.name')->label('Model')->searchable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('price_day')->label('Price / day')->numeric(2)->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                ToggleColumn::make('is_featured')->label('Featured'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn (): array => Category::query()->ordered()->get()->pluck('name', 'id')->all()),
                SelectFilter::make('brand')->relationship('brand', 'name'),
                SelectFilter::make('status')->options(CarStatus::class),
                TernaryFilter::make('is_featured')->label('Featured'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ReplicateAction::make()
                    ->label('Duplicate car')
                    ->excludeAttributes(['slug', 'is_featured'])
                    ->beforeReplicaSaved(function (Car $replica, Car $record): void {
                        $replica->slug = Str::slug($record->slug.'-copy-'.Str::lower(Str::random(4)));
                        $replica->status = CarStatus::Inactive;
                        $replica->is_featured = false;
                    })
                    ->after(function (Car $replica, Car $record): void {
                        $replica->features()->sync($record->features()->pluck('features.id'));
                        foreach ($record->getMedia('main') as $media) {
                            $media->copy($replica, 'main');
                        }
                        foreach ($record->getMedia('gallery') as $media) {
                            $media->copy($replica, 'gallery');
                        }
                    })
                    ->successRedirectUrl(fn (Car $replica): string => route('filament.admin.resources.cars.edit', $replica)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Activate')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->authorizeIndividualRecords('update')
                        ->action(fn (Collection $records) => $records->each->update(['status' => CarStatus::Active]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->authorizeIndividualRecords('update')
                        ->action(fn (Collection $records) => $records->each->update(['status' => CarStatus::Inactive]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
