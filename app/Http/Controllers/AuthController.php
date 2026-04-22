<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];

        // Try to find user in mock data
        $mockUsers = $this->api->get('/api/users') ?? [];
        
        $foundUser = null;
        foreach ($mockUsers as $u) {
            if (($u['email'] ?? '') === $email) {
                $foundUser = $u;
                break;
            }
        }

        if ($foundUser) {
            // User found - check password (in mock mode, just verify password is not empty)
            if (empty($password)) {
                return back()->withErrors(['email' => 'Mot de passe incorrect']);
            }
            
            $mockToken = 'mock_token_' . uniqid();
            $this->api->setToken($mockToken);

            $user = [
                'id' => $foundUser['id'],
                'name' => $foundUser['name'],
                'email' => $foundUser['email'],
                'role' => $foundUser['role'],
            ];

            Session::put('user', $user);
            Session::flash('success', 'Connexion réussie');

            return match ($user['role']) {
                'admin' => redirect()->route('admin.dashboard'),
                'responsable' => redirect()->route('responsable.dashboard'),
                'enseignant' => redirect()->route('enseignant.dashboard'),
            };
        }

        // Fallback: use mock role detection for test accounts
        $role = match (true) {
            str_contains($email, 'admin') => 'admin',
            str_contains($email, 'responsable') => 'responsable',
            default => 'enseignant',
        };

        $mockToken = 'mock_token_' . uniqid();
        $this->api->setToken($mockToken);

        $user = [
            'id' => rand(1, 100),
            'name' => explode('@', $email)[0],
            'email' => $email,
            'role' => $role,
        ];

        Session::put('user', $user);
        Session::flash('success', 'Connexion réussie (mode test)');

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'responsable' => redirect()->route('responsable.dashboard'),
            'enseignant' => redirect()->route('enseignant.dashboard'),
        };
    }

    public function logout(Request $request)
    {
        $this->api->clearToken();
        Session::forget('user');
        Session::flash('success', 'Déconnecté avec succès');

        return redirect()->route('login');
    }

    public function unauthorized()
    {
        return view('auth.unauthorized');
    }
}