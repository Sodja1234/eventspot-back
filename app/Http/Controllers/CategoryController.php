<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->has('all')) {
            $query = Category::All();
            return CategoryResource::collection($query);
        }
        $query = Category::query();
        $categories = $query->paginate(18);

        return CategoryResource::collection($categories);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
                'title'=>'required|string|max:255|unique:categories',
                'description'=>'required|max:255',
            ]
            );

            if($validator->fails())
            {
                return response()->json([
                    'success'=>false,
                    'message'=>$validator->errors()
                ]);
            }

            $category = Category::create($request->all());


            return response()->json([
                'success'=>true,
                'message'=>'insertion réussit',
                'data'=>$category
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category Not Found'
            ]);
        }
        return CategoryResource::make($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}