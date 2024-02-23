<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Toy;
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
        
        return response()->json($product);
    }
}
