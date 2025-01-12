<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'dining_table_id',
        'status',
    ];


    /**  
     * Relationship with Menu model.  
     */
    public function items()
    {
        return $this->hasMany(OrderDetail::class);
    }



    /**  
     * Relationship with DiningTable model.  
     */
    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class);
    }
}
