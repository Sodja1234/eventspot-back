<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class FavoriteController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/events/{event_id}/favorite",
     *     operationId="__invoke",
     *     summary="Toggle favorite status for an event",
     *     description="Add or remove an event from the authenticated user's favorites.",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         description="ID of the event to toggle favorite",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite status toggled successfully",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="Event added to favorites.")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="Event removed from favorites.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="erreur", type="string", example="User not authenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="erreur", type="string", example="Event not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request, $event_id)
    {
        try {
            sleep(10);
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'erreur' => 'Utilisateur non connecté'
                ], 401);
            }

            $favorite = $user->events()->where('event_id', $event_id)->first();

            if($favorite){
                $currentEtat = $favorite->pivot->etat;
                $newEtat = $currentEtat == 1 ? 0 : 1;

                $user->events()->updateExistingPivot($event_id, [
                    'etat' => $newEtat,
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'data' => [
                        'message' => $newEtat ? 'Événement ajouté aux favoris.' : 'Événement retiré des favoris.',                      'etat' => $favorite->pivot->etat,
                        'etat' => $newEtat,
                    ],
                ]);
            } else {
                $user->events()->attach($event_id, [
                    'etat' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'data' => [
                        'message' => 'Événement ajouté aux favoris...',
                        'etat' => 1
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/events/{event_id}/favorite",
     *     summary="Check if an event is favorited by the authenticated user",
     *     description="Returns the favorite status of a specific event for the logged-in user.",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         description="ID of the event to check favorite status",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite status retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="This event is favorited."),
     *                 @OA\Property(property="etat", type="integer", enum={0,1}, example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="erreur", type="string", example="User not authenticated")
     *         )
     *     )
     * )
     */
    public function show(Request $request, string $event_id)
    {
        $user = auth()->user();
        $isFavorited = $user->events()->where('event_id', $event_id)->exists();

        if ($isFavorited) {
            return response()->json([
                'data' => [
                    'message' => 'cet event est bien en favori.',
                    'etat' => 1
                ],
            ]);
        } else {
            return response()->json([
                'data' => [
                    'message' => 'cet event n\'est pas en favori',
                    'etat' => 0
                ],
            ]);
        }
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
    public function getUserFavorites(Request $request)
    {
        $user = auth()->user();
        $favorites = $user->events()->wherePivot('etat', 1)->get();

        return EventResource::collection($favorites);
    }
}
