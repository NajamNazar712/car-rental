<?php

namespace App\Filament\Resources\MenuItems;

use App\Enums\MenuLocation;
use App\Filament\Concerns\SuperAdminOnly;
use App\Filament\Resources\MenuItems\Pages\ManageMenuItems;
use App\Models\MenuItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class MenuItemResource extends Resource
{
    /** Super admins only: kept in code, not offered to the client (static website content). */
    use SuperAdminOnly;

    protected static ?string $model = MenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static string|UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Menus';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('location')->options(MenuLocation::class)->default(MenuLocation::Header)->required(),
                TextInput::make('label')->required()->maxLength(50),
                TextInput::make('url')
                    ->label('Link')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('/fleet')
                    ->helperText('Internal pages: path without language, e.g. /fleet or /about. External: full URL.')
                    ->columnSpanFull(),
                Toggle::make('opens_new_tab')->label('Open in new tab'),
                Toggle::make('is_active')->label('Active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->defaultGroup('location')
            ->columns([
                TextColumn::make('label'),
                TextColumn::make('url')->label('Link'),
                TextColumn::make('location')->badge(),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->filters([
                SelectFilter::make('location')->options(MenuLocation::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMenuItems::route('/'),
        ];
    }
}
