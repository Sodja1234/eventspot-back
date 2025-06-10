<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EventResource;
use App\Models\Media;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::query();
        $per_page = $request->query('per_page', 20);

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        if ($request->query('cycle')) {
            $cycle = strtolower($request->query('cycle'));
            $query->whereRaw('LOWER(cycle) LIKE ?', ['%' . $cycle . '%']);
        }

        $events = $query->paginate($per_page);

        if ($events->isEmpty()) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return EventResource::collection($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'title' => 'required|string|max:255|unique:events',
        //     'description' => 'required|string|max:500',
        //     'cycle' => 'nullable|string|max:100',
        //     'created_by' => 'required|exists:users,id',
        //     'date_time_start' => 'required|date|after_or_equal:today',
        //     'date_time_end' => 'required|date|after_or_equal:date_time_start',
        //     'category_ids' => 'required|array',
        //     'category_ids.*' => 'exists:categories,id',
        //     'tickets' => 'nullable|array|min:1',
        //     'tickets.*.name' => 'required|string|max:255',
        //     'tickets.*.price' => 'required|numeric|min:10',
        //     'tickets.*.places' => 'required|integer|min:1',
        //     'tickets.*.description' => 'required|string|max:100',
        //     // 'url'=>'required|file|mimes:mp4,mp3,jpeg,png,jpg'
        // ]);

        $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255|unique:events',
        'description' => 'required|string|max:500',
        'cycle' => 'nullable|string|max:100',
        'created_by' => 'required|exists:users,id',
        'date_time_start' => 'required|date|after_or_equal:today',
        'date_time_end' => 'required|date|after_or_equal:date_time_start',
        'category_ids' => 'nullable|array',
        'category_ids.*' => 'exists:categories,id',
        'address' => 'required|string|max:255',
        'latitude' => ['required', 'numeric', 'between:-90,90'],
        'longitude' => ['required', 'numeric', 'between:-180,180'],
        'tickets' => 'nullable|array|min:1',
        'tickets.*.name' => 'required|string|max:255',
        'tickets.*.price' => 'required|numeric|min:10',
        'tickets.*.places' => 'required|integer|min:1',
        'tickets.*.description' => 'required|string|max:100',
        'url' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240'
]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::create($request->except('tickets'));
        $path = $request->file('url')->store('url', 'public'); 

        $media = Media::create([
        'event_id' => $event->id,
        'url' => 'storage/' . $path, 
        ]);

        if ($request->has('category_ids') && is_array($request->category_ids)) {
            $event->categories()->attach($request->category_ids);
            $event->load('categories');
        }

        $createdTickets = [];
        $reserved_places = 0;
        if ($request->has('tickets') && is_array($request->input('tickets'))) {
            foreach ($request->input('tickets') as $ticketData) {
                $ticket = Ticket::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'cycle' => $request->cycle,
                    'created_by' => $request->created_by,
                    'date_time_start' => $request->date_time_start,
                    'date_time_end' => $request->date_time_end,
                    'address' => $request->address,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,

                ]);
                $createdTickets[] = $ticket->toArray();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Événement et billets créés avec succès',
            'data' => [
            'event' => $event->toArray(),
            'tickets' => $createdTickets,
            'url'=>$media
            ],
            201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return EventResource::make($event);
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
    public function getLastThreeEvents()
    {
        $events = Event::orderBy('id', 'desc')
            ->take(3)
            ->get();
        return EventResource::collection($events);
    }
}