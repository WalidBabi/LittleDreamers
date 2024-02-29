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
        $age = ToyDescription::distinct()->orderBy('age', 'asc')->pluck('age');
        $holiday = ToyDescription::distinct()->pluck('holiday');
        $skill_development = ToyDescription::distinct()->pluck('skill_development');
        $companies = Company::distinct()->pluck('name','id');

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

        // Retrieve price range from request parameters
        $priceRange = $request->input('price');

        // Query toys based on selected criteria and company, eager loading the company relationship
        $toys = Toy::with(['toy_description', 'toy_description.company'])
            ->whereHas('toy_description', function ($query) use ($selectedCompanies, $selectedCategories, $selectedAges, $selectedHolidays, $selectedSkillDevelopments, $priceRange) {
                // Applying filters if there are multiple criteria specified
                $query->when(!empty($selectedCompanies), function ($q) use ($selectedCompanies) {
                    $q->whereIn('company_id', $selectedCompanies);
                });
                $query->when(!empty($selectedCategories), function ($q) use ($selectedCategories) {
                    $q->whereIn('category', $selectedCategories);
                });
                $query->when(!empty($selectedAges), function ($q) use ($selectedAges) {
                    $q->whereIn('age', $selectedAges);
                });
                $query->when(!empty($selectedHolidays), function ($q) use ($selectedHolidays) {
                    $q->whereIn('holiday', $selectedHolidays);
                });
                $query->when(!empty($selectedSkillDevelopments), function ($q) use ($selectedSkillDevelopments) {
                    $q->whereIn('skill_development', $selectedSkillDevelopments);
                });
                $query->when(!empty($priceRange), function ($q) use ($priceRange) {
                    if (isset($priceRange['min']) && isset($priceRange['max'])) {
                        $q->whereBetween('price', [$priceRange['min'], $priceRange['max']]);
                    }
                });
            })
            ->get();

        // Return the filtered toys along with the company name
        return response()->json([
            'toys' => $toys,
        ]);
    }

    public function getFilteredToys(Request $request)
    {
        // Retrieve selected categories, ages, holidays, skill developments, and companies
        $selectedCategories = $request->input('categories', []);
        $selectedAges = $request->input('ages', []);
        $selectedHolidays = $request->input('holidays', []);
        $selectedSkillDevelopments = $request->input('skill_developments', []);
        $selectedCompanies = $request->input('companies', []);

        // Retrieve price range from request parameters
        $priceRange = $request->input('price');

        // Query toys based on selected criteria and company, eager loading the company relationship
        $toys = Toy::with(['toy_description', 'toy_description.company'])
            ->whereHas('toy_description', function ($query) use ($selectedCompanies, $selectedCategories, $selectedAges, $selectedHolidays, $selectedSkillDevelopments, $priceRange) {
                // Applying filters if there are multiple criteria specified
                $query->when(!empty($selectedCompanies), function ($q) use ($selectedCompanies) {
                    $q->whereIn('company_id', $selectedCompanies);
                });
                $query->when(!empty($selectedCategories), function ($q) use ($selectedCategories) {
                    $q->whereIn('category', $selectedCategories);
                });
                $query->when(!empty($selectedAges), function ($q) use ($selectedAges) {
                    $q->whereIn('age', $selectedAges);
                });
                $query->when(!empty($selectedHolidays), function ($q) use ($selectedHolidays) {
                    $q->whereIn('holiday', $selectedHolidays);
                });
                $query->when(!empty($selectedSkillDevelopments), function ($q) use ($selectedSkillDevelopments) {
                    $q->whereIn('skill_development', $selectedSkillDevelopments);
                });
                $query->when(!empty($priceRange), function ($q) use ($priceRange) {
                    if (isset($priceRange['min']) && isset($priceRange['max'])) {
                        $q->whereBetween('price', [$priceRange['min'], $priceRange['max']]);
                    }
                });
            })
            ->get();

        // Return the filtered toys along with the company name
        return response()->json([
            'toys' => $toys,
        ]);
    }
}
