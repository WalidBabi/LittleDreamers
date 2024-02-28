<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Toy;
use App\Models\ToyDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function addToy(Request $request)
    {
        // Create a new Company instance
        $company = new Company();
        $company->name = $request->input('company');
        $company->save();

        // // Create a new ToyDescription instance
        $toyDescription = new ToyDescription();
        $toyDescription->company_id = $company->id;
        $toyDescription->category = $request->input('category');
        $toyDescription->description = $request->input('description');
        $toyDescription->age = $request->input('age');
        $toyDescription->gender = $request->input('gender');
        $toyDescription->holiday = $request->input('holiday');
        $toyDescription->skill_development = $request->input('skill_development');
        $toyDescription->play_pattern = $request->input('play_pattern');
        $toyDescription->save();

        // Handle image upload
        $ImageFile = $request->file('image');
        $ImagePath = ('/img'); // Set the path where you want to store cover images
        $uniqueImageFileName = uniqid() . '.' . $ImageFile->getClientOriginalExtension();
        $ImageFile->move(public_path($ImagePath), $uniqueImageFileName);

        // Create a new Toy instance
        $toy = new Toy();
        $toy->name = $request->input('name');
        $toy->toy_description_id = $toyDescription->id;
        $toy->price = $request->input('price');
        $toy->image = 'http://localhost:8000' . $ImagePath . '/' . $uniqueImageFileName;
        $toy->quantity = $request->input('quantity');
        $toy->save();

        // Redirect back to dashboard with success message
        return response()->json(['success' => true, 'message' => 'Toy added successfully!'], 200);
    }

    public function updateToy(Request $request, $id)
    {
        // Retrieve the toy to edit
        $toy = Toy::findOrFail($id);
        $ImageFile = $request->file('image');

        // Update the associated toy description
        $toyDescription = $toy->toy_description;
        $toyDescription->category = $request->input('category');
        $toyDescription->description = $request->input('description');
        $toyDescription->age = $request->input('age');
        $toyDescription->gender = $request->input('gender');
        $toyDescription->holiday = $request->input('holiday');
        $toyDescription->skill_development = $request->input('skill_development');
        $toyDescription->play_pattern = $request->input('play_pattern');
        $toyDescription->save();

        $ImagePath = '/img'; // Set the default image path
        $uniqueImageFileName = null; // Define $uniqueImageFileName with a default value

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            $ImageFile = $request->file('image');
            // Set the path where you want to store cover images
            $uniqueImageFileName = uniqid() . '.' . $ImageFile->getClientOriginalExtension();
            $ImageFile->move(public_path($ImagePath), $uniqueImageFileName);
        }

        // Update other toy details
        $toy->name = $request->input('name');
        $toy->price = $request->input('price');
        $toy->quantity = $request->input('quantity');

        // Check if $uniqueImageFileName is not null before using it
        if ($uniqueImageFileName !== null) {
            $toy->image = 'http://localhost:8000' . $ImagePath . '/' . $uniqueImageFileName;
        }
        $toy->save();

        // Retrieve company name associated with the toy's description
        $companyName = $toyDescription->company;
        $companyName->name = $request->input('company');
        $companyName->save();
        // Redirect back to dashboard with success message
        return response()->json([
            'success' => true,
            'message' => 'Toy updated successfully!'
        ], 200);
    }


    public function delete($id)
    {
        try {
            // Find the Toy record by ID
            $toy = Toy::findOrFail($id);

            // Fetch the associated ToyDescription record
            $toyDescription = ToyDescription::where('id', $id)->first();

            // Check if the ToyDescription record exists
            if ($toyDescription) {
                // Find the Company record by its ID
                $company = Company::findOrFail($toyDescription->company_id);
                // Delete the associated ToyDescription record
                $toyDescription->delete();

                // Check if the Company record exists
                if ($company) {
                    // Delete the associated Company record
                    $company->delete();
                }
            }

            // Check if the cover and attachment_url fields are not null or empty
            if (!empty($toy->image)) {
                // Define file paths
                $imagepath =  $toy->image;
                //"C:\Users\waled\Desktop\LittleDreamers\public\http://localhost:8000/img/65deea21e5943.jpg"
                //the image is in C:\Users\waled\Desktop\LittleDreamers\public\img\65dee9a9e1b74.jpg
                // Construct the absolute file paths using public_path
                // Construct the absolute file paths using public_path
                $imageAbsolutePath = public_path('img/' . basename($imagepath));

                // Check if the file exists before attempting to delete it
                if (File::exists($imageAbsolutePath)) {
                    // Delete the image file
                    File::delete($imageAbsolutePath);
                }
            }

            // Delete the Toy record
            $toy->delete();

            return "Toy Deleted";
        } catch (\Throwable $th) {
            return "error " . $th->getMessage();
        }
    }
}
