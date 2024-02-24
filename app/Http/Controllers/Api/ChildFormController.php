<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChildFormController extends Controller
{
    public function processForm(Request $request)
    {
        // Validate form data (uncomment if needed)
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string',
        //     'age' => 'required|integer',
        //     'gender' => 'required|string',
        //     'interests_and_preferences' => 'required|string',
        //     'challenges_or_learning_needs' => 'required|string',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 400);
        // }

        // Save form data to the database
        $child = new Child();
        $child->name = $request->input('childName');
        $child->age = $request->input('childAge');
        $child->gender = $request->input('childGender');
        $child->interests_and_preferences = $request->input('childInterests');
        $child->challenges_or_learning_needs = $request->input('childChallenges');
        $child->save();
        return response([
            'message' => 'Form Submitted'
        ]);
    }
}
