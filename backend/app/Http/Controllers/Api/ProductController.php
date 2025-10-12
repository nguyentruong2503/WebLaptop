<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Laptop;
use App\Models\Accessory;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LaptopsImport;


class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with(['type', 'branch', 'laptop', 'accessory'])
            ->where('isActive', 1)
            ->get();
        return response()->json($products);
    }

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
            'laptop.GPU_type' => 'nullable|in:Tích hợp,Rời',
            'laptop.expandable_slots' => 'nullable|string|max:255',
            'laptop.battery_capacity_wh' => 'nullable|integer',
            'laptop.charging_watt' => 'nullable|integer',
            'laptop.USB_A_ports' => 'nullable|integer',
            'laptop.USB_C_ports' => 'nullable|integer',
            'laptop.HDMI_ports' => 'nullable|integer',
            'laptop.LAN_port' => 'nullable|boolean',
            'laptop.Thunderbolt_ports' => 'nullable|integer',
            'laptop.jack_3_5mm' => 'nullable|boolean',
            'laptop.special_features' => 'nullable|string',
            'laptop.dimensions' => 'nullable|string|max:50',
            'laptop.weight_kg' => 'nullable|numeric',

            'accessory' => 'nullable|array',
            'accessory.des' => 'nullable|string'
        ]);

        $product = Product::create([
            'productName' => $data['productName'],
            'id_type' => $data['id_type'],
            'id_branch' => $data['id_branch'],
            'price' => $data['price'],
            'quality' => $data['quality'],
            'img' => $data['img'] ?? null,
            'isActive' => 1
        ]);

        if ($data['id_type'] == 1 && !empty($data['laptop'])) {
            Laptop::create(array_merge(['productID' => $product->id], $data['laptop']));
        } elseif ($data['id_type'] == 2 && !empty($data['accessory'])) {
            Accessory::create(array_merge(['productID' => $product->id], $data['accessory']));
        }

        return response()->json($product->load(['type', 'branch', 'laptop', 'accessory']), 201);
    }

    public function show($id)
    {
        $product = Product::with(['type', 'branch', 'laptop', 'accessory'])
            ->where('isActive', 1)
            ->findOrFail($id);
        return response()->json($product);
    }

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
            'isActive' => 'nullable|boolean',

            'laptop' => 'nullable|array',
            'laptop.screenSpecs' => 'nullable|string|max:255',
            'laptop.CPU' => 'nullable|string|max:255',
            'laptop.RAM' => 'nullable|string|max:255',
            'laptop.SSD' => 'nullable|string|max:255',
            'laptop.GPU' => 'nullable|string|max:255',
            'laptop.des' => 'nullable|string',
            'laptop.GPU_type' => 'nullable|in:Tích hợp,Rời',
            'laptop.expandable_slots' => 'nullable|string|max:255',
            'laptop.battery_capacity_wh' => 'nullable|integer',
            'laptop.charging_watt' => 'nullable|integer',
            'laptop.USB_A_ports' => 'nullable|integer',
            'laptop.USB_C_ports' => 'nullable|integer',
            'laptop.HDMI_ports' => 'nullable|integer',
            'laptop.LAN_port' => 'nullable|boolean',
            'laptop.Thunderbolt_ports' => 'nullable|integer',
            'laptop.jack_3_5mm' => 'nullable|boolean',
            'laptop.special_features' => 'nullable|string',
            'laptop.dimensions' => 'nullable|string|max:50',
            'laptop.weight_kg' => 'nullable|numeric',

            'accessory' => 'nullable|array',
            'accessory.des' => 'nullable|string'
        ]);

        $product->update([
            'productName' => $data['productName'],
            'id_type' => $data['id_type'],
            'id_branch' => $data['id_branch'],
            'price' => $data['price'],
            'quality' => $data['quality'],
            'img' => $data['img'] ?? $product->img,
            'isActive' => $data['isActive'] ?? $product->isActive
        ]);

        if ($data['id_type'] == 1 && !empty($data['laptop'])) {
            Laptop::updateOrCreate(['productID' => $product->id], $data['laptop']);
            Accessory::where('productID', $product->id)->delete();
        } elseif ($data['id_type'] == 2 && !empty($data['accessory'])) {
            Accessory::updateOrCreate(['productID' => $product->id], $data['accessory']);
            Laptop::where('productID', $product->id)->delete();
        } else {
            Laptop::where('productID', $product->id)->delete();
            Accessory::where('productID', $product->id)->delete();
        }

        return response()->json($product->load(['type', 'branch', 'laptop', 'accessory']));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['isActive' => 0]);
        return response()->json(['message' => 'Sản phẩm đã được ẩn thành công']);
    }

    public function getSpecLaptopByID($id)
    {
        $product = Product::with('laptop')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }

        if (!$product->laptop) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm này không phải laptop hoặc chưa có thông số laptop',
            ], 404);
        }

        $laptopData = $product->laptop->toArray();
        $laptopData['productName'] = $product->productName;
        $laptopData['img'] = $product->img ?? null; 

        return response()->json([
            'success' => true,
            'data' => $laptopData,
        ]);
    }

    public function import(Request $request)
    {
        try {
            // Kiểm tra có file được gửi lên không
            if (!$request->hasFile('file')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có file nào được tải lên'
                ], 400);
            }

            $file = $request->file('file');

            // Kiểm tra định dạng file hợp lệ
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['xls', 'xlsx'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'File không đúng định dạng Excel (.xls hoặc .xlsx)'
                ], 400);
            }

            // Gọi Import class (đã tạo riêng để xử lý)
            Excel::import(new LaptopsImport, $file);

            return response()->json([
                'status' => true,
                'message' => 'Import dữ liệu laptop thành công!'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi import: ' . $e->getMessage()
            ], 500);
        }
    }
}
