<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The only hardcoded role: vendor/maintenance tier, bypasses all permission checks.
     * Every other role is created and managed in Users & Access → Roles.
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Any role grants panel access (built-in or client-created); what users see is governed by permissions.
        return $this->is_active && $this->roles()->exists();
    }

    /** Vendor/maintenance tier. Hidden from client admins in Users and Roles. */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public static function currentIsSuperAdmin(): bool
    {
        $user = auth()->user();

        return $user instanceof self && $user->isSuperAdmin();
    }
}
