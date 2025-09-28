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
        'orderstatus', 

    ];

    public static function revenue($month, $year)
    {
        return self::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('orderstatus', '!=', 'Cancelled')
            ->sum('totalAmount');
    }

    public static function orderCount($month, $year)
    {
        return self::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();
    }

    public static function cancelCount($month, $year)
    {
        return self::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('orderstatus', 'Cancelled')
            ->count();
    }

    public static function statusCount($status)
    {
        return self::where('orderstatus', $status)->count();
    }

    public function details() {
    return $this->hasMany(Order_detail::class, 'orderID');
}
}
