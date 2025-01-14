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
        'is_paid',
    ];

    protected $appends = [
        'total_amount',
    ];

    public function getTotalAmountAttribute()
    {
        return $this->items->sum('total_amount');
    }

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
