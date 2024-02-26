<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Toy;
use App\Models\ToyDescription;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Toy::orderBy('created_at', 'desc')->get();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Toy::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $toy_description = ToyDescription::where('id', $id)->get()->toArray();

        return response()->json([
            'product' => $product,
            'description' => $toy_description
        ], 200);
    }

    public function Review(Request $request)
    {
        $review = new Review();
        $review->child_id = $request->input('child_id');
        $review->toy_id = $request->input('toy_id');
        $review->rating = $request->input('rating');
        // Save the review
        $review->save();

        // You can return a response indicating success or handle errors appropriately
        return response()->json(['message' => 'Review saved successfully'], 200);
    }
}
