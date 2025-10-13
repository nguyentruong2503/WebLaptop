<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function check(Request $request)
    {
        $code = trim($request->input('code'));
        $total = (float) $request->input('total');

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json(['status' => false, 'message' => 'Mã không tồn tại']);
        }

        if (!$voucher->is_active || $voucher->quantity <= 0) {
            return response()->json(['status' => false, 'message' => 'Mã đã hết hạn hoặc không còn hiệu lực']);
        }

        $now = Carbon::now();
        if (($voucher->start_date && $now->lt($voucher->start_date)) ||
            ($voucher->end_date && $now->gt($voucher->end_date))) {
            return response()->json(['status' => false, 'message' => 'Mã đã hết hạn thời gian']);
        }

        if ($total < $voucher->min_order_value) {
            return response()->json(['status' => false, 'message' => 'Chưa đạt giá trị tối thiểu để áp dụng mã']);
        }

        $discount = $voucher->discount_type === 'percent'
            ? $total * ($voucher->discount_value / 100)
            : $voucher->discount_value;

        $final_total = max(0, $total - $discount);

        return response()->json([
        'status' => true,
        'discount' => $discount,
        'discount_type' => $voucher->discount_type,
        'discount_value' => $voucher->discount_value,
        'final_total' => $final_total,
        'message' => 'Áp dụng mã thành công!'
    ]);

    }
}
