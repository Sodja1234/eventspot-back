<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
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
    public function __invoke(Request $request, $event_id)
{
    try {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'erreur' => 'Utilisateur non connecté'
            ], 401);
        }

    
        $subscription = $user->subscribEvents()->where('event_id', $event_id)->first();

        if ($subscription) {
    
            $currentEtat = $subscription->pivot->etat;

      
            $newEtat = $currentEtat == 1 ? 0 : 1;

            $user->subscribEvents()->updateExistingPivot($event_id, [
                'etat' => $newEtat,
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => $newEtat ? 'Souscription activée.' : 'Souscription désactivée.',
                'etat' => $newEtat,
            ]);
        } else {
          
            $user->subscribEvents()->attach($event_id, [
                'etat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Souscription créée.',
                'etat' => 1,
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
    public function show(string $id)
    {
        //
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
