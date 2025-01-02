<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'dining_table_id',
        'status',
        'quantity',
        'price',
    ];

    /**  
     * Relationship with Menu model.  
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**  
     * Relationship with DiningTable model.  
     */
    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class);
    }
}
