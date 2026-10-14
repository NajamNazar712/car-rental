<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Helpers for searching/sorting spatie translatable JSON columns in the current locale.
 */
final class Translatable
{
    public static function search(string $column): Closure
    {
        return function (Builder $query, string $search) use ($column): Builder {
            $path = $query->getQuery()->getGrammar()->wrap($column.'->'.app()->getLocale());

            return $query->whereRaw("LOWER({$path}) LIKE ?", ['%'.mb_strtolower($search).'%']);
        };
    }

    public static function sort(string $column): Closure
    {
        return fn (Builder $query, string $direction): Builder => $query->orderBy($column.'->'.app()->getLocale(), $direction);
    }
}
