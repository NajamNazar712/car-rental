<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasLocalizedTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Faq extends Model
{
    use HasLocalizedTranslations;

    /** @var list<string> */
    public array $translatable = ['question', 'answer', 'group'];

    protected $fillable = ['question', 'answer', 'group', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
