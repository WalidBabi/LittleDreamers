<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Toy;
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
        $child->name = $request->input('childName');
        $child->age = $request->input('childAge');
        $child->gender = $request->input('childGender');
        $child->interests_and_preferences = $request->input('childInterests');
        $child->challenges_or_learning_needs = $request->input('childChallenges');
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
        $JsonData = json_encode($childData);

        $command = [
            'C:\Users\waled\AppData\Local\Programs\Python\Python311\python.exe',
            'C:/Users/waled/Desktop/LittleDreamers/LDDiagrams/recommendation_algorithm.py',
            $JsonData,
        ];

        // Execute the Python script
        $process = new Process($command);
        $process->run();

        // Handle output and potential errors
        if ($process->isSuccessful()) {
            $output = $process->getOutput();
        
            $matches = [];
            // Use regular expression to extract ID numbers
            preg_match_all('/\s+(\d+)\s+/', $output, $matches);

            // Check if matches are found
            if (!empty($matches[1])) {
                // Extract the first 5 ID numbers
                $idNumbers = array_slice($matches[1], 0, 5);

                // Add 1 to each extracted number
                $idNumbers = array_map(function ($num) {
                    return intval($num) + 1;
                }, $idNumbers);

                // Query the database for toys based on the extracted IDs
                $toys = Toy::whereIn('id', $idNumbers)->get();

                // Serialize the toys data
                $serializedToys = [];
                foreach ($toys as $toy) {
                    $serializedToys[] = [
                        'id' => $toy->id,
                        'name' => $toy->name,
                        // Add other fields as needed
                    ];
                }

                // Return the toys data as JSON response
                return response()->json(['toys' => $serializedToys]);
            } else {
                return response()->json(['error' => 'No ID numbers found in output'], 500);
            }
        } else {
            $error = $process->getErrorOutput();
            return response()->json(['error' => $error], 500); // Internal Server Error
        }
    }
}
