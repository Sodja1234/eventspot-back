<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/search/user/{name}",
     *     summary="Search users by name or email",
     *     description="Search for users whose name or email contains the specified string (case-insensitive). Returns paginated results.",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         description="Name or email substring to search for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users found and returned in paginated format",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No users found matching the search criteria",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function index(string $name)
    {
        $query = User::query();

        $query->where(function ($q) use ($name) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($name) . '%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($name) . '%']);
        });

        $users = $query->paginate(10);

        if ($users->isEmpty()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return UserResource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Get user details by ID",
     *     description="Retrieve detailed information of a user including their interests.",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to retrieve",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $user = User::with('interets')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/user/edit/{id}",
     *     summary="Update a user and their interests",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to update",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data to update, including optional interests",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=255,
     *                 description="Name of the user"
     *             ),
     *             @OA\Property(
     *                 property="interets",
     *                 type="array",
     *                 description="Array of interest IDs to sync with the user",
     *                 @OA\Items(type="integer")
     *             ),
     *             example={
     *                 "name": "John Doe",
     *                 "interets": {1, 2, 3}
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully updated with interests",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User attempted to update another user",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You can not update another user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="object", example={
     *               "name"={"The name must be a string."},
     *               "interets"={"The interets must be an array."}
     *             })
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();

        if ($user->id != $id) {
            return response()->json(['message' => 'You can not update another user'], 403);
        }

        $userByID = User::with('interets')->find($id);

        $validator = Validator::make($request->all(), [
            'name'      => 'sometimes|string|max:255',
            'interets'   => 'sometimes|array|min:1',
            'interets.*' => 'numeric|exists:interets,id', // vérifie que les IDs existent dans la table interets
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Mise à jour des champs utilisateur (hors intérêts)
        if (isset($validated['name'])) {
            $userByID->name = $validated['name'];
            $userByID->save();
        }

        // Synchronisation des intérêts si fournis
        if (isset($validated['interets'])) {
            // sync() va attacher les intérêts présents dans le tableau,
            // détacher ceux qui ne sont pas dans le tableau et garder ceux présents
            $userByID->interets()->sync($validated['interets']);
        }

        // Recharger la relation pour retourner les données à jour
        $userByID->load('interets');

        return UserResource::make($userByID);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}