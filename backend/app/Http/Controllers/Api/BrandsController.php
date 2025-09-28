<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Brand::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nameOfBranch' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $brand = Brand::create([
            'nameOfBranch' => $request->nameOfBranch,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
        ]);

        return response()->json($brand, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Không tìm thấy nhãn hàng'], 404);
        }
        return $brand;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Không tìm thấy nhãn hàng'], 404);
        }

        $request->validate([
            'nameOfBranch' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $brand->update([
            'nameOfBranch' => $request->nameOfBranch,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
        ]);

        return response()->json($brand);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Không tìm thấy nhãn hàng'], 404);
        }

        try {
            $brand->delete();
            return response()->json(['message' => 'Xóa thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Không thể xóa nhãn hàng vì có sản phẩm liên quan'], 400);
        }
    }
}