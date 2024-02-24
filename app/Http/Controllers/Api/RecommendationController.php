<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Toy;
use App\Models\ToyDescription;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class RecommendationController extends Controller
{
    public function recommendations(Request $request)
    {
        $child_id = $request->input('child_id');

        // Fetch the child data based on the child_id
        $child = Child::find($child_id);
        // Prepare data for Python script (use actual form data)
        $childData = [
            'name' => $child->name,
            'age' => $child->age,
            'gender' => $child->gender,
            'interests_and_preferences' => $child->interests_and_preferences,
            'challenges_or_learning_needs' => $child->challenges_or_learning_needs,
        ];

        // Query the toys_description table for relevant data
        $toysDescriptions = ToyDescription::all();

        // // Extract relevant fields from toys descriptions
        $toysDescriptionsData = [];

        foreach ($toysDescriptions as $description) {
            $toysDescriptionsData[] = [
                'age' => $description->age,
                'description' => $description->description,
                'gender' => $description->gender,
                'skill_development' => $description->skill_development,
                'play_pattern' => $description->play_pattern,
            ];
        }
        // dd($toysDescriptionsData);

        // Ensure proper JSON encoding and command execution
        $JsonData = json_encode($childData);
        // Write JSON data to a temporary file
        $tmpFile = tempnam(sys_get_temp_dir(), 'toys_descriptions_');
        file_put_contents($tmpFile, json_encode($toysDescriptionsData));

        // Prepare command with file path as argument
        $command = [
            'C:\Python311\python.exe',
            'D:\LittleDreamers-main\LittleDreamers\LDDiagrams\recommendation_algorithm.py',
            $JsonData,
            $tmpFile, // Pass file path as argument
        ];

        // Execute the Python script
        $process = new Process($command);
        $process->run();

        // Clean up temporary file
        unlink($tmpFile);


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
                $toys_description = ToyDescription::whereIn('id', $idNumbers)->get();

                // Serialize the toys data
                $serializedToys = [];
                foreach ($toys as $toy) {
                    // Find the corresponding description for the current toy
                    $description = $toys_description->firstWhere('id', $toy->id);

                    $serializedToys[] = [
                        'id' => $toy->id,
                        'name' => $toy->name,
                        'description' => $description->description,
                        'category' => $description->category,
                        'holiday' => $description->holiday,
                        'skill_development' => $description->skill_development,
                        'play_pattern' => $description->play_pattern,
                        'price' => $toy->price,
                        'image' => $toy->image,
                    ];
                }

                // Return the toys data as JSON response
                return response()->json([
                    'child_id' => $child->id,
                    'toys' => $serializedToys,
                ]);
            } else {
                return response()->json(['error' => 'No recommendations found in output'], 500);
            }
        } else {
            $error = $process->getErrorOutput();
            return response()->json(['error' => $error], 500); // Internal Server Error
        }
    }
}
