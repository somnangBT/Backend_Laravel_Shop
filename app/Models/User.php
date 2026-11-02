<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a cashier
     */
    public function isCashier(): bool
    {
        return in_array($this->role, ['cashier', 'admin']);
    }

    /**
     * Check if user is a kitchen chef/staff
     */
    public function isKitchen(): bool
    {
        return in_array($this->role, ['kitchen', 'admin']);
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasRole(...$roles): bool
    {
        // Admin always has access to all roles
        if ($this->role === 'admin') {
            return true;
        }

        if (is_array($roles[0] ?? null)) {
            $roles = $roles[0];
        }

        return in_array($this->role, $roles);
    }

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}