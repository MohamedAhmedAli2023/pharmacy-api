<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // GET /carts - List all cart items for the authenticated user
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
                        ->with('medicine')
                        ->get();
        return response()->json([
            'message' => $cartItems->isEmpty() ? 'Cart is empty' : 'Cart retrieved successfully',
            'data' => $cartItems
        ], 200);
    }

    // POST /carts - Add a medicine to the cart
    public function store(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $medicine = Medicine::findOrFail($request->medicine_id);
        $cartItem = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'medicine_id' => $medicine->id,
        ]);

        $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $request->quantity;
        $cartItem->save();

        return response()->json([
            'message' => 'Medicine added to cart',
            'data' => $cartItem->load('medicine')
        ], 201);
    }

    // GET /carts/{id} - Show a specific cart item (optional, rarely used)
    public function show($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
                        ->with('medicine')
                        ->findOrFail($id);
        return response()->json([
            'message' => 'Cart item retrieved successfully',
            'data' => $cartItem
        ], 200);
    }

    // PUT /carts/{id} - Update quantity of a cart item
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->quantity = $request->quantity;

        if ($cartItem->quantity === 0) {
            $cartItem->delete();
            return response()->json(['message' => 'Cart item removed'], 204);
        }

        $cartItem->save();
        return response()->json([
            'message' => 'Cart item updated',
            'data' => $cartItem->load('medicine')
        ], 200);
    }

    // DELETE /carts/{id} - Remove a specific item from the cart
    public function destroy($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();
        return response()->json(['message' => 'Cart item removed'], 204);
    }
}
