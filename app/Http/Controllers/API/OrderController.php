<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Medicine;
use App\Models\Cart;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Create a new order
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $items = $request->input('items');
        $totalPrice = 0;

        foreach ($items as $item) {
            $medicine = Medicine::find($item['medicine_id']);
            if (!$medicine) {
                return response()->json([
                    'message' => "Medicine ID {$item['medicine_id']} not found",
                    'error' => 'Invalid medicine',
                ], 404);
            }
            if ($medicine->stock < $item['quantity']) {
                return response()->json([
                    'message' => "Insufficient stock for {$medicine->name}",
                    'error' => 'Stock unavailable',
                ], 400);
            }
            $totalPrice += $medicine->price * $item['quantity'];
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        foreach ($items as $item) {
            $medicine = Medicine::find($item['medicine_id']);
            $order->medicines()->attach($medicine->id, [
                'quantity' => $item['quantity'],
                'price' => $medicine->price,
            ]);
            $medicine->decrement('stock', $item['quantity']);
        }

        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        $order->load('medicines');

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);
    }

    // List all orders for the authenticated user
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('medicines')->get();
        return response()->json([
            'message' => $orders->isEmpty() ? 'No orders found' : 'Orders retrieved successfully',
            'orders' => $orders,
        ]);
    }

    // Show a specific order
    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())->with('medicines')->find($id);
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
                'error' => 'Invalid order ID',
            ], 404);
        }
        return response()->json([
            'message' => 'Order retrieved successfully',
            'order' => $order,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,shipped,delivered,canceled',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
                'error' => 'Invalid order ID',
            ], 404);
        }
        // Optional: Add role check (e.g., middleware('role:pharmacist'))
        $order->update(['status' => $request->status]);

        // Add to status history
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ]);
    }

    // Delete an order (e.g., cancel if pending)
    public function destroy($id)
    {
        $order = Order::where('user_id', Auth::id())->find($id);
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
                'error' => 'Invalid order ID',
            ], 404);
        }
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending orders can be canceled',
                'error' => 'Invalid status',
            ], 400);
        }
        $order->delete();
        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }

    // Confirm an order (custom action from your routes)
    public function confirm($id)
    {
        $order = Order::where('user_id', Auth::id())->find($id);
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
                'error' => 'Invalid order ID',
            ], 404);
        }
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Order already confirmed or canceled',
                'error' => 'Invalid status',
            ], 400);
        }
        $order->update(['status' => 'confirmed']);
        // Add to status history
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
        ]);

        return response()->json([
            'message' => 'Order confirmed successfully',
            'order' => $order,
        ]);
    }

    // Create order from cart
    public function storeFromCart(Request $request)
    {
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('medicine')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $totalPrice = 0;
        foreach ($cartItems as $item) {
            if ($item->medicine->stock < $item->quantity) {
                return response()->json([
                    'message' => "Insufficient stock for {$item->medicine->name}",
                    'error' => 'Stock unavailable'
                ], 400);
            }
            $totalPrice += $item->medicine->price * $item->quantity;
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        foreach ($cartItems as $item) {
            $order->medicines()->attach($item->medicine_id, [
                'quantity' => $item->quantity,
                'price' => $item->medicine->price,
            ]);
            $item->medicine->decrement('stock', $item->quantity);
            $item->delete();
        }

        // Add initial status to order_statuses
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        // Delegate to PaymentController
        return app(PaymentController::class)->store($request, $order->id);
    }
}
