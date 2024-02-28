<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Toy;
use App\Models\ToyDescription;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function addToy(Request $request)
    {
        // Validate incoming request data for both Toy and ToyDescription
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Assuming image is required and supported formats are jpeg, png, jpg, gif with a max size of 2MB
        //     // Add more validation rules for other Toy attributes as needed
        //     'category' => 'required|string|max:255',
        //     'age' => 'required|string|max:255',
        //     'gender' => 'required|string|max:255',
        //     'holiday' => 'required|string|max:255',
        //     'skill_development' => 'required|string|max:255',
        //     'play_pattern' => 'required|string|max:255',
        //     'price' => 'required|numeric',
        //     'quantity' => 'required|integer',
        // ]);

        // if ($validator->fails()) {
        //     return Redirect::back()->withErrors($validator)->withInput();
        // }

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
}
