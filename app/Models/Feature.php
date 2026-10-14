<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasLocalizedTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Feature extends Model
{
    use HasLocalizedTranslations;

    /** @var list<string> */
    public array $translatable = ['name'];

    protected $fillable = ['name', 'icon', 'sort_order'];

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
