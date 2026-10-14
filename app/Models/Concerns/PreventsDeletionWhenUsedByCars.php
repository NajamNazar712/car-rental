<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Exceptions\ModelInUseException;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Blocks deleting a lookup record (category, brand, model) while any car (including trashed) uses it.
 * Requires a cars(): HasMany relation.
 */
trait PreventsDeletionWhenUsedByCars
{
    public static function bootPreventsDeletionWhenUsedByCars(): void
    {
        static::deleting(function (self $model): void {
            if ($model->isUsedByCars()) {
                throw new ModelInUseException(__('admin.in_use_cannot_delete'));
            }
        });
    }

    public function isUsedByCars(): bool
    {
        /** @var HasMany $relation */
        $relation = $this->cars();

        return $relation->withTrashed()->exists();
    }
}
