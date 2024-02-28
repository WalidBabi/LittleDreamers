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
        $products = Toy::orderBy('id', 'desc')->get();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Toy::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $toy_description = ToyDescription::find($id);
        // Assuming 'company' is the method to access the company relationship on ToyDescription
        $company_name = $toy_description->company->name;

        return response()->json([
            'product' => $product,
            'description' => $toy_description,
            'company' => $company_name
        ], 200);
    }

    public function Review(Request $request)
    {
        $review = new Review();
        $review->child_id = $request->input('child_id');
        $review->toy_id = $request->input('product_Id');
        $review->rating = $request->input('rating');
        // Save the review
        $review->save();

        // You can return a response indicating success or handle errors appropriately
        return response()->json(['message' => 'Review saved successfully'], 200);
    }
}
