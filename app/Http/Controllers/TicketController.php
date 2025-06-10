<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Http\Resources\TicketResource as TicketResoure;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // Logique pour récupérer et retourner les tickets
        // Exemple : 
        return TicketResoure::collection(Ticket::all());
        //return response()->json(['message' => 'Liste des tickets']);
    }

    public function show($eventId)
    {
     
        // Logique pour récupérer un ticket spécifique par son ID
        $ticket = Ticket::findOrFail($eventId);
        return new TicketResoure($ticket);
    }
    

    public function getUserTickets($userId)
{
    // Vérifier que l'utilisateur existe
    $user = User::findOrFail($userId);

    // Récupérer les tickets liés aux événements de cet utilisateur
    $tickets = Ticket::whereHas('event', function ($query) use ($userId) {
        $query->where('created_by', $userId);
    })->get();

    return TicketResoure::collection($tickets);
    
}


    public function getUserTicket(Request $request)
{
    // Vérifier que l'utilisateur est authentifié
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }

    // Récupérer les tickets liés aux événements de cet utilisateur avec pagination
    $tickets = Ticket::whereHas('event', function ($query) use ($user) {
        $query->where('created_by', $user->id);
    })->paginate(10);

    return TicketResoure::collection($tickets);
}

    
    //
}