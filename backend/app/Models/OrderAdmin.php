<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAdmin extends Model
{
    use HasFactory;
    protected $table = 'orders'; // Đảm bảo đúng tên bảng đơn hàng thực tế
    protected $fillable = [
        'id',
        'userID',
        'totalAmount',
        'fullName',
        'phone',
        'address',
        'orderstatus',
        'created_at',
    ];
    protected $appends = ['status'];

    // Đảm bảo lấy đúng trường trạng thái
    public function getStatusAttribute() {
        return $this->orderstatus ?? '';
    }
}

