<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Product_Types;
use Illuminate\Http\Request;

class ProductTypesController extends Controller
{
    /**
     * 
     */
    public function index(Request $request)
    {

        $query = Product_types::query();

        if ($request->has('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('typeName', 'like', '%' . $keyword . '%');
        }

         return response()->json($query->paginate(5));

    }

    /**
     * 
     */
    public function store(Request $request)
    {
        $request->validate([
            'typeName' => 'required|unique:product_types,typeName',
        ]);

        $loai = Product_Types::create([
            'typeName' => $request->typeName,
        ]);

        return response()->json($loai, 201);
    }

    /**
     * 
     */
    public function show($id)
    {
        $loai = Product_Types::find($id);
        if (!$loai) {
            return response()->json(['message' => 'Không tìm thấy loại sản phẩm'], 404);
        }
        return $loai;
    }

    /**
     * 
     */
    public function update(Request $request, $id)
    {
        $loai = Product_Types::find($id);
        if (!$loai) {
            return response()->json(['message' => 'Không tìm thấy loại sản phẩm'], 404);
        }

        $request->validate([
            'typeName' => 'required|string|max:255',
        ]);

        $loai->typeName = $request->typeName;
        $loai->save();

        return response()->json($loai);
    }

    /**
     * .
     */
    public function destroy($id)
    {
        $loai = Product_Types::find($id);
        if (!$loai) {
            return response()->json(['message' => 'Không tìm thấy loại sản phẩm'], 404);
        }

        // Lấy tất cả sản phẩm thuộc loại sản phẩm này
        $products = $loai->products;

        foreach ($products as $product) {
            Cart::where('productID', $product->id)->delete();

            $product->isActive = 0;
            $product->save();
        }

        $loai->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }

}