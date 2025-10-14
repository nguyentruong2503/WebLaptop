<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code','des', 'discount_type', 'discount_value',
        'min_order_value', 'quantity', 'start_date',
        'end_date', 'is_active'
    ];
}
