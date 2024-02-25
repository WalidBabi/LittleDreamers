<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Toy;
use App\Models\ToyDescription;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Toy::all();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Toy::find($id);
    
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        $toy_description = ToyDescription::where('toy_id', $id)->orderBy('created_at', 'desc')->get()->toArray();
    
        return response()->json([
            'product' => $product,
            'description' => $toy_description 
        ], 200);
    }
}
