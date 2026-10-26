<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use UnitEnum;

/**
 * Admin users (super_admin only, via Shield permissions). Inactive users cannot log in
 * (see User::canAccessPanel). Users cannot deactivate or delete themselves.
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Users & Access';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->required()->maxLength(100),
                TextInput::make('email')->email()->required()->maxLength(150)->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->rule(Password::min(8)->letters()->numbers())
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Leave empty to keep the current password.' : null),
                Select::make('roles')
                    ->relationship('roles', 'name', fn (Builder $query) => static::assignableRoles($query))
                    ->getOptionLabelFromRecordUsing(fn ($record): string => str($record->name)->replace('_', ' ')->title())
                    ->multiple()
                    ->maxItems(1)
                    ->label('Role')
                    ->preload()
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active (can log in)')
                    ->default(true)
                    ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('roles'))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->disabled(fn (User $record): bool => $record->is(auth()->user())),
                TextColumn::make('created_at')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name', fn (Builder $query) => static::assignableRoles($query))
                    ->preload(),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (User $record): bool => $record->is(auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }

    /** Client admins never see super admin accounts (so they cannot edit, disable or delete them). */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! User::currentIsSuperAdmin()) {
            $query->whereDoesntHave('roles', fn (Builder $q) => $q->where('name', User::ROLE_SUPER_ADMIN));
        }

        return $query;
    }

    /** All roles (including client-created ones); super_admin only for super admins. */
    private static function assignableRoles(Builder $query): Builder
    {
        return User::currentIsSuperAdmin()
            ? $query
            : $query->where('name', '!=', User::ROLE_SUPER_ADMIN);
    }
}
