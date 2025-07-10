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

    /**
     * @OA\Get(
     *     path="/api/dashboard/tickets",
     *     summary="Get paginated tickets for events created by the authenticated user",
     *     description="Retrieve a paginated list of tickets related to events created by the authenticated user.",
     *     tags={"Organizers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Tickets retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TicketResource")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 description="Pagination links"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 description="Pagination metadata"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not authenticated")
     *         )
     *     )
     * )
     */
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