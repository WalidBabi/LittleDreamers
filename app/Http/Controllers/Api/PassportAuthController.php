<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Parentt;
use Illuminate\Http\Request;

use App\Models\Profile;

class PassportAuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|min:4',
            'last_name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        $user = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $profileId = $user->id;

        $token = $user->createToken('Register')->accessToken;

        $parent = new Parentt();
        $parent->profile_id = $profileId; // $profileId is the ID of the associated profile
        $parent->save();

        return response()->json(['token' => $token], 200)->header('Location', '/');
    }

    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $user = auth()->user();
            $fullName = $user->first_name . ' ' . $user->last_name;
            $token = $user->createToken('Login')->accessToken;

            $profile_id = $user->id;
            // dd($profile_id);
            $parent_ids = $user->parents;
            // dd($parent_ids);
            foreach ($parent_ids as $parent_id) {
                $parent_id = $parent_id->id;
            }
            // dd($parent_id);
            $parent = Parentt::find($parent_id);
            // dd($parent);

            $children_ids = $parent->children;
            foreach ($children_ids as $child_id) {
                $child_id = $child_id->id;
            }
            // dd($child_id);
            $children = [];

            $children = [];

            if ($parent) {
                // Retrieve children of the parent
                $children_data = $parent->children->map(function ($child) {
                    return [
                        'child_id' => $child->id,
                        'name' => $child->name,
                        'age' => $child->age,
                        'gender' => $child->gender,
                        'interests_and_preferences' => $child->interests_and_preferences,
                        'challenges_or_learning_needs' => $child->challenges_or_learning_needs
                    ];
                });

                if ($children_data->isNotEmpty()) {
                    $children = $children_data->toArray();
                }
            }

            // Return response with user's full name, token, and children
            return response()->json([
                'parent_id' => $parent_id,
                'fullName' => $fullName,
                // 'token' => $token,
                'children' => $children
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response([
            'message' => 'Logged out successfully'
        ]);
    }
}
