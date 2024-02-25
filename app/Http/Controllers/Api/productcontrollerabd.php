<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Toy;
use Illuminate\Http\Request;
use Validator;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Toy::all();
        return response()->json([
         'succcess'=> true,
         'message'=> 'All propucts',
         'products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $requset-> all();
    $validator = Validator::make($input,[
        'toy_description_id'=>'required',
         'name'=>'required',
         'price'=>'required',
         'image'=>'required',
         'quantity'=>'required',
    ]);
    if($validator-> fulse()){
    return response()->json([
     'fail'=> fulse,
     'message'=> 'sory not stored',
     'erorr' =>$validator->errors() ]);
}
  $products = products::create( $input);
  return response()->json([
 'succcess'=> true,
 'message'=> 'propucts created successfully',
 'products' => $products]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $products = products::find( $id);
        if( is_null( $products)){
            return response()->json([
             'fail'=> fulse,
             'message'=> 'sory not fond',
            ]); 
        }
            return response()->json([
                'succcess'=> true,
                'message'=> 'propucts fetched successfully',
                'products' => $products]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, products $products)
    {
        $input = $requset-> all();
        $validator = Validator::make($input,[
            'toy_description_id'=>'required',
             'name'=>'required',
             'price'=>'required',
             'image'=>'required',
             'quantity'=>'required',
        ]);
        if($validator-> fulse()){
        return response()->json([
         'fail'=> fulse,
         'message'=> 'sory not stored',
         'erorr' =>$validator->errors() ]);
        }
        $products->toy_description_id=['toy_description_id'];
        $products-> name=['name'];
        $products->price=['price'];
        $products->image=['image'];
        $products->quantity=['quantity'];
        $products->save();
    
        return response()->json([
            'succcess'=> true,
            'message'=> 'propucts fetched successfully',
            'products' => $products]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( products $products)
    {
        $products->delete();
                return response()->json([
                    'succcess'=> true,
                    'message'=> 'propucts delete successfully',
                    'products' => $products]);
    }
}
