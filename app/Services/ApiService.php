<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ApiService
{
    protected string $baseUrl;
    protected string $token;
    protected bool $useMock;

    public function __construct()
    {
        $this->baseUrl = config('app.api_url', env('ROOMBOOK_API_URL', 'http://localhost:8001'));
        $sessionToken = Session::get('api_token');
        $this->token = is_string($sessionToken) ? $sessionToken : '';
        $this->useMock = env('USE_MOCK_DATA', false);
    }

    /**
     * Définit le jeton API pour les requêtes authentifiées
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
        Session::put('api_token', $token);
    }

    /**
     * Supprime le jeton API (déconnexion)
     */
    public function clearToken(): void
    {
        $this->token = '';
        Session::forget('api_token');
    }

    /**
     * Récupère le jeton API depuis la session
     */
    public function getToken(): ?string
    {
        if ($this->token) {
            return $this->token;
        }
        return Session::get('api_token');
    }

    /**
     * Récupère les informations de l'utilisateur authentifié
     */
    public function getAuthUser(): ?array
    {
        return $this->get('/api/me');
    }

    /**
     * Effectue une requête GET vers l'API
     */
    public function get(string $endpoint, array $query = []): ?array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * Effectue une requête POST vers l'API
     */
    public function post(string $endpoint, array $data = []): ?array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Effectue une requête PUT/PATCH vers l'API
     */
    public function put(string $endpoint, array $data = []): ?array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * Effectue une requête DELETE vers l'API
     */
    public function delete(string $endpoint): ?array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Exécute une requête HTTP vers l'API backend
     */
    protected function request(string $method, string $endpoint, array $options = []): ?array
    {
        // Si le mode mock est activé, retourne des données simulées immédiatement
        if ($this->useMock) {
            return $this->handleMockRequest($method, $endpoint, $options);
        }

        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        try {
            $response = Http::withHeaders($headers)
                ->send($method, $url, $options);

            if ($response->successful()) {
                return $response->json();
            }

            // Si l'API retourne une erreur 404/500, utilise les données mock en mode debug
            if (config('app.debug') && $response->status() >= 400) {
                logger()->warning('Échec de la requête API, utilisation des données mock', [
                    'méthode' => $method,
                    'url' => $url,
                    'statut' => $response->status(),
                ]);
                return $this->handleMockRequest($method, $endpoint, $options);
            }

            logger()->error('Échec de la requête API', [
                'méthode' => $method,
                'url' => $url,
                'statut' => $response->status(),
                'corps' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            // Erreur réseau - retourne les données mock en mode debug/local
            if (config('app.debug')) {
                logger()->warning('Connexion API échouée, utilisation des données mock', [
                    'méthode' => $method,
                    'url' => $url,
                    'erreur' => $e->getMessage(),
                ]);
                return $this->handleMockRequest($method, $endpoint, $options);
            }

            logger()->error('Exception API', [
                'méthode' => $method,
                'url' => $url,
                'erreur' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Gère les requêtes en mode mock (simulation)
     */
    protected function handleMockRequest(string $method, string $endpoint, array $options): ?array
    {
        // Initialiser les données mutables en session si nécessaire
        $this->ensureMockState();

        // Requêtes GET
        if ($method === 'GET') {
            // Salles
            if ($endpoint === '/api/rooms') {
                return $this->getMockState('rooms');
            }

            if (preg_match('#^/api/rooms/(\d+)$#', $endpoint, $matches)) {
                $id = (int)$matches[1];
                foreach ($this->getMockState('rooms') as $room) {
                    if ($room['id'] == $id) {
                        return $room;
                    }
                }
                return null;
            }

            // Matériel
            if ($endpoint === '/api/equipment') {
                return $this->getMockState('equipment');
            }

            // Utilisateurs
            if ($endpoint === '/api/users') {
                return $this->getMockState('users');
            }

            if (preg_match('#^/api/equipment/(\d+)$#', $endpoint, $matches)) {
                $id = (int)$matches[1];
                foreach ($this->getMockState('equipment') as $equip) {
                    if ($equip['id'] == $id) {
                        return $equip;
                    }
                }
                return null;
            }

            // Endpoints statiques
            if ($endpoint === '/api/bookings/my') {
                $bookings = $this->getMockState('bookings');
                return [
                    'data' => $bookings,
                    'total' => count($bookings),
                ];
            }

            if ($endpoint === '/api/bookings') {
                return $this->getMockState('bookings');
            }

            if ($endpoint === '/api/bookings/pending') {
                $pending = $this->getMockState('pending_bookings');
                return [
                    'data' => $pending,
                    'total' => count($pending),
                ];
            }

            return $this->getStaticMockData($endpoint);
        }

        // Requêtes POST
        if ($method === 'POST') {
            if ($endpoint === '/api/bookings') {
                $data = $options['json'] ?? [];
                $bookings = $this->getMockState('bookings');
                $pending = $this->getMockState('pending_bookings');
                $user = Session::get('user', [
                    'id' => 2,
                    'name' => 'Jean Dupont',
                    'email' => 'enseignant@test.com',
                    'role' => 'enseignant',
                ]);

                $roomId = (int) ($data['room_id'] ?? 0);
                $rooms = $this->getMockState('rooms');
                $selectedRoom = null;
                foreach ($rooms as $room) {
                    if ((int) ($room['id'] ?? 0) === $roomId) {
                        $selectedRoom = $room;
                        break;
                    }
                }

                $newId = $this->getNextId($bookings);
                $newBooking = [
                    'id' => $newId,
                    'room_id' => $roomId,
                    'room' => [
                        'name' => $selectedRoom['name'] ?? 'Salle inconnue',
                        'building' => $selectedRoom['building'] ?? '',
                        'capacity' => $selectedRoom['capacity'] ?? 0,
                    ],
                    'user' => [
                        'name' => $user['name'] ?? 'Utilisateur',
                        'email' => $user['email'] ?? '',
                    ],
                    'starts_at' => $data['starts_at'] ?? now()->addHours(2)->toDateTimeString(),
                    'ends_at' => $data['ends_at'] ?? now()->addHours(3)->toDateTimeString(),
                    'purpose' => $data['purpose'] ?? '',
                    'status' => 'pending',
                    'equipment' => [],
                ];

                $bookings[] = $newBooking;
                $pending[] = $newBooking;
                $this->putMockState('bookings', $bookings);
                $this->putMockState('pending_bookings', $pending);

                return [
                    'success' => true,
                    'data' => $newBooking,
                ];
            }

            if (preg_match('#^/api/bookings/(\d+)/decision$#', $endpoint, $matches)) {
                $id = (int)$matches[1];
                $status = $options['json']['status'] ?? null;
                if (! in_array($status, ['accepted', 'rejected'], true)) {
                    return null;
                }

                $pending = $this->getMockState('pending_bookings');
                $pending = array_values(array_filter($pending, fn($booking) => (int)($booking['id'] ?? 0) !== $id));
                $this->putMockState('pending_bookings', $pending);

                $bookings = $this->getMockState('bookings');
                $equipment = $this->getMockState('equipment');
                $updatedBooking = null;

                foreach ($bookings as &$booking) {
                    if ((int)($booking['id'] ?? 0) === $id) {
                        $booking['status'] = $status;

                        // Update equipment quantities if booking has equipment
                        if (!empty($booking['equipment']) && is_array($booking['equipment'])) {
                            foreach ($booking['equipment'] as &$eq) {
                                $eqId = (int)($eq['id'] ?? $eq['equipment_id'] ?? 0);
                                $quantity = (int)($eq['pivot']['quantity'] ?? $eq['quantity'] ?? 1);

                                foreach ($equipment as &$eqItem) {
                                    if ((int)($eqItem['id'] ?? 0) === $eqId) {
                                        if ($status === 'accepted') {
                                            $eqItem['quantity'] = max(0, (int)($eqItem['quantity'] ?? 0) - $quantity);
                                        } else {
                                            $eqItem['quantity'] = (int)($eqItem['quantity'] ?? 0) + $quantity;
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                        $updatedBooking = $booking;
                        break;
                    }
                }
                $this->putMockState('bookings', $bookings);
                $this->putMockState('equipment', $equipment);

                return [
                    'success' => true,
                    'status' => $status,
                    'data' => $updatedBooking,
                ];
            }

            if (preg_match('#^/api/bookings/(\d+)/cancel$#', $endpoint, $matches)) {
                $id = (int)$matches[1];
                $user = Session::get('user', [
                    'id' => 2,
                    'name' => 'Jean Dupont',
                    'email' => 'enseignant@test.com',
                    'role' => 'enseignant',
                ]);
                $userId = (int)($user['id'] ?? 0);

                $bookings = $this->getMockState('bookings');
                $pendingBookings = $this->getMockState('pending_bookings');
                $found = false;
                $updated = false;

                foreach ($bookings as $idx => $booking) {
                    if ((int)($booking['id'] ?? 0) === $id) {
                        $bookingUserId = (int)($booking['user_id'] ?? 0);

                        // If no user_id in booking, allow cancel for testing
                        if ($bookingUserId === 0) {
                            $bookingUserId = $userId;
                        }

                        if ($bookingUserId !== $userId) {
                            return ['success' => false, 'message' => 'Vous ne pouvez annuler que vos propres réservations.'];
                        }

                        if ($booking['status'] !== 'accepted') {
                            return ['success' => false, 'message' => 'Seules les réservations acceptées peuvent être annulées.'];
                        }

                        $bookings[$idx]['status'] = 'cancelled';
                        $found = true;
                        $updated = true;
                        break;
                    }
                }

                if (!$found) {
                    return ['success' => false, 'message' => 'Réservation non trouvée.'];
                }

                if ($updated) {
                    $this->putMockState('bookings', $bookings);
                    // Remove from pending bookings as well
                    $filteredPending = array_values(array_filter($pendingBookings, fn($b) => (int)($b['id'] ?? 0) !== $id));
                    $this->putMockState('pending_bookings', $filteredPending);
                }

                return ['success' => true, 'message' => 'Réservation annulée avec succès.'];
            }

            if ($endpoint === '/api/rooms') {
                $data = $options['json'] ?? [];
                $rooms = $this->getMockState('rooms');
                $newId = $this->getNextId($rooms);
                $newRoom = array_merge([
                    'id' => $newId,
                    'name' => '',
                    'capacity' => 0,
                    'building' => '',
                    'is_available' => true,
                ], $data);
                $newRoom['capacity'] = (int)$newRoom['capacity'];
                $newRoom['is_available'] = (bool)$newRoom['is_available'];
                $rooms[] = $newRoom;
                $this->putMockState('rooms', $rooms);
                return $newRoom;
            }

            if ($endpoint === '/api/equipment') {
                $data = $options['json'] ?? [];
                $equipments = $this->getMockState('equipment');
                $newId = $this->getNextId($equipments);
                $newEquip = array_merge([
                    'id' => $newId,
                    'name' => '',
                    'quantity' => 0,
                    'is_available' => true,
                ], $data);
                $newEquip['quantity'] = (int)$newEquip['quantity'];
                $newEquip['is_available'] = (bool)$newEquip['is_available'];
                $equipments[] = $newEquip;
                $this->putMockState('equipment', $equipments);
                return $newEquip;
            }

            if ($endpoint === '/api/users') {
                $data = $options['json'] ?? [];
                $users = $this->getMockState('users');
                $newId = $this->getNextId($users);
                $newUser = [
                    'id' => $newId,
                    'name' => $data['name'] ?? '',
                    'email' => $data['email'] ?? '',
                    'role' => $data['role'] ?? 'enseignant',
                    'created_at' => now()->toDateTimeString(),
                ];
                $users[] = $newUser;
                $this->putMockState('users', $users);
                return $newUser;
            }
        }

        // Requêtes PUT
        if ($method === 'PUT') {
            if (preg_match('#^/api/rooms/(\d+)$#', $endpoint, $matches)) {
                $id = (int)$matches[1];
                $data = $options['json'] ?? [];
                $rooms = $this->getMockState('rooms');
                foreach ($rooms as &$room) {
                    if ($room['id'] == $id) {
                        $room = array_merge($room, $data);
                        if (isset($room['capacity'])) {
                            $room['capacity'] = (int)$room['capacity'];
                        }
                        if (isset($room['is_available'])) {
                            $room['is_available'] = (bool)$room['is_available'];
                        }
                        $this->putMockState('rooms', $rooms);
                        return $room;
                    }
                }
                return null;
            }

            if (preg_match('#^/api/equipment/(\d+)$#', $endpoint, $matches)) {
                $id = (int)$matches[1];
                $data = $options['json'] ?? [];
                $equipments = $this->getMockState('equipment');
                foreach ($equipments as &$equip) {
                    if ($equip['id'] == $id) {
                        $equip = array_merge($equip, $data);
                        if (isset($equip['quantity'])) {
                            $equip['quantity'] = (int)$equip['quantity'];
                        }
                        if (isset($equip['is_available'])) {
                            $equip['is_available'] = (bool)$equip['is_available'];
                        }
                        $this->putMockState('equipment', $equipments);
                        return $equip;
                    }
                }
                return null;
            }
        }

        // Requêtes DELETE
        if ($method === 'DELETE') {
            if (preg_match('#^/api/rooms/(\d+)$#', $endpoint, $matches)) {
                $id = (int)$matches[1];
                $rooms = $this->getMockState('rooms');
                $rooms = array_values(array_filter($rooms, fn($r) => $r['id'] != $id));
                $this->putMockState('rooms', $rooms);
                return ['message' => 'Room deleted'];
            }

            if (preg_match('#^/api/equipment/(\d+)$#', $endpoint, $matches)) {
                $id = (int)$matches[1];
                $equipments = $this->getMockState('equipment');
                $equipments = array_values(array_filter($equipments, fn($e) => $e['id'] != $id));
                $this->putMockState('equipment', $equipments);
                return ['message' => 'Equipment deleted'];
            }
        }

        return null;
    }

    /**
     * Retourne les données initiales pour les salles (mock)
     */
    protected function getInitialRooms(): array
    {
        return [
            ['id' => 1, 'name' => 'Salle A101', 'capacity' => 30, 'building' => 'Bâtiment A', 'is_available' => true],
            ['id' => 2, 'name' => 'Salle B202', 'capacity' => 20, 'building' => 'Bâtiment B', 'is_available' => true],
            ['id' => 3, 'name' => 'Amphithéâtre', 'capacity' => 100, 'building' => 'Bâtiment C', 'is_available' => true],
            ['id' => 4, 'name' => 'Salle TP1', 'capacity' => 15, 'building' => 'Bâtiment A', 'is_available' => true],
            ['id' => 5, 'name' => 'Salle réunion', 'capacity' => 10, 'building' => 'Bâtiment B', 'is_available' => true],
        ];
    }

    /**
     * Retourne les données initiales pour les utilisateurs (mock)
     */
    protected function getInitialUsers(): array
    {
        return [
            ['id' => 1, 'name' => 'Admin Principal', 'email' => 'admin@test.com', 'role' => 'admin', 'created_at' => '2025-01-01 00:00:00'],
            ['id' => 2, 'name' => 'Jean Dupont', 'email' => 'enseignant@test.com', 'role' => 'enseignant', 'created_at' => '2025-01-01 00:00:00'],
        ];
    }

    /**
     * Retourne les données initiales pour le matériel (mock)
     */
    protected function getInitialEquipment(): array
    {
        return [
            ['id' => 1, 'name' => 'Vidéoprojecteur', 'quantity' => 10, 'is_available' => true],
            ['id' => 2, 'name' => 'Tableau blanc', 'quantity' => 5, 'is_available' => true],
            ['id' => 3, 'name' => 'Ordinateur portable', 'quantity' => 3, 'is_available' => true],
            ['id' => 4, 'name' => 'Microphone sans fil', 'quantity' => 8, 'is_available' => true],
            ['id' => 5, 'name' => 'Webcam HD', 'quantity' => 6, 'is_available' => true],
        ];
    }

    /**
     * Retourne les demandes en attente initiales (mock)
     */
    protected function getInitialPendingBookings(): array
    {
        return [
            [
                'id' => 2,
                'user' => ['name' => 'Jean Dupont', 'email' => 'enseignant@test.com'],
                'room' => ['name' => 'Salle B202', 'building' => 'Bâtiment B', 'capacity' => 20],
                'starts_at' => '2025-06-02 14:00:00',
                'ends_at' => '2025-06-02 16:00:00',
                'purpose' => 'Réunion équipe projet',
                'status' => 'pending',
                'equipment' => [],
            ],
        ];
    }

    /**
     * Retourne les réservations initiales (mock)
     */
    protected function getInitialBookings(): array
    {
        return [
            [
                'id' => 1,
                'user_id' => 2,
                'room_id' => 1,
                'room' => ['name' => 'Salle A101', 'building' => 'Bâtiment A'],
                'starts_at' => '2025-06-01 09:00:00',
                'ends_at' => '2025-06-01 11:00:00',
                'purpose' => 'Cours de Laravel',
                'status' => 'accepted',
                'equipment' => [
                    ['id' => 1, 'name' => 'Vidéoprojecteur', 'pivot' => ['quantity' => 1]],
                ],
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'room_id' => 2,
                'room' => ['name' => 'Salle B202', 'building' => 'Bâtiment B'],
                'starts_at' => '2025-06-02 14:00:00',
                'ends_at' => '2025-06-02 16:00:00',
                'purpose' => 'Réunion équipe',
                'status' => 'pending',
                'equipment' => [],
            ],
        ];
    }

    /**
     * Retourne des données statiques pour les endpoints qui ne changent pas (mock)
     */
    protected function getStaticMockData(string $endpoint): ?array
    {
        if ($endpoint === '/api/me') {
            $sessionUser = Session::get('user');
            if (is_array($sessionUser) && isset($sessionUser['role'])) {
                return $sessionUser;
            }

            return [
                'id' => 1,
                'name' => 'Jean Dupont',
                'email' => 'enseignant@test.com',
                'role' => 'enseignant',
            ];
        }

        $static = [
            '/api/users' => [
                ['id' => 1, 'name' => 'Admin User', 'email' => 'admin@test.com', 'role' => 'admin', 'created_at' => '2025-01-01'],
                ['id' => 2, 'name' => 'Jean Dupont', 'email' => 'enseignant@test.com', 'role' => 'enseignant', 'created_at' => '2025-01-02'],
                ['id' => 3, 'name' => 'Marie Martin', 'email' => 'responsable@test.com', 'role' => 'responsable', 'created_at' => '2025-01-03'],
            ],
            '/api/bookings/my' => [
                'data' => [
                    [
                        'id' => 1,
                        'room_id' => 1,
                        'room' => ['name' => 'Salle A101', 'building' => 'Bâtiment A'],
                        'starts_at' => '2025-06-01 09:00:00',
                        'ends_at' => '2025-06-01 11:00:00',
                        'purpose' => 'Cours de Laravel',
                        'status' => 'accepted',
                        'equipment' => [],
                    ],
                    [
                        'id' => 2,
                        'room_id' => 2,
                        'room' => ['name' => 'Salle B202', 'building' => 'Bâtiment B'],
                        'starts_at' => '2025-06-02 14:00:00',
                        'ends_at' => '2025-06-02 16:00:00',
                        'purpose' => 'Réunion équipe',
                        'status' => 'pending',
                        'equipment' => [],
                    ],
                ],
                'total' => 2,
            ],
            '/api/bookings/pending' => [
                'data' => [
                    [
                        'id' => 2,
                        'user' => ['name' => 'Jean Dupont', 'email' => 'enseignant@test.com'],
                        'room' => ['name' => 'Salle B202', 'building' => 'Bâtiment B', 'capacity' => 20],
                        'starts_at' => '2025-06-02 14:00:00',
                        'ends_at' => '2025-06-02 16:00:00',
                        'purpose' => 'Réunion équipe projet',
                        'status' => 'pending',
                        'equipment' => [],
                    ],
                ],
                'total' => 1,
            ],
        ];

        return $static[$endpoint] ?? null;
    }

    /**
     * Génère le prochain ID pour une collection
     */
    protected function getNextId(array $items): int
    {
        if (empty($items)) {
            return 1;
        }
        $ids = array_column($items, 'id');
        return max($ids) + 1;
    }

    /**
     * Retourne l'URL de base de l'API
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Initialise le cache mock global.
     */
    protected function ensureMockState(): void
    {
        $this->getMockState('rooms');
        $this->getMockState('equipment');
        $this->getMockState('users');
        $this->getMockState('pending_bookings');
        $this->getMockState('bookings');
    }

    /**
     * Lit un état mock partagé.
     */
    protected function getMockState(string $key): array
    {
        $sessionKey = 'mock_' . $key;

        return Session::get($sessionKey, match ($key) {
            'rooms' => $this->getInitialRooms(),
            'equipment' => $this->getInitialEquipment(),
            'users' => $this->getInitialUsers(),
            'pending_bookings' => $this->getInitialPendingBookings(),
            'bookings' => $this->getInitialBookings(),
            default => [],
        });
    }

    /**
     * Sauvegarde un état mock partagé.
     */
    protected function putMockState(string $key, array $value): void
    {
        Session::put('mock_' . $key, $value);
    }
}
