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