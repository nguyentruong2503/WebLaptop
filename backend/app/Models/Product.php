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
}