<?php

namespace App\Http\Controllers;

use App\Models\Interets;
use Illuminate\Http\Request;

class InteretsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        return response()->json( Interets::all()
        );
    }

    // POST /api/interets
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255'
        ]);

        $interet = Interets::create([
            'nom' => $request->nom
        ]);

        return response()->json([
            'message' => 'Intérêt créé avec succès',
            'interet' => $interet
        ], 201);
    }

    // GET /api/interets/{id}
    public function show($id)
    {
        $interet = Interets::findOrFail($id);

        return response()->json($interet);
    }

    // PUT /api/interets/{id}
    public function update(Request $request, $id)
    {
        $interet = Interets::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255'
        ]);

        $interet->update([
            'nom' => $request->nom
        ]);

        return response()->json([
            'message' => 'Intérêt mis à jour avec succès',
            'interet' => $interet
        ]);
    }

    // DELETE /api/interets/{id}
    public function destroy($id)
    {
        $interet = Interets::findOrFail($id);
        $interet->delete();

        return response()->json([
            'message' => 'Intérêt supprimé avec succès'
        ]);
    }
}