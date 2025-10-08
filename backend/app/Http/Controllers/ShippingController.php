<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingController extends Controller
{
    public function calculateFee(Request $request)
    {
        $validated = $request->validate([
            'from_district' => 'required|integer',
            'to_district' => 'required|integer',
            'to_ward' => 'required',
            'weight' => 'required|integer|min:1',
        ]);

        $token = env('GHN_TOKEN'); // lưu trong .env
        $shopId = env('GHN_SHOP_ID'); // cũng có trong tài khoản GHN

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Token' => $token,
            'ShopId' => $shopId,
        ])->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
            "service_id" => 53321, // ID dịch vụ GHN, cậu có thể thay theo khu vực
            "insurance_value" => 100000,
            "from_district_id" => $request->from_district,
            "to_district_id" => $request->to_district,
            "to_ward_code" => $request->to_ward,
            "weight" => $request->weight,
        ]);

        return response()->json($response->json());
    }
}
