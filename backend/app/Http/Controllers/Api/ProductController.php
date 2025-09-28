<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Laptop;
use App\Models\Accessory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products (only active ones).
     */
    public function index()
    {
        $products = Product::with(['type', 'branch', 'laptop', 'accessory'])
            ->where('isActive', 1)
            ->get();
        return response()->json($products);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'productName' => 'required|string|max:255',
            'id_type' => 'required|exists:product_types,id',
            'id_branch' => 'required|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'quality' => 'required|integer|min:0',
            'img' => 'nullable|string|max:255',
            'laptop' => 'nullable|array',
            'laptop.screenSpecs' => 'nullable|string|max:255',
            'laptop.CPU' => 'nullable|string|max:255',
            'laptop.RAM' => 'nullable|string|max:255',
            'laptop.SSD' => 'nullable|string|max:255',
            'laptop.GPU' => 'nullable|string|max:255',
            'laptop.des' => 'nullable|string',
            'accessory' => 'nullable|array',
            'accessory.des' => 'nullable|string'
        ]);

        $product = Product::create([
            'productName' => $data['productName'],
            'id_type' => $data['id_type'],
            'id_branch' => $data['id_branch'],
            'price' => $data['price'],
            'quality' => $data['quality'],
            'img' => $data['img'],
            'isActive' => 1 // Đảm bảo isActive mặc định là 1
        ]);

        if ($data['id_type'] == 1 && $data['laptop']) {
            Laptop::create(array_merge(['productID' => $product->id], $data['laptop']));
        } elseif ($data['id_type'] == 2 && $data['accessory']) {
            Accessory::create(array_merge(['productID' => $product->id], $data['accessory']));
        }

        return response()->json($product->load(['type', 'branch', 'laptop', 'accessory']), 201);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with(['type', 'branch', 'laptop', 'accessory'])
            ->where('isActive', 1)
            ->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validate([
            'productName' => 'required|string|max:255',
            'id_type' => 'required|exists:product_types,id',
            'id_branch' => 'required|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'quality' => 'required|integer|min:0',
            'img' => 'nullable|string|max:255',
            'isActive' => 'nullable|boolean', // Thêm xác thực cho isActive
            'laptop' => 'nullable|array',
            'laptop.screenSpecs' => 'nullable|string|max:255',
            'laptop.CPU' => 'nullable|string|max:255',
            'laptop.RAM' => 'nullable|string|max:255',
            'laptop.SSD' => 'nullable|string|max:255',
            'laptop.GPU' => 'nullable|string|max:255',
            'laptop.des' => 'nullable|string',
            'accessory' => 'nullable|array',
            'accessory.des' => 'nullable|string'
        ]);

        $product->update([
            'productName' => $data['productName'],
            'id_type' => $data['id_type'],
            'id_branch' => $data['id_branch'],
            'price' => $data['price'],
            'quality' => $data['quality'],
            'img' => $data['img'],
            'isActive' => isset($data['isActive']) ? $data['isActive'] : $product->isActive // Chỉ cập nhật nếu isActive được gửi
        ]);

        if ($data['id_type'] == 1 && $data['laptop']) {
            Laptop::updateOrCreate(
                ['productID' => $product->id],
                $data['laptop']
            );
            Accessory::where('productID', $product->id)->delete();
        } elseif ($data['id_type'] == 2 && $data['accessory']) {
            Accessory::updateOrCreate(
                ['productID' => $product->id],
                $data['accessory']
            );
            Laptop::where('productID', $product->id)->delete();
        } else {
            // Xóa cả laptop và accessory nếu id_type không phải 4 hoặc 5
            Laptop::where('productID', $product->id)->delete();
            Accessory::where('productID', $product->id)->delete();
        }

        return response()->json($product->load(['type', 'branch', 'laptop', 'accessory']));
    }

    /**
     * Soft delete the specified product by setting isActive to 0.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['isActive' => 0]);
        return response()->json(['message' => 'Sản phẩm đã được ẩn thành công']);
    }
}   