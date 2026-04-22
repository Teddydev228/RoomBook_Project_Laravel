<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Affiche la liste du matériel
     */
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $equipment = $this->api->get('/api/equipment');

        return view('equipment.index', [
            'user' => $user,
            'equipment' => $equipment ?? [],
        ]);
    }

    /**
     * Affiche le formulaire de création de matériel
     */
    public function create(Request $request)
    {
        $user = $request->session()->get('user');

        return view('equipment.create', [
            'user' => $user,
        ]);
    }

    /**
     * Enregistre un nouveau matériel
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available');

        $response = $this->api->post('/api/equipment', $data);

        if ($response) {
            return redirect()->route('equipment.index')
                ->with('success', 'Matériel créé avec succès');
        }

        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la création du matériel');
    }

    /**
     * Affiche le formulaire d'édition du matériel
     */
    public function edit(Request $request, int $id)
    {
        $user = $request->session()->get('user');
        $item = $this->api->get("/api/equipment/{$id}");

        if (!$item) {
            return redirect()->route('equipment.index')
                ->with('error', 'Matériel non trouvé');
        }

        return view('equipment.edit', [
            'user' => $user,
            'equipment' => $item,
        ]);
    }

    /**
     * Met à jour le matériel
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available');

        $response = $this->api->put("/api/equipment/{$id}", $data);

        if ($response) {
            return redirect()->route('equipment.index')
                ->with('success', 'Matériel mis à jour avec succès');
        }

        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la mise à jour du matériel');
    }

    /**
     * Supprime le matériel
     */
    public function destroy(Request $request, int $id)
    {
        $response = $this->api->delete("/api/equipment/{$id}");

        if ($response) {
            return redirect()->route('equipment.index')
                ->with('success', 'Matériel supprimé avec succès');
        }

        return back()->with('error', 'Erreur lors de la suppression du matériel');
    }
}
