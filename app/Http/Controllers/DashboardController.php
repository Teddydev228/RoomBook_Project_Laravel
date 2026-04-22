<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Dashboard principal - redirige selon le rôle de l'utilisateur
     */
    public function index(Request $request)
    {
        $user = $request->session()->get('user');

        if (!$user) {
            return redirect()->route('login');
        }

        return match ($user['role']) {
            'admin' => redirect()->route('admin.dashboard'),
            'responsable' => redirect()->route('responsable.dashboard'),
            'enseignant' => redirect()->route('enseignant.dashboard'),
            default => redirect()->route('login'),
        };
    }

    /**
     * Dashboard pour l'enseignant - vérifie le rôle
     */
    public function teacherDashboard(Request $request)
    {
        $user = Session::get('user');
        
        // Force redirect if not teacher
        if (!$user || ($user['role'] ?? '') !== 'enseignant') {
            return match ($user['role'] ?? '') {
                'admin' => redirect()->route('admin.dashboard'),
                'responsable' => redirect()->route('responsable.dashboard'),
                default => redirect()->route('login'),
            };
        }

        $bookings = $this->extractList($this->api->get('/api/bookings/my'));
        $rooms = $this->extractList($this->api->get('/api/rooms'));
        $equipment = $this->extractList($this->api->get('/api/equipment'));

        return view('dashboard.teacher', [
            'user' => $user,
            'bookings' => $bookings,
            'rooms' => $rooms ?? [],
            'equipment' => $equipment ?? [],
        ]);
    }

    /**
     * Dashboard pour le responsable - vérifie le rôle
     */
    public function managerDashboard(Request $request)
    {
        $user = Session::get('user');
        
        // Force redirect if not responsable
        if (!$user || ($user['role'] ?? '') !== 'responsable') {
            return match ($user['role'] ?? '') {
                'admin' => redirect()->route('admin.dashboard'),
                'enseignant' => redirect()->route('enseignant.dashboard'),
                default => redirect()->route('login'),
            };
        }

        $pendingBookings = $this->extractList($this->api->get('/api/bookings/pending'));
        $allBookings = $this->extractList($this->api->get('/api/bookings'));
        $rooms = $this->extractList($this->api->get('/api/rooms'));
        $equipment = $this->extractList($this->api->get('/api/equipment'));

        return view('dashboard.manager', [
            'user' => $user,
            'pendingBookings' => $pendingBookings,
            'allBookings' => $allBookings,
            'rooms' => $rooms ?? [],
            'equipment' => $equipment ?? [],
        ]);
    }

    /**
     * Dashboard pour l'administrateur - vérifie le rôle
     */
    public function adminDashboard(Request $request)
    {
        $user = Session::get('user');
        
        // Force redirect if not admin
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            return match ($user['role'] ?? '') {
                'responsable' => redirect()->route('responsable.dashboard'),
                'enseignant' => redirect()->route('enseignant.dashboard'),
                default => redirect()->route('login'),
            };
        }

        $users = $this->extractList($this->api->get('/api/users'));
        $rooms = $this->extractList($this->api->get('/api/rooms'));
        $equipment = $this->extractList($this->api->get('/api/equipment'));
        $allBookings = $this->extractList($this->api->get('/api/bookings'));

        return view('dashboard.admin', [
            'user' => $user,
            'users' => $users,
            'rooms' => $rooms ?? [],
            'equipment' => $equipment ?? [],
            'allBookings' => $allBookings,
        ]);
    }

    /**
     * Extrait une liste depuis plusieurs formats possibles de réponse API.
     */
    private function extractList($response): array
    {
        if (!is_array($response)) {
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
}