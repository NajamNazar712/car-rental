<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Cars\CarResource;
use App\Filament\Widgets\Concerns\RequiresFleetAccess;
use App\Models\Car;
use App\Support\Money;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

final class RecentCarsTable extends TableWidget
{
    use RequiresFleetAccess;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('admin.dashboard.recent_cars'))
            ->query(fn () => Car::query()->with(['brand', 'category', 'media'])->latest('updated_at')->limit(5))
            ->paginated(false)
            ->columns([
                SpatieMediaLibraryImageColumn::make('main')
                    ->label('')
                    ->collection('main')
                    ->conversion('thumb')
                    ->imageWidth(72)
                    ->imageHeight(46),
                TextColumn::make('name')
                    ->label(__('admin.dashboard.car'))
                    ->weight('semibold')
                    ->description(fn (Car $record): string => trim($record->brand?->name.' · '.$record->year, ' ·')),
                TextColumn::make('category.name')->label(__('admin.dashboard.category'))->badge()->color('gray'),
                TextColumn::make('price_day')
                    ->label(__('admin.dashboard.price_day'))
                    ->formatStateUsing(fn (Car $record): string => Money::format($record->effectiveDayPrice())),
                TextColumn::make('status')->badge(),
                TextColumn::make('updated_at')->label(__('admin.dashboard.updated'))->since(),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label(__('admin.dashboard.edit'))
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (Car $record): string => CarResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (Car $record): bool => CarResource::canEdit($record)),
            ])
            ->recordUrl(fn (Car $record): ?string => CarResource::canView($record) ? CarResource::getUrl('view', ['record' => $record]) : null);
    }
}
