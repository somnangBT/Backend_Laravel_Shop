<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'pro_id';

    protected $fillable = [
        'pro_code',
        'pro_name',
        'category_id',
        'qty',
        'price',
        'discounted_price', // បន្ថែមចំណុចនេះ ដើម្បីឱ្យអាចរក្សាទុកតម្លៃបញ្ចុះតម្លៃបាន
        'description',
        'discount',
        'image',
    ];

    // កែសម្រួល Accessor នេះបន្តិច (ករណីចង់ហៅទាញមកប្រើបន្ថែមនៅកន្លែងផ្សេង)
    public function getDiscountedPriceAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        return $this->price * (1 - ($this->discount ?? 0) / 100);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'cat_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'pro_id');
    }
}