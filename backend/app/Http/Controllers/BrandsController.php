<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Brands;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Brands::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nameOfBranch', 'like', "%$search%");
        }
        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nameOfBranch' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
        $brand = Brands::create($validated);
        return response()->json($brand, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $brand = Brands::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Không tìm thấy nhãn hàng'], 404);
        }
        return response()->json($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $brand = Brands::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Không tìm thấy nhãn hàng'], 404);
        }
        $validated = $request->validate([
            'nameOfBranch' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
        $brand->update($validated);
        return response()->json($brand);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brand = Brands::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Không tìm thấy nhãn hàng'], 404);
        }
        try {
        $products = $brand->products;

        foreach ($products as $product) {
            Cart::where('productID', $product->id)->delete();
            $product->isActive = 0;
            $product->id_branch = null; // Đúng tên trường khóa ngoại
            $product->save();
        }
            $brand->delete();
            return response()->json(['message' => 'Xóa thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Không thể xóa nhãn hàng vì có sản phẩm liên quan'], 400);
        }
    }
}

