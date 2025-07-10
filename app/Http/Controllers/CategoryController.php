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
    /**
     * @OA\Get(
     *      path="/api/categories",
     *      operationId="getCategoriesList",
     *      tags={"Categories"},
     *      summary="Get list of categories",
     *      description="Returns list of categories.",
     *      @OA\Parameter(
     *          name="all",
     *          in="query",
     *          description="Set to true to get all categories without pagination",
     *          required=false,
     *          @OA\Schema(type="boolean")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Category")
     *          )
     *      )
     * )
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
    /**
     * @OA\Post(
     *      path="/api/categories",
     *      operationId="storeCategory",
     *      tags={"Categories"},
     *      summary="Create a new category",
     *      description="Creates a new category. Note: This route should be protected by authentication & authorization middleware.",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Category creation data",
     *          @OA\JsonContent(
     *              required={"title", "description"},
     *              @OA\Property(property="title", type="string", example="Music"),
     *              @OA\Property(property="description", type="string", example="Events related to music and concerts")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful creation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="insertion réussit"),
     *              @OA\Property(property="data", ref="#/components/schemas/Category")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="object")
     *          )
     *      )
     * )
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
    /**
     * @OA\Get(
     *      path="/api/categories/{id}",
     *      operationId="getCategoryById",
     *      tags={"Categories"},
     *      summary="Get category information",
     *      description="Returns category data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Category id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Category")
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Category Not Found")
     *          )
     *      )
     * )
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