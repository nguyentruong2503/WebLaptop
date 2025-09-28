<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Laptop;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class Home_client extends Controller
{
    //
    public function getByLoai()
    {
        $laptops = Laptop::with('product')->get();


        return response()->json([
            'status' => true,
            'data' => $laptops
        ]);
    }
    public function getAccessory()
    {
        $laptops = Accessory::with('product')->get();


        return response()->json([
            'status' => true,
            'data' => $laptops
        ]);
    }
    public function getLaptopById($id)
    {
    $laptop = Laptop::with('product')->where('productID', $id)->first();

        if (!$laptop) {
            return response()->json([
                'status' => false,
                'message' => 'Laptop không tồn tại'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $laptop
        ]);
    }
    public function getAccessoryById($id)
    {
    $laptop = Accessory::with('product')->where('productID', $id)->first();

        if (!$laptop) {
            return response()->json([
                'status' => false,
                'message' => 'Laptop không tồn tại'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $laptop
        ]);
    }
}
