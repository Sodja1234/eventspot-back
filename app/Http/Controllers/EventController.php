<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EventResource;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/events",
     *      operationId="getEventsList",
     *      tags={"Events"},
     *      summary="Get list of events",
     *      description="Returns list of events with filtering and pagination.",
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Search keyword for event title or description",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="category_id",
     *          in="query",
     *          description="Filter by category ID",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="user_id",
     *          in="query",
     *          description="Filter by creator user ID",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="Filter by event status",
     *          required=false,
     *          @OA\Schema(type="string", enum={"upcoming", "past"})
     *      ),
     *      @OA\Parameter(
     *          name="latest",
     *          in="query",
     *          description="Get the latest events",
     *          required=false,
     *          @OA\Schema(type="boolean")
     *      ),
     *      @OA\Parameter(
     *          name="count",
     *          in="query",
     *          description="Number of latest events to return (used with 'latest')",
     *          required=false,
     *          @OA\Schema(type="integer", default=6)
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Number of events per page for pagination",
     *          required=false,
     *          @OA\Schema(type="integer", default=20)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Event")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Event not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Event not found")
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $query = Event::with(['categories', 'user', 'medias']);

        // latest event
        if ($request->has('latest')) {
            if ($request->has('exclude')) {
                $excludeId = $request->query('exclude_id');
                $query->where('id', '!=', $excludeId);
            }
            $count = $request->query('count', 6);
            $events = $query->orderBy('id', 'desc')->take($count)->get();
            return EventResource::collection($events);
        }
        // latest by category
        if ($request->has('recent_by_category')) {
            $query->whereHas('categories', function ($q) use ($request): void {
                $q->where('categories.title', $request->recent_by_category);
            });
            $count = $request->query('recent_count', 1);

            return EventResource::collection($query->orderBy('id', 'desc')->take($count)->get());
        }

        $per_page = $request->query('per_page', 20);

        // Search keyword
        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtering by specific fields
        $filters = $request->except(['search', 'per_page', 'page', 'category_id', 'user_id', 'status', 'latest', 'count']);
        foreach ($filters as $field => $value) {
            if ($value && Schema::hasColumn('events', $field)) {
                $query->where($field, 'like', "%{$value}%");
            }
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('created_by', $request->user_id);
        }

        if ($request->has('status')) {
            $status = $request->status;
            if ($status === 'upcoming') {
                $query->upcoming();
            } elseif ($status === 'past') {
                $query->past();
            }
        }


        $events = $query->orderBy('id', 'desc')->paginate($per_page);

        if ($events->isEmpty() && !$request->has('search') && !$request->has('category_id') && !$request->has('user_id') && !$request->has('status') && empty(array_filter($filters))) {
            $events = Event::with(['categories', 'user', 'medias'])->paginate($per_page);
        } elseif ($events->isEmpty()) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return EventResource::collection($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *      path="/api/events",
     *      operationId="storeEvent",
     *      tags={"Events"},
     *      summary="Create a new event",
     *      description="Creates a new event, its associated tickets, and uploads a media file. Requires organizer privileges.",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Event creation data",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"title", "description", "created_by", "date_time_start", "date_time_end", "address", "latitude", "longitude"},
     *                  @OA\Property(property="title", type="string", example="My Awesome Concert"),
     *                  @OA\Property(property="description", type="string", example="A description of the awesome concert."),
     *                  @OA\Property(property="cycle", type="string", nullable=true, example="Annual"),
     *                  @OA\Property(property="created_by", type="integer", description="ID of the organizer user", example=1),
     *                  @OA\Property(property="date_time_start", type="string", format="date-time", example="2025-10-31T20:00:00Z"),
     *                  @OA\Property(property="date_time_end", type="string", format="date-time", example="2025-10-31T23:00:00Z"),
     *                  @OA\Property(property="address", type="string", example="123 Music Lane, Concert City"),
     *                  @OA\Property(property="latitude", type="number", format="float", example=48.8566),
     *                  @OA\Property(property="longitude", type="number", format="float", example=2.3522),
     *                  @OA\Property(property="category_ids[]", type="array", @OA\Items(type="integer"), description="Array of category IDs to associate with the event."),
     *                  @OA\Property(property="tickets[0][name]", type="string", example="General Admission"),
     *                  @OA\Property(property="tickets[0][price]", type="number", format="float", example=50.00),
     *                  @OA\Property(property="tickets[0][places]", type="integer", example=200),
     *                  @OA\Property(property="tickets[0][description]", type="string", example="Access to the main area."),
     *                  @OA\Property(property="url", type="string", format="binary", description="Event banner image or video (jpg, jpeg, png, mp4). Max 10MB."),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful creation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Événement et billets créés avec succès"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="event", ref="#/components/schemas/Event"),
     *                  @OA\Property(property="tickets", type="array", @OA\Items(ref="#/components/schemas/Ticket")),
     *                  @OA\Property(property="url", ref="#/components/schemas/Media")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Données invalides"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden (User is not an organizer)"
     *      )
     * )
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
        //dd($request);
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:events',
            'description' => 'required|string',
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
            'url' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240',
            'available'=> 'required|min:0|integer',
        

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $remaining_seats =0;
        $event = Event::create($request->except('tickets'));
        $path = $request->file('url')->store('url', 'public');
        $user = auth()->user();


        $media = Media::create([
            'event_id' => $event->id,
            'url' => 'storage/' . $path,
        ]);

        if ($request->has('category_ids') && is_array($request->category_ids)) {
            $event->categories()->attach($request->category_ids);
            $event->load('categories');
        }

        $createdTickets = [];
        if ($request->has('tickets') && is_array($request->input('tickets'))) {
            foreach ($request->input('tickets') as $ticketData) {
                $ticket = Ticket::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'cycle' => $request->cycle,
                    'created_by' => $user,
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
                'url' => $media
            ],
            201
        ]);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *      path="/api/events/{id}",
     *      operationId="getEventById",
     *      tags={"Events"},
     *      summary="Get event information",
     *      description="Returns event data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Event id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/EventResource")
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Event not found")
     *          )
     *      )
     * )
     */
    public function show(string $id, Request $request)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if ($request->user()) {
            $user = $request->user();

            $event->load(['favoritedByUsers' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }, 'subscribeUsers' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);

            $event->setRelation('favorite', $event->favoritedByUsers->first());
            $event->setRelation('subscribe', $event->subscribeUsers->first());
        }
        return EventResource::make($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $event)
{
    $user = Auth::user();

    $event = Event::find($event);


    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Utilisateur non authentifié'
        ], 401);
    }

    if (!$event) {
        return response()->json([
            'success' => false,
            'message' => 'Événement non trouvé'
        ], 404);
    }

    if ($event->created_by !== $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'Vous n\'êtes pas autorisé à modifier cet événement'
        ], 403);
    }

  
    

    $validator = Validator::make($request->all(), [
        'title' => 'sometimes|string|max:255|unique:events,title,' . $event->id,
        'description' => 'sometimes|string|max:500',
        'cycle' => 'nullable|string|max:100',
        'date_time_start' => 'sometimes|date|after_or_equal:today',
        'date_time_end' => 'sometimes|date|after_or_equal:date_time_start',
        'category_ids' => 'nullable|array',
        'category_ids.*' => 'exists:categories,id',
        'address' => 'sometimes|string|max:255',
        'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
        'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
        'tickets' => 'nullable|array|min:1',
        'tickets.*.name' => 'sometimes|string|max:255',
        'tickets.*.price' => 'sometimes|numeric|min:10',
        'tickets.*.places' => 'sometimes|integer|min:1',
        'tickets.*.description' => 'sometimes|string|max:100',
        'url' => 'sometimes|file|mimes:jpg,jpeg,png,mp4|max:10240'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Données invalides',
            'errors' => $validator->errors()
        ], 422);
    }

   
    $event->update($request->except(['tickets', 'category_ids', 'url']));

    // dd($event);

    // Mise à jour des catégories (relation many-to-many)
    if ($request->has('category_ids')) {
        $event->categories()->sync($request->category_ids);
    }

    // Mise à jour du média
    if ($request->hasFile('url')) {
        $path = $request->file('url')->store('url', 'public');

        // Supprime l'ancien média lié
        $event->medias()->delete();

        // Crée un nouveau média
        $media = Media::create([
            'event_id' => $event->id,
            'url' => 'storage/' . $path,
        ]);
    } else {
        $media = $event->medias()->first();
    }

    // Supprime les anciens tickets
    $event->ticket()->delete();

    $createdTickets = [];

    // Création des nouveaux tickets
    if ($request->has('tickets')) {
        foreach ($request->tickets as $ticketData) {
            $ticket = new Ticket([
                'name' => $ticketData['name'],
                'price' => $ticketData['price'],
                'places' => $ticketData['places'],
                'description' => $ticketData['description'],
            ]);
            $event->ticket()->save($ticket);
            $createdTickets[] = $ticket->toArray();
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Événement mis à jour avec succès',
        'data' => [
            'event' => $event->fresh()->toArray(),
            'tickets' => $createdTickets,
            'url' => $media
        ]
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/api/events/favorites",
     *      operationId="getUserFavorites",
     *      tags={"Events"},
     *      summary="Get user's favorite events",
     *      description="Returns a list of events that the authenticated user has favorited.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Event")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function getUserFavorites(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'erreur' => 'Utilisateur non connecté'
                ], 401);
            }

            $favorites = $user->events()->orderBy('event_id', 'desc')->get();
            $event = EventResource::collection($favorites);

            return response()->json([
                'data' => $event
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/events",
     *     operationId="getUserEvents",
     *     tags={"Events"},
     *     summary="Get events created by the authenticated user",
     *     description="Returns user information along with all events created by the authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="role", type="string", enum={"user", "organisateur"}, example="organisateur")
     *             ),
     *             @OA\Property(
     *                 property="events",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/EventResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
     public function getUserEvents(Request $request)
{
    $user = $request->user();
    $events = Event::where('created_by', $user->id)->get();

    $userData = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ];

    return response()->json([
        'user' => $userData,
        'events' => EventResource::collection($events),
    ]);
}
}
