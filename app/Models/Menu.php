<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /** @use HasFactory<\Database\Factories\MenuFactory> */
    use HasFactory;

    protected $fillable = [
        'name', // pastikan ini ada  
        'description',
        'price',
        'image', // jika ada kolom lain  
        'category',

    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
