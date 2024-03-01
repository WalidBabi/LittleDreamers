<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Parentt;
use App\Models\Profile;
use App\Models\Toy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingCart extends Controller
{
    public function store(Request $request)
    {
        // Authenticate the user using the token
        if ($request->header('Authorization')) {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            $profile = Profile::where('remember_token', $token)->first();
            if (!$profile) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $parent_ids = $profile->parents;
        $parent_id = null; // Initialize parent_id variable
        foreach ($parent_ids as $parent) {
            $parent_id = $parent->id;
        }

        if ($parent_id === null) {
            return response()->json(['error' => 'Parent not found'], 404);
        }

        $parent = Parentt::find($parent_id);

        // Create a new order
        $order = new Order();
        $order->total_price = 0; // Initialize total price
        $order->parent_id = $parent->id;
        $order->save(); // Save the order to generate an ID

        foreach ($request->products as $product) {
            $toy = Toy::findOrFail($product['productId']);

            // Check if requested quantity is available
            if ($toy->quantity < $product['quantity']) {
                return response()->json(['error' => 'Requested quantity not available for ' . $toy->name], 400);
            }

            // Attach the toy to the order with quantity and price
            $order->toys()->attach($toy->id, [
                'order_id' => $order->id, // Attach the order_id
                'quantity' => $product['quantity'],
                'price' => $product['fullPrice'] / $product['quantity'], // Calculate price per unit
            ]);

            // Decrement the available quantity
            $toy->quantity -= $product['quantity'];
            $toy->save();

            // Increment total price
            $order->total_price += $product['fullPrice'];
        }

        // Save the updated total price
        $order->save();

        return response()->json(['message' => 'Order placed successfully'], 200);
    }
}
