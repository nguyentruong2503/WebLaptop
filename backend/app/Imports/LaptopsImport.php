<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Laptop;
use App\Models\Brand;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaptopsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Bỏ dòng tiêu đề
        $rows->shift();

        foreach ($rows as $row) {

            // Bỏ qua dòng trống
            if (empty($row[2])) continue;

            DB::transaction(function () use ($row) {
                try {
                    $brand = Brand::firstOrCreate(['nameOfBranch' => trim($row[1])]);
                    $product = Product::where('productName', trim($row[2]))
                        ->where('id_branch', $brand->id)
                        ->first();

                    $price = (float) str_replace([','], '', $row[21]) ?: 0;
                    $quantity = (int) ($row[22] ?? 0);


                    if ($product) {
                        $product->update([
                            'price' => $price, // cập nhật lại giá mới
                            'quality' => $product->quality + $quantity, // cộng dồn số lượng
                        ]);
                    }
                    else {
                        $product = Product::create([
                            'productName' => trim($row[2]),
                            'id_type' => 1, 
                            'id_branch' => $brand->id,
                            'price' => $price,
                            'quality' => $quantity,
                            'isActive' => 1,
                        ]);
                    }

                    $laptop = Laptop::where('productID', $product->id)->first();

                    $data = [
                        'CPU' => $row[3] ?? null,
                        'RAM' => $row[4] ?? null,
                        'GPU_type' => $row[5] ?? null,
                        'GPU' => $row[6] ?? null,
                        'screenSpecs' => $row[7] ?? null,
                        'SSD' => $row[8] ?? null,
                        'expandable_slots' => $row[9] ?? null,
                        'battery_capacity_wh' => $row[10] ?? null,
                        'charging_watt' => $row[11] ?? null,
                        'USB_A_ports' => $row[12] ?? 0,
                        'USB_C_ports' => $row[13] ?? 0,
                        'HDMI_ports' => $row[14] ?? 0,
                        'LAN_port' => $row[15] ?? 0,
                        'Thunderbolt_ports' => $row[16] ?? 0,
                        'jack_3_5mm' => $row[17] ?? 0,
                        'special_features' => $row[18] ?? null,
                        'dimensions' => $row[19] ?? null,
                        'weight_kg' => is_numeric($row[20]) ? (float)$row[20] : null,
                    ];

                    if ($laptop) {
                        $laptop->update($data);
                    } else {
                        $data['productID'] = $product->id;
                        Laptop::create($data);
                    }

                    Log::info('✅ Import thành công: ' . $product->productName);
                } catch (\Exception $e) {
                    Log::error('❌ Lỗi import dòng: ' . json_encode($row) . ' | ' . $e->getMessage());
                }
            });
        }
    }
}
