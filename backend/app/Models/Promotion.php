<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','applied_type','category_id','brand_id','discount_percent','discount_amount','start_date','end_date','is_active'
    ];

    public function products() {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }

    public function category() {
        return $this->belongsTo(Product_Types::class, 'category_id');
    }

    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
