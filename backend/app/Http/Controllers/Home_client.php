<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Laptop;
use App\Models\Accessory;
use Illuminate\Http\Request;

class Home_client extends Controller
{
    /**
     * 🧠 Lấy tất cả sản phẩm là Laptop (có quan hệ laptop)
     */
    public function getByLoai()
    {
        $laptops = Product::has('laptop')
            ->with(['laptop'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'productName' => $product->productName,
                    'img' => $product->img,
                    'isActive' => $product->isActive,
                    'price' => (float)$product->price,
                    'discounted_price' => (float)$product->getDiscountedPrice(),
                    'laptop' => $product->laptop,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $laptops
        ]);
    }

    /**
     * 🔌 Lấy tất cả sản phẩm là Phụ kiện (có quan hệ accessory)
     */
    public function getAccessory()
    {
        $accessories = Product::has('accessory')
            ->with(['accessory'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'productName' => $product->productName,
                    'img' => $product->img,
                    'isActive' => $product->isActive,
                    'price' => (float)$product->price,
                    'discounted_price' => (float)$product->getDiscountedPrice(),
                    'accessory' => $product->accessory,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $accessories
        ]);
    }

    /**
     * 💻 Lấy chi tiết Laptop theo ID
     */
    public function getLaptopById($id)
    {
        
        $laptop = Laptop::with('product')->where('productID', $id)->first();
         $laptop->product->discounted_price = (float)$laptop->product->getDiscountedPrice();

        if (!$laptop) {
            return response()->json([
                'status' => false,
                'message' => 'Laptop không tồn tại',
            ], 404);
        }

        $product = $laptop->product;
        $laptop->product->discounted_price = $product->getDiscountedPrice();

        return response()->json([
            'status' => true,
            'data' => $laptop,
        ]);
    }

    /**
     * 🧩 Lấy chi tiết Phụ kiện theo ID
     */
    public function getAccessoryById($id)
    {
        $accessory = Accessory::with('product')->where('productID', $id)->first();

        if (!$accessory) {
            return response()->json([
                'status' => false,
                'message' => 'Phụ kiện không tồn tại',
            ], 404);
        }

        $product = $accessory->product;
        $accessory->product->discounted_price = $product->getDiscountedPrice();

        return response()->json([
            'status' => true,
            'data' => $accessory,
        ]);
    }

    /**
     * 🛍️ (Tùy chọn thêm) - Lấy tất cả sản phẩm (Laptop + Phụ kiện)
     */
    public function getAllProducts()
    {
        $products = Product::with(['laptop', 'accessory'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'productName' => $product->productName,
                    'img' => $product->img,
                    'isActive' => $product->isActive,
                    'price' => (float)$product->price,
                    'discounted_price' => (float)$product->getDiscountedPrice(),
                    'type' => $product->laptop ? 'laptop' : ($product->accessory ? 'accessory' : 'other'),
                    'detail' => $product->laptop ?? $product->accessory,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }
}
