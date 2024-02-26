<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Toy;
use App\Models\ToyDescription;
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

    public function getFilters()
    {
        $category = ToyDescription::distinct()->pluck('category');
        $age = ToyDescription::distinct()->pluck('age');
        $holiday = ToyDescription::distinct()->pluck('holiday');
        $skill_development = ToyDescription::distinct()->pluck('skill_development');
        $companies = Company::pluck('name', 'id');

        // Prepare the response data
        $filters = [
            'category' => $category,
            'age' => $age,
            'holiday' => $holiday,
            'skill_development' => $skill_development,
            'companies' => $companies,
        ];

        // Return the response
        return response()->json($filters);
    }
}
