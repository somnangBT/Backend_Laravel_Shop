<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'promo_code',
        'notes',
        'status',
        'email',
        'payment_method',
        'payment_status',
        'shipping_address',
        'phone',
    ];

    /**
     * Get the user who placed the order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Scope for user's orders
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}