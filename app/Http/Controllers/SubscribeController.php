<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/events/{event_id}/subscribers",
     *     summary="Get active subscribers of an event",
     *     description="Retrieve the list of users subscribed (with active subscription) to a specific event. Only the event organizer can access this endpoint.",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         description="ID of the event to get subscribers for",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscribers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="event", type="string", example="Event Title"),
     *             @OA\Property(property="total_abonnes", type="integer", example=42),
     *             @OA\Property(
     *                 property="abonnes",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/UserResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Only the event organizer can view subscribers",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Access denied. Only the organizer can view subscribers.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Event not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error retrieving subscribers: error details")
     *         )
     *     )
     * )
     */
    public function getSubscribers($event_id)
{
    try {

        $event = Event::with(['subscribeUsers' => function($query) {
            $query->wherePivot('etat', 1);
        }])->findOrFail($event_id);


        if (auth()->id() != $event->created_by) {
            return response()->json([
                'message' => 'Accès refusé. Seul l\'organisateur peut voir les abonnés.'
            ], 403);
        }

        $subscribers = $event->subscribeUsers;

        return response()->json([
            'event' => $event->title,
            'total_abonnes' => $subscribers->count(),
            'abonnes' => UserResource::collection($subscribers)
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la récupération des abonnés: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/events/{event_id}/subscribe",
     *     summary="Toggle subscription status for an event",
     *     description="Activate, deactivate, or create a subscription for the authenticated user on a specific event.",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         description="ID of the event to toggle subscription",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subscription activated."),
     *             @OA\Property(property="etat", type="integer", enum={0,1}, example=1)
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
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'erreur' => 'Utilisateur non connecté'
                ], 401);
            }

            $event = Event::withCount(['subscribeUsers' => function ($query) {
                $query->where('etat', 1);
            }])->find($event_id);

            if (!$event) {
                return response()->json([
                    'erreur' => 'Événement non trouvé'
                ], 404);
            }

            $subscription = $user->subscribEvents()->where('event_id', $event_id)->first();


            if ($subscription) {
                $currentEtat = $subscription->pivot->etat;
                $newEtat = $currentEtat == 1 ? 0 : 1;


                if ($newEtat == 1 && $event->remaining_seats >= $event->available) {
                    return response()->json([
                        'message' => 'Nombre de places déjà atteint. Impossible de réactiver la souscription.'
                    ], 422);
                }


                $user->subscribEvents()->updateExistingPivot($event_id, [
                    'etat' => $newEtat,
                    'updated_at' => now(),
                ]);


                $event->remaining_seats += ($newEtat == 1) ? 1 : -1;
                $event->save();

                return response()->json([
                    'data' => [
                        'message' => $newEtat ? 'Souscription activée.' : 'Souscription désactivée.',
                        'etat' => $newEtat,
                    ]
                ]);
            }


            if ($event->remaining_seats >= $event->available) {
                return response()->json([
                    'message' => 'Nombre de places déjà atteint. Impossible de souscrire.'
                ], 422);
            }

            $user->subscribEvents()->attach($event_id, [
                'etat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            $event->remaining_seats += 1;
            $event->save();

            return response()->json([
                'data' => [
                    'message' => 'Souscription créée.',
                    'etat' => 1
                ]
            ]);
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
     *     path="/api/events/{event_id}/subscribe",
     *     summary="Check subscription status for an event",
     *     description="Returns whether the authenticated user's subscription to a specific event is active or not.",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         description="ID of the event to check subscription status",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription status retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Your subscription to this event is active."),
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

        if (auth()->user()->subscribEvents()->where('event_id', $event_id)->where('etat', 1)->exists()) {
            return response()->json([
                'data' => [
                    'message' => 'Votre souscription à cet event est activé.',
                    'etat' => 1
                ],
            ]);
        } else {
            return response()->json([
                'data' => [
                    'message' => 'Votre souscription à cet event est desactivé.',
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
}
