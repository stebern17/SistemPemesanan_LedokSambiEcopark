<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_detail_id',
        'method',
        'amount',
        'status',
    ];

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }
}
