<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])
                           ->where('status', 'delivered')
                           ->sum('total_price');

        $orderCount = Order::whereBetween('created_at', [$startDate, $endDate])
                           ->where('status', 'delivered')
                           ->count();

        $topMedicines = Medicine::withCount(['orders' => function ($query) use ($startDate, $endDate) {
                                $query->whereBetween('orders.created_at', [$startDate, $endDate])
                                      ->where('orders.status', 'delivered');
                            }])
                            ->orderBy('orders_count', 'desc')
                            ->take(10)
                            ->get();

        $salesByCategory = Medicine::join('order_medicine', 'medicines.id', '=', 'order_medicine.medicine_id')
                                   ->join('orders', 'order_medicine.order_id', '=', 'orders.id')
                                   ->join('categories', 'medicines.category_id', '=', 'categories.id')
                                   ->whereBetween('orders.created_at', [$startDate, $endDate])
                                   ->where('orders.status', 'delivered')
                                   ->select('categories.name as category_name', DB::raw('SUM(order_medicine.quantity * order_medicine.price) as total_sales'))
                                   ->groupBy('categories.name')
                                   ->get();

        return response()->json([
            'total_sales' => $totalSales,
            'order_count' => $orderCount,
            'top_medicines' => $topMedicines,
            'sales_by_category' => $salesByCategory,
        ]);
    }
}
