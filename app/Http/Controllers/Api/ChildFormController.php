<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;

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
        $child->name = $request->input('name');
        $child->age = $request->input('age');
        $child->gender = $request->input('gender');
        $child->interests_and_preferences = $request->input('interests_and_preferences');
        $child->challenges_or_learning_needs = $request->input('challenges_or_learning_needs');
        $child->save();

        // Prepare data for Python script (use actual form data)
        $childData = [
            'name' => $child->name,
            'age' => $child->age,
            'gender' => $child->gender,
            'interests_and_preferences' => $child->interests_and_preferences,
            'challenges_or_learning_needs' => $child->challenges_or_learning_needs,
        ];

        // Ensure proper JSON encoding and command execution
        $jsonData = json_encode($childData);

        $escapedJsonData = escapeshellarg($jsonData);
        $escapedJsonDataWithQuotes = '' . $escapedJsonData . '';

        $command = [
            'C:\Users\waled\AppData\Local\Programs\Python\Python311\python.exe',
            'C:/Users/waled/Desktop/LittleDreamers/LDDiagrams/recommendation_algorithm.py',
            $escapedJsonDataWithQuotes,
        ];

        // Execute the Python script
        $process = new Process($command);
        $process->run();

        // Handle output and potential errors
        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $recommendations = json_decode($output);
            return response()->json($recommendations);
        } else {
            $error = $process->getErrorOutput();
            return response()->json(['error' => $error], 500); // Internal Server Error
        }
    }
}
