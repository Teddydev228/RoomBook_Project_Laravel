<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Enregistrement du middleware de vérification des rôles
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Sanctum comme garde d'auth par défaut pour les API
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Retourne les erreurs d'authentification en JSON
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Non authentifié. Veuillez vous connecter.',
                ], 401);
            }
        });

        // Retourne les erreurs de validation en JSON
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Erreur de validation.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Retourne les 404 en JSON
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Ressource introuvable.',
                ], 404);
            }
        });
    })->create();
