<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Favorie extends Controller
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

       
        $event = Event::find($event_id);
        if (!$event) {
            return response()->json([
                'erreur' => 'Événement inexistant'
            ], 404);
        }

      
        $isFavorited = $user->events()->where('event_id', $event_id)->exists();

        if ($isFavorited) {
 
            $user->events()->detach($event_id);
            return response()->json([
                'message' => 'Événement retiré des favoris.',
            ]);
        } else {
 
            $user->events()->attach($event_id);
            return response()->json([
                'message' => 'Événement ajouté aux favoris.',
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
