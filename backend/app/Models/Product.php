<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    /**
     * Các trường có thể được mass-assigned.
     *
     * @var array<string>
     */
    protected $fillable = [
        'productName',
        'id_type',
        'id_branch',
        'price',
        'quality',
        'img',
        'isActive',
    ];

    /**
     * Quan hệ với ProductType (một sản phẩm thuộc một loại).
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Product_Types::class, 'id_type', 'id');
    }

    /**
     * Quan hệ với Brand (một sản phẩm thuộc một nhãn hàng).
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'id_branch', 'id');
    }

    /**
     * Quan hệ với Laptop (một sản phẩm có thể có một laptop).
     */
    public function laptop(): HasOne
    {
        return $this->hasOne(Laptop::class, 'productID', 'id');
    }

    /**
     * Quan hệ với Accessory (một sản phẩm có thể có một phụ kiện).
     */
    public function accessory(): HasOne
    {
        return $this->hasOne(Accessory::class, 'productID', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_product');
    }
//promotion
public function promotions() {
    return $this->belongsToMany(Promotion::class, 'promotion_product');
}
public function getDiscountedPrice()
{
    $now = now();

    // Lấy tất cả khuyến mãi hợp lệ
    $allPromotions = $this->promotions()
        ->where('is_active', true)
        ->whereDate('start_date','<=',$now)
        ->whereDate('end_date','>=',$now)
        ->get();

    $brandPromotion = Promotion::where('applied_type','brand')
        ->where('brand_id', $this->id_branch)
        ->where('is_active', true)
        ->whereDate('start_date','<=',$now)
        ->whereDate('end_date','>=',$now)
        ->get();

    $categoryPromotion = Promotion::where('applied_type','category')
        ->where('category_id', $this->id_type)
        ->where('is_active', true)
        ->whereDate('start_date','<=',$now)
        ->whereDate('end_date','>=',$now)
        ->get();

    $globalPromotion = Promotion::where('applied_type','global')
        ->where('is_active', true)
        ->whereDate('start_date','<=',$now)
        ->whereDate('end_date','>=',$now)
        ->get();

    $allPromotions = $allPromotions
        ->merge($brandPromotion)
        ->merge($categoryPromotion)
        ->merge($globalPromotion);

    if($allPromotions->isEmpty()) return $this->price;

    $bestPrice = $this->price;

    foreach($allPromotions as $promo){
        $discounted = $this->price;

        if($promo->discount_percent !== null){
            $discounted = $this->price * (1 - $promo->discount_percent / 100);
        }

        if($promo->discount_amount !== null){
            $discounted = max(0, $discounted - $promo->discount_amount);
        }

        if($discounted < $bestPrice){
            $bestPrice = $discounted;
        }
    }

    return round($bestPrice, 0);
}
    


}