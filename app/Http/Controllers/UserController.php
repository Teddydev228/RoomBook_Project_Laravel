<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Affiche la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $users = $this->api->get('/api/users');

        return view('users.index', [
            'user' => $user,
            'users' => $users ?? [],
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouvel utilisateur
     */
    public function create(Request $request)
    {
        $user = $request->session()->get('user');

        return view('users.create', [
            'user' => $user,
        ]);
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:enseignant,responsable',
        ]);

        $response = $this->api->post('/api/users', $data);

        if ($response) {
            return redirect()->route('users.index')
                ->with('success', 'Utilisateur créé avec succès');
        }

        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la création de l\'utilisateur');
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit(Request $request, int $id)
    {
        $user = $request->session()->get('user');
        $editUser = $this->api->get("/api/users/{$id}");

        if (!$editUser) {
            return redirect()->route('users.index')
                ->with('error', 'Utilisateur non trouvé');
        }

        return view('users.edit', [
            'user' => $user,
            'editUser' => $editUser,
        ]);
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:enseignant,responsable',
        ]);

        // Supprime le mot de passe si vide
        if (empty($data['password'])) {
            unset($data['password']);
            unset($data['password_confirmation']);
        }

        $response = $this->api->put("/api/users/{$id}", $data);

        if ($response) {
            return redirect()->route('users.index')
                ->with('success', 'Utilisateur mis à jour avec succès');
        }

        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la mise à jour de l\'utilisateur');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy(Request $request, int $id)
    {
        $response = $this->api->delete("/api/users/{$id}");

        if ($response) {
            return redirect()->route('users.index')
                ->with('success', 'Utilisateur supprimé avec succès');
        }

        return back()->with('error', 'Erreur lors de la suppression de l\'utilisateur');
    }
}
