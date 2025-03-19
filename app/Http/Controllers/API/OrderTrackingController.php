<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;

class OrderTrackingController extends Controller
{
    // Update order status (pharmacist only)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,canceled',
        ]);

        $order = Order::findOrFail($id);

        // Create new status record
        $status = OrderStatus::create([
            'order_id' => $order->id,
            'status' => $request->status,
        ]);

        // Update order's current status
        $order->update(['status' => $request->status]);
        // Send email notification to the order’s user
        Mail::to($order->user->email)->send(new OrderStatusUpdated($order, $request->status));
        return response()->json([
            'message' => 'Order status updated,email sent to user',
            'status' => $status,
        ], 200);
    }

    // Get order tracking history
    public function track($id)
    {
        $order = Order::with('statuses')->findOrFail($id);

        // Ensure user owns the order or is a pharmacist
        if (Auth::id() !== $order->user_id && !Auth::user()->hasRole('pharmacist')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'message' => 'Order tracking retrieved',
            'order' => $order->only('id', 'total_price', 'payment_status'),
            'tracking' => $order->statuses->map(function ($status) {
                return [
                    'status' => $status->status,
                    'updated_at' => $status->updated_at,
                ];
            }),
            'current_status' => $order->latestStatus->status ?? 'pending',
        ]);
    }
}
