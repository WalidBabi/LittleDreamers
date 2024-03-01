<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parentt;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Toy;
use App\Models\ToyDescription;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Toy::orderByDesc('updated_at')->orderByDesc('created_at')->get();
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
        // Authenticate the user using the token
        if ($request->header('Authorization')) {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            // dd( $token );
            $profile = Profile::where('remember_token', $token)->first();
            // dd( $profile );
            if (!$profile) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // dd( $profile);
        $parent_ids = $profile->parents;
        // dd($parent_ids);
        foreach ($parent_ids as $parent_id) {
            $parent_id = $parent_id->id;
        }
        // dd($parent_id);
        $parent = Parentt::find($parent_id);
        // dd($parent->id);
        $review = new Review();
        $review->parent_id = $parent->id;
        $review->toy_id = $request->input('productId');
        $review->rating = $request->input('rating');
        // Save the review
        $review->save();

        // You can return a response indicating success or handle errors appropriately
        return response()->json(['message' => 'Review saved successfully'], 200);
    }
}
