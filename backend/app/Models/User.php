<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // HasApiTokens = syarat Laravel Sanctum (BAB III 3.4.7)
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // satu user punya banyak alamat (one to many)
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    // satu user punya banyak pesanan
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // alamat "Anter ke" yang sedang dipilih
    public function defaultAddress(): ?Address
    {
        return $this->addresses()->where('is_default', true)->first()
            ?? $this->addresses()->orderBy('address_id')->first();
    }
}
