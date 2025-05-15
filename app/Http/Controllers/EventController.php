<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255|unique:events',
        'description' => 'required|string|max:500',
        'cycle' => 'nullable|string|max:100',
        'created_by' => 'required|exists:users,id',
        'date_time_start' => 'required|date|after_or_equal:today',
        'date_time_end' => 'required|date|after_or_equal:date_time_start',
        'category_ids' => 'required|array',
        'category_ids.*' => 'exists:categories,id',
        'tickets' => 'nullable|array|min:1',
        'tickets.*.name' => 'required|string|max:255',
        'tickets.*.price' => 'required|numeric|min:10', 
        'tickets.*.places' => 'required|integer|min:1',
        'description'=>'required|string|max:100',
        // 'url'=>'required|file|mimes:mp4,mp3,jpeg,png,jpg'
       
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Données invalides',
            'errors' => $validator->errors()
        ], 422);
    }

    $event = Event::create($request->except('tickets'));

    if ($request->has('category_ids') && is_array($request->category_ids)) {
        $event->categories()->attach($request->category_ids);
        $event->load('categories');
    }
        
    $createdTickets = [];
    $reserved_places = 0;
    if ($request->has('tickets') && is_array($request->input('tickets'))) {
        foreach ($request->input('tickets') as $ticketData) {
            $ticket = Ticket::create([
                'event_id' => $event->id,
                'name' => $ticketData['name'],
                'description'=>$ticketData['description'],
                'price' => $ticketData['price'],
                'places' => $ticketData['places'],
                'reserved_places'=>$reserved_places
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
        ],
        201
    ]);

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
