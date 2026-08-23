<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');

        $startStr = $request->query('startDate');
        $endStr = $request->query('endDate');

        $startDate = $startStr ? Carbon::parse($startStr) : Carbon::today('Asia/Kolkata')->subDays(30)->startOfDay();
        $endDate = $endStr ? Carbon::parse($endStr) : Carbon::today('Asia/Kolkata')->endOfDay();

        $orders = Order::where('owner_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('items')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalOrders = $orders->count();
        $totalRevenue = (float) $orders->sum('total_amount');
        $averageTicketSize = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Daily sales breakdown
        $dailyMap = [];
        $temp = $startDate->copy()->startOfDay();
        $daysDiff = $temp->diffInDays($endDate);

        if ($daysDiff <= 90) {
            while ($temp <= $endDate) {
                $dateStr = $temp->format('Y-m-d');
                $dailyMap[$dateStr] = 0.0;
                $temp->addDay();
            }
        }

        $itemMap = [];

        foreach ($orders as $order) {
            $dateStr = $order->created_at->format('Y-m-d');
            $amount = (float) $order->total_amount;
            $dailyMap[$dateStr] = ($dailyMap[$dateStr] ?? 0.0) + $amount;

            foreach ($order->items as $item) {
                $key = $item->menu_item_name;
                $qty = $item->quantity;
                $rev = (float) $item->price * $qty;

                if (isset($itemMap[$key])) {
                    $itemMap[$key]['quantity'] += $qty;
                    $itemMap[$key]['revenue'] += $rev;
                } else {
                    $itemMap[$key] = [
                        'name' => $key,
                        'quantity' => $qty,
                        'revenue' => $rev,
                    ];
                }
            }
        }

        $dailySales = [];
        foreach ($dailyMap as $date => $rev) {
            $dailySales[] = ['date' => $date, 'revenue' => $rev];
        }
        usort($dailySales, fn($a, $b) => strcmp($a['date'], $b['date']));

        $topItems = array_values($itemMap);
        usort($topItems, fn($a, $b) => $b['quantity'] <=> $a['quantity']);
        $topItems = array_slice($topItems, 0, 10);

        return response()->json([
            'success' => true,
            'data' => [
                'metrics' => [
                    'totalOrders' => $totalOrders,
                    'totalRevenue' => $totalRevenue,
                    'averageTicketSize' => $averageTicketSize,
                ],
                'dailySales' => $dailySales,
                'topItems' => $topItems,
            ]
        ]);
    }
}
