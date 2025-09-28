<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    use HasFactory;
    protected $fillable = [
    'productID', // Thêm trường này
    'screenSpecs',
    'CPU',
    'RAM',
    'SSD',
    'GPU',
    'des'
];
    public function product()
{
    return $this->belongsTo(Product::class, 'productID', 'id');
}

}
