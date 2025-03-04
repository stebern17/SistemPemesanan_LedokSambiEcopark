<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    /** @use HasFactory<\Database\Factories\DiningTableFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'status',
        'position',
    ];
}
