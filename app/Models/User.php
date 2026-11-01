<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Peran;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var array<string, mixed> */
    protected $attributes = ['peran' => 'pengguna'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'peran' => Peran::class,
        ];
    }

    // `peran` sengaja tidak fillable: hanya diubah lewat admin (Akun) atau `php artisan akun:peran`.
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->peran->bisaMasukAdmin();
    }

    public function superadmin(): bool
    {
        return $this->peran === Peran::Superadmin;
    }

    /** @return HasOne<Langganan, $this> */
    public function langganan(): HasOne
    {
        return $this->hasOne(Langganan::class);
    }

    public function berlangganan(): bool
    {
        return (bool) $this->langganan?->aktif();
    }

    /** @return HasMany<ProgresBelajar, $this> */
    public function progresBelajar(): HasMany
    {
        return $this->hasMany(ProgresBelajar::class);
    }
}
