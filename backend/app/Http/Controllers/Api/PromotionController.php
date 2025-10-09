<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Product;

class PromotionController extends Controller
{
    // --- Danh sách ---
    public function index()
    {
        $promotions = Promotion::with('products','category','brand')->get();
        return response()->json($promotions);
    }

    // --- Chi tiết ---
    public function show(Promotion $promotion)
    {
        $promotion->load('products','category','brand');
        return response()->json($promotion);
    }

    // --- Tạo ---
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'applied_type'=>'required|in:product,category,brand,global',
            'category_id'=>'nullable|exists:product_types,id',
            'brand_id'=>'nullable|exists:brands,id',
            'discount_percent'=>'nullable|numeric|min:0|max:100',
            'discount_amount'=>'nullable|numeric|min:0',
            'start_date'=>'nullable|date',
            'end_date'=>'nullable|date|after_or_equal:start_date',
            'is_active'=>'nullable|boolean',
            'products'=>'nullable|array',
            'products.*'=>'exists:products,id'
        ]);

        $promotion = Promotion::create($data);

        if($request->applied_type==='product' && !empty($request->products)){
            $promotion->products()->attach($request->products);
        }

        return response()->json(['message'=>'Tạo khuyến mãi thành công','promotion'=>$promotion],201);
    }

    // --- Cập nhật ---
    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'name'=>'sometimes|required|string|max:255',
            'applied_type'=>'sometimes|required|in:product,category,brand,global',
            'category_id'=>'nullable|exists:product_types,id',
            'brand_id'=>'nullable|exists:brands,id',
            'discount_percent'=>'nullable|numeric|min:0|max:100',
            'discount_amount'=>'nullable|numeric|min:0',
            'start_date'=>'nullable|date',
            'end_date'=>'nullable|date|after_or_equal:start_date',
            'is_active'=>'nullable|boolean',
            'products'=>'nullable|array',
            'products.*'=>'exists:products,id'
        ]);

        $promotion->update($data);

        if($request->applied_type==='product'){
            $promotion->products()->sync($request->products ?? []);
        } else {
            $promotion->products()->detach();
        }

        return response()->json(['message'=>'Cập nhật thành công','promotion'=>$promotion]);
    }

    // --- Xóa ---
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return response()->json(['message'=>'Xóa khuyến mãi thành công']);
    }
}
