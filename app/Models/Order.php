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
        'unique_id'
    ];

    protected $appends = [
        'total_amount',
    ];

    protected static function booted()
    {
        static::updated(function ($order) {
            $table = DiningTable::find($order->dining_table_id);
            if ($table) {
                // Tentukan status baru berdasarkan status order
                $newStatus = ($order->status === 'waiting') ? 'unavailable' : 'available';

                // Perbarui status dining table
                $table->update(['status' => $newStatus]);
            }
        });
    }

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

    public static function boot()
    {
        parent::boot();

        static::creating(function (Order $order) {
            $order->unique_id = 'ORD' . str_pad(uniqid(), 5, '0', STR_PAD_LEFT);
        });
    }
}
