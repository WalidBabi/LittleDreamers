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

    public function handleCheckboxSubmission(Request $request)
    {
        // Retrieve selected categories, ages, holidays, skill developments, and companies
        $selectedCategories = $request->input('categories', []);
        $selectedAges = $request->input('ages', []);
        $selectedHolidays = $request->input('holidays', []);
        $selectedSkillDevelopments = $request->input('skill_developments', []);
        $selectedCompanies = $request->input('companies', []);

        // Query toys based on selected criteria and company, eager loading the company relationship
        $toys = Toy::with(['toy_description', 'toy_description.company'])
            ->whereHas('toy_description', function ($query) use ($selectedCompanies, $selectedCategories, $selectedAges, $selectedHolidays, $selectedSkillDevelopments) {
                if (!empty($selectedCompanies)) {
                    $query->whereIn('company_id', $selectedCompanies);
                }
                if (!empty($selectedCategories)) {
                    $query->whereIn('category', $selectedCategories);
                }
                if (!empty($selectedAges)) {
                    $query->whereIn('age', $selectedAges);
                }
                if (!empty($selectedHolidays)) {
                    $query->whereIn('holiday', $selectedHolidays);
                }
                if (!empty($selectedSkillDevelopments)) {
                    $query->whereIn('skill_development', $selectedSkillDevelopments);
                }
            })
            ->get();

        // Return the filtered toys along with the company name
        $filteredToys = $toys->map(function ($toy) {
            return [
                'toy' => $toy,
                'company_name' => $toy->toy_description->company->name
            ];
        });

        return response()->json([
            'toys' => $filteredToys,
        ]);
    }
}
