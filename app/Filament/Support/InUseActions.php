<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Exceptions\ModelInUseException;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Delete actions for lookup records (category/brand/model) that are blocked while used by cars.
 * The model-level `deleting` guard is the source of truth; these only hide/skip in the UI.
 */
final class InUseActions
{
    public static function delete(): DeleteAction
    {
        return DeleteAction::make()
            ->hidden(fn (Model $record): bool => $record->isUsedByCars());
    }

    public static function bulkDelete(): BulkAction
    {
        return BulkAction::make('deleteUnused')
            ->label(__('filament-actions::delete.multiple.label'))
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->requiresConfirmation()
            ->authorize('deleteAny')
            ->action(function (Collection $records): void {
                $skipped = 0;
                foreach ($records as $record) {
                    try {
                        $record->delete();
                    } catch (ModelInUseException) {
                        $skipped++;
                    }
                }

                $skipped > 0
                    ? Notification::make()->warning()->title(__('admin.in_use_skipped', ['count' => $skipped]))->send()
                    : Notification::make()->success()->title(__('filament-actions::delete.multiple.notifications.deleted.title'))->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
