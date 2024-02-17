<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;

class ChildFormController extends Controller
{
    public function submitForm(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'childName' => 'required|string',
            'childAge' => 'required|integer',
            // Add validation rules for other fields if needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Save the form data to your database or perform any other actions
        // Assuming you have a Child model
        $child = new \App\Models\Child();
        $child->name = $request->input('childName');
        $child->age = $request->input('childAge');
        $child->gender = $request->input('childGender');
        // Save other fields as needed
        $child->save();

        // Prepare the data to send to the Python algorithm
        $childData = [
            'age' => $request->input('childAge'),
            'Interests_and_Preferences' => implode(',', $request->input('favoritePatterns')),
            'Challenges_or_Learning_Needs' => implode(',', $request->input('learningNeeds'))
        ];

        // Convert the array to JSON for passing to the Python script
        $childDataJson = json_encode($childData);

        // Execute the Python script
        $process = new Process(['python', 'path/to/your/python/script.py', $childDataJson]);
        $process->run();

        // Check if the execution was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Get the output of the Python script (recommended toys)
        $recommendedToys = $process->getOutput();

        // Return a response with the recommended toys
        return response()->json(['toys' => $recommendedToys]);
    }
}
