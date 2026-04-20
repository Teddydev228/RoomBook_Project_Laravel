<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Liste tous les utilisateurs.
     * Accessible : Admin
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $users,
            'total' => $users->count(),
        ]);
    }

    /**
     * Crée un nouvel utilisateur.
     * Accessible : Admin
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,responsable,enseignant',
        ], [
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.email'        => 'L\'email n\'est pas valide.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required'      => 'Le rôle est obligatoire.',
            'role.in'            => 'Le rôle doit être : admin, responsable ou enseignant.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'data'    => $user->only(['id', 'name', 'email', 'role', 'created_at']),
        ], 201);
    }

    /**
     * Affiche un utilisateur spécifique.
     * Accessible : Admin
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => $user->only(['id', 'name', 'email', 'role', 'created_at']),
        ]);
    }

    /**
     * Met à jour un utilisateur.
     * Accessible : Admin
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8|confirmed',
            'role'     => 'sometimes|in:admin,responsable,enseignant',
        ], [
            'email.email'        => 'L\'email n\'est pas valide.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.in'            => 'Le rôle doit être : admin, responsable ou enseignant.',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'data'    => $user->fresh()->only(['id', 'name', 'email', 'role', 'created_at']),
        ]);
    }

    /**
     * Supprime un utilisateur.
     * Accessible : Admin
     */
    public function destroy(User $user): JsonResponse
    {
        // Empêche l'admin de se supprimer lui-même
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 403);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.',
        ]);
    }
}
