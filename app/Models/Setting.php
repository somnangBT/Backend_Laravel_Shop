<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'dark_mode',
        'compact_mode',
        'color_theme',
    ];
}
