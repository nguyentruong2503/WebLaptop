<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;

class ThongkeController extends Controller
{
    public function dashboard()
    {
        $monthNow = date('m');
        $monthPrev = date('m', strtotime('-1 month'));
        $yearNow = date('Y');
        $yearPrev = date('Y', strtotime('-1 month'));

        $revenueNow = Order::revenue($monthNow, $yearNow);
        $revenuePrev = Order::revenue($monthPrev, $yearPrev);

        $ordersNow = Order::orderCount($monthNow, $yearNow);
        $ordersPrev = Order::orderCount($monthPrev, $yearPrev);

        $customersNow = User::newUserCount($monthNow, $yearNow);
        $customersPrev = User::newUserCount($monthPrev, $yearPrev);

        $cancelNow = Order::cancelCount($monthNow, $yearNow);
        $cancelPrev = Order::cancelCount($monthPrev, $yearPrev);

        $revenueChart = [
            'labels' => [],
            'data' => [],
        ];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('m', strtotime("-$i month"));
            $year = date('Y', strtotime("-$i month"));
            $label = 'Tháng ' . (int)$month;
            $value = Order::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('orderstatus', '!=', 'Cancelled')
                ->sum('totalAmount') / 1000000;
            $revenueChart['labels'][] = $label;
            $revenueChart['data'][] = round($value, 2);
        }

        $statusLabels = ['Confirmed', 'Shipped', 'Delivered', 'Cancelled'];
        $statusData = [];
        foreach ($statusLabels as $status) {
            $statusData[] = Order::statusCount($status);
        }

        return response()->json([
            'summary' => [
                'revenue_now' => $revenueNow,
                'revenue_prev' => $revenuePrev,
                'orders_now' => $ordersNow,
                'orders_prev' => $ordersPrev,
                'customers_now' => $customersNow,
                'customers_prev' => $customersPrev,
                'cancel_now' => $cancelNow,
                'cancel_prev' => $cancelPrev,
            ],
            'revenue_chart' => $revenueChart,
            'order_status_chart' => [
                'labels' => $statusLabels,
                'data' => $statusData,
            ]
        ]);
    }
}
