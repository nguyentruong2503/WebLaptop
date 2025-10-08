<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Laptop;
use App\Models\Order_detail;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;


class RecommendController extends Controller
{
    public function getRecommend($id)
    {
        $reviews = Review::where('id_product', $id)
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json([
            'status' => true,
            'data' => $reviews
        ]);
    }

    public function storeReview(Request $request)
    {
        try {
            $userId = $request->userId;
            $productId = $request->productId;
            $rating = $request->rating;
            $comment = $request->comment;

            $order = Order::where('userID', $userId)
                ->pluck('id');

            if ($order->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn chưa có đơn hàng nào'
                ], 403);
            }

            $hasPurchased = Order_detail::whereIn('orderID', $order)
                ->where('productID', $productId)
                ->exists();

            if (!$hasPurchased) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn chưa mua sản phẩm này'
                ], 403);
            }

            $review = Review::create([
                'author' => $userId,
                'id_product' => $productId,
                'rate' => $rating,
                'comment' => $comment
            ]);

            return response()->json([
                'status' => true,
                'data' => $review,
                'message' => 'Đánh giá thành công'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
