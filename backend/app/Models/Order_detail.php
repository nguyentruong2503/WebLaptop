<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_detail extends Model
{
    use HasFactory;
        protected $fillable = [
        'orderID',
        'productID',
        'quantity',
          'price',
       
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'productID');
    }
}
