<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity',
        'user_id',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'user_id' => 'integer',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}