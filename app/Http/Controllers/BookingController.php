<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Affiche la liste des réservations (pour l'enseignant)
     */
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $result = $this->api->get('/api/bookings/my');
        $bookings = $result['data'] ?? [];

        return view('bookings.index', [
            'user' => $user,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle réservation
     */
    public function create(Request $request)
    {
        $user = $request->session()->get('user');
        $roomsResponse = $this->api->get('/api/rooms');
        $equipmentResponse = $this->api->get('/api/equipment');

        $rooms = $this->extractList($roomsResponse);
        $equipment = $this->extractList($equipmentResponse);

        return view('bookings.create', [
            'user' => $user,
            'rooms' => is_array($rooms) ? $rooms : [],
            'equipment' => is_array($equipment) ? $equipment : [],
        ]);
    }

    /**
     * Extrait une liste depuis différents formats de réponse API.
     */
    private function extractList($response): array
    {
        if (! is_array($response)) {
            return [];
        }

        if (array_is_list($response)) {
            return $response;
        }

        if (isset($response['data']) && is_array($response['data'])) {
            if (array_is_list($response['data'])) {
                return $response['data'];
            }

            if (isset($response['data']['data']) && is_array($response['data']['data']) && array_is_list($response['data']['data'])) {
                return $response['data']['data'];
            }
        }

        return [];
    }

    /**
     * Enregistre une nouvelle réservation
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|integer',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'purpose' => 'required|string|max:1000',
            'equipment' => 'sometimes|array',
            'equipment.*.equipment_id' => 'nullable|integer',
            'equipment.*.quantity' => 'nullable|integer|min:1',
        ]);

        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = Carbon::parse($data['ends_at']);

        if ($startsAt->lt(now()->addHours(2))) {
            return back()
                ->withInput()
                ->withErrors([
                    'starts_at' => 'La réservation doit être faite au moins 2 heures à l\'avance.',
                ]);
        }

        if ($endsAt->gt((clone $startsAt)->addHours(4))) {
            return back()
                ->withInput()
                ->withErrors([
                    'ends_at' => 'La durée maximale d\'une réservation est de 4 heures.',
                ]);
        }

        $rooms = $this->extractList($this->api->get('/api/rooms'));
        $selectedRoom = collect($rooms)->firstWhere('id', (int) $data['room_id']);

        if (! $selectedRoom) {
            return back()
                ->withInput()
                ->withErrors([
                    'room_id' => 'La salle sélectionnée est introuvable.',
                ]);
        }

        $roomAvailable = (bool) ($selectedRoom['is_available'] ?? $selectedRoom['available'] ?? true);

        if (! $roomAvailable) {
            return back()
                ->withInput()
                ->withErrors([
                    'room_id' => 'Cette salle est actuellement indisponible.',
                ]);
        }

        $equipmentCatalog = collect($this->extractList($this->api->get('/api/equipment')))
            ->keyBy(fn ($item) => (int) ($item['id'] ?? 0));

        $data['equipment'] = collect($data['equipment'] ?? [])
            ->filter(fn ($item) => !empty($item['equipment_id']))
            ->filter(function ($item) use ($equipmentCatalog) {
                $equipmentId = (int) ($item['equipment_id'] ?? 0);
                $equipment = $equipmentCatalog->get($equipmentId);

                return $equipment && (bool) ($equipment['is_available'] ?? $equipment['available'] ?? true);
            })
            ->map(function ($item) {
                return [
                    'equipment_id' => (int) $item['equipment_id'],
                    'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                ];
            })
            ->values()
            ->all();

        $response = $this->api->post('/api/bookings', $data);

        if ($response) {
            return redirect()->route('bookings.index')
                ->with('success', 'Demande de réservation créée avec succès');
        }

        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la création de la réservation');
    }

    /**
     * Annule une réservation
     */
    public function cancel(Request $request, int $bookingId)
    {
        $response = $this->api->post("/api/bookings/{$bookingId}/cancel");

        if ($response) {
            return redirect()->back()
                ->with('success', 'Réservation annulée avec succès');
        }

        return back()->with('error', 'Erreur lors de l\'annulation de la réservation');
    }

    /**
     * Prend une décision sur une réservation en attente (accepter/refuser) - AJAX
     */
    public function decision(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string|max:1000',
        ]);

        $response = $this->api->post("/api/bookings/{$id}/decision", $data);

        if ($response) {
            return response()->json([
                'success' => true,
                'message' => $data['status'] === 'accepted' 
                    ? 'Réservation acceptée avec succès' 
                    : 'Réservation refusée',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du traitement de la réservation',
        ], 500);
    }

    /**
     * Affiche les réservations en attente (pour le responsable)
     */
    public function pending(Request $request)
    {
        $user = $request->session()->get('user');
        $result = $this->api->get('/api/bookings/pending');
        $pendingBookings = $result['data'] ?? [];

        return view('bookings.pending', [
            'user' => $user,
            'pendingBookings' => $pendingBookings,
        ]);
    }

    /**
     * Affiche les détails d'une réservation
     */
    public function show(Request $request, int $id)
    {
        $user = $request->session()->get('user');
        // Récupère les détails de la réservation - depuis mes réservations ou en attente
        $result = $this->api->get("/api/bookings/{$id}");

        if (!$result) {
            return redirect()->back()->with('error', 'Réservation non trouvée');
        }

        $booking = $result['data'] ?? $result;

        return view('bookings.show', [
            'user' => $user,
            'booking' => $booking,
        ]);
    }
}
