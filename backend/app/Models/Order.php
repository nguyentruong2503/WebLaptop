<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
      protected $fillable = [
        'id',
        'userID',
        'totalAmount',
        'fullName',
          'phone',
        'address',
    ];
    public function details() {
    return $this->hasMany(Order_detail::class);
}
}
