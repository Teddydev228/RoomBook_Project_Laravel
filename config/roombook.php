<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration API RoomBook
    |--------------------------------------------------------------------------
    |
    | Ce fichier configure les paramètres de connexion à l'API RoomBook
    | pour les Lots 1, 2 et 3 (Backend).
    |
    */

    // URL de base de l'API backend (Changez selon votre configuration)
    'base_url' => env('ROOMBOOK_API_URL', 'http://localhost:8001'),

    // Timeout des requêtes (en secondes)
    'timeout' => env('ROOMBOOK_API_TIMEOUT', 30),

    // Nombre maximum de tentatives de reconnexion
    'retry_attempts' => env('ROOMBOOK_API_RETRIES', 3),

    // Activer le mode mock/demo (sans API réelle)
    'use_mock' => env('USE_MOCK_DATA', false),

    // Cache des données (en minutes)
    'cache_ttl' => env('ROOMBOOK_CACHE_TTL', 5),

];
