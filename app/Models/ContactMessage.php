<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'message', 'is_read', 'ip_address'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
