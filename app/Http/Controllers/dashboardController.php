<?php


namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Resources\EventResource;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard",
     *     summary="Get organizer dashboard statistics and data",
     *     description="Retrieve statistics, recent events, and tickets for the authenticated organizer user.",
     *     tags={"Organizers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Organizer"),
     *                 @OA\Property(property="email", type="string", format="email", example="john.organizer@example.com")
     *             ),
     *             @OA\Property(
     *                 property="stats",
     *                 type="object",
     *                 @OA\Property(property="total_events", type="integer", example=10),
     *                 @OA\Property(property="upcoming_events", type="integer", example=3),
     *                 @OA\Property(property="past_events", type="integer", example=7),
     *                 @OA\Property(property="total_participants", type="integer", example=150),
     *                 @OA\Property(property="total_revenue", type="number", format="float", example=12500.50)
     *             ),
     *             @OA\Property(
     *                 property="events",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/EventResource")
     *             ),
     *             @OA\Property(
     *                 property="tickets",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TicketResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not an organizer or not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Not authorized")
     *         )
     *     )
     * )
     */
    public function organizerDashboard(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->isOrganisateur()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $stats = [
            'total_events' => Event::forOrganisateur($user->id)->count(),
            'upcoming_events' => Event::forOrganisateur($user->id)->upcoming()->count(),
            'past_events' => Event::forOrganisateur($user->id)->past()->count(),
            'total_participants' => Ticket::whereHas('event', fn($q) => $q->where('created_by', $user->id))->sum('reserved_places'),
            'total_revenue' => Ticket::whereHas('event', fn($q) => $q->where('created_by', $user->id))
                         ->sum(DB::raw('price * reserved_places')),
        ];

        $events = Event::forOrganisateur($user->id)->with(['categories', 'ticket'])->latest()->paginate(6);
        $tickets = Ticket::whereHas('event', fn($q) => $q->where('created_by', $user->id))
                         ->with('event')->latest()->paginate(5);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => $stats,
            'events' => EventResource::collection($events),
            'tickets' => TicketResource::collection($tickets),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/events/{filter?}",
     *     summary="Get organizer's events with optional filtering and search",
     *     description="Retrieve a paginated list of events created by the authenticated organizer, optionally filtered by upcoming or past events and searchable by title or description.",
     *     tags={"Organizers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Filter events by status: 'all' (default), 'upcoming', or 'past'",
     *         required=false,
     *         @OA\Schema(type="string", enum={"all", "upcoming", "past"}, default="all")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter events by title or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of events retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/EventResource")
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
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function organizerEvents(Request $request, $filter = 'all')
    {
        $user = $request->user();
        $query = Event::forOrganisateur($user->id)->with(['categories', 'ticket']);

        if ($filter === 'upcoming') $query->upcoming();
        elseif ($filter === 'past') $query->past();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        return EventResource::collection($query->latest()->paginate(6));
    }
}