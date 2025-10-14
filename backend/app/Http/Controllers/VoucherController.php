<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherController extends Controller
{

    public function index()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $vouchers
        ]);
    }

    public function show($id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy voucher'], 404);
        }

        return response()->json(['status' => true, 'data' => $voucher]);
    }

    public function findByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = trim($request->input('code'));

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy mã voucher.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Tìm thấy voucher.',
            'data' => $voucher
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'des' => 'nullable|string',
            'discount_type' => 'required|in:percent,amount',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $voucher = Voucher::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Tạo voucher thành công',
            'data' => $voucher
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy voucher'
            ], 404);
        }

        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $id,
            'des' => 'nullable|string',
            'discount_type' => 'required|in:percent,amount',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $voucher->code = $request->code;
        $voucher->des = $request->des;
        $voucher->discount_type = $request->discount_type;
        $voucher->discount_value = $request->discount_value;
        $voucher->min_order_value = $request->min_order_value;
        $voucher->quantity = $request->quantity;
        $voucher->start_date = $request->start_date;
        $voucher->end_date = $request->end_date;
        $voucher->is_active = true;

        $voucher->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật voucher thành công',
            'data' => $voucher
        ]);
    }

    public function destroy($id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy voucher'], 404);
        }

        $voucher->delete();

        return response()->json(['status' => true, 'message' => 'Xóa voucher thành công']);
    }

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
