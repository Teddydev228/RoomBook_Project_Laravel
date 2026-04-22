<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Affiche la liste des salles
     */
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $rooms = $this->api->get('/api/rooms');

        return view('rooms.index', [
            'user' => $user,
            'rooms' => $rooms ?? [],
        ]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle salle
     */
    public function create(Request $request)
    {
        $user = $request->session()->get('user');

        return view('rooms.create', [
            'user' => $user,
        ]);
    }

    /**
     * Enregistre une nouvelle salle
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'building' => 'required|string|max:255',
            'is_available' => 'sometimes|boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available');

        $response = $this->api->post('/api/rooms', $data);

        if ($response) {
            return redirect()->route('rooms.index')
                ->with('success', 'Salle créée avec succès');
        }

        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la création de la salle');
    }

    /**
     * Affiche le formulaire d'édition d'une salle
     */
    public function edit(Request $request, int $id)
    {
        $user = $request->session()->get('user');
        $room = $this->api->get("/api/rooms/{$id}");

        if (!$room) {
            return redirect()->route('rooms.index')
                ->with('error', 'Salle non trouvée');
        }

        return view('rooms.edit', [
            'user' => $user,
            'room' => $room,
        ]);
    }

    /**
     * Met à jour une salle
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'building' => 'required|string|max:255',
            'is_available' => 'sometimes|boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available');

        $response = $this->api->put("/api/rooms/{$id}", $data);

        if ($response) {
            return redirect()->route('rooms.index')
                ->with('success', 'Salle mise à jour avec succès');
        }

        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la mise à jour de la salle');
    }

    /**
     * Supprime une salle
     */
    public function destroy(Request $request, int $id)
    {
        $response = $this->api->delete("/api/rooms/{$id}");

        if ($response) {
            return redirect()->route('rooms.index')
                ->with('success', 'Salle supprimée avec succès');
        }

        return back()->with('error', 'Erreur lors de la suppression de la salle');
    }
}
