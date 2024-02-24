<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Toy;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide a search query',
            ], 400);
        }

        $products = Toy::where('name', 'like', '%' . $query . '%')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Search results',
            'data' => $products,
        ]);
    }
}
