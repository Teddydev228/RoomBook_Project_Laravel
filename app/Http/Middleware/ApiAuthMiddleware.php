<?php

namespace App\Http\Middleware;

use App\Services\ApiService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    protected ApiService $api;

    /**
     * Constructeur du middleware d'authentification API
     *
     * @param ApiService $api Service de communication avec l'API backend
     */
    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Gère une requête entrante et vérifie l'authentification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupération du token depuis la session
        $token = Session::get('api_token');

        if (!$token) {
            // Pas de token présent : l'utilisateur doit se connecter
            return redirect()->route('login');
        }

        // Définition du token dans le service API
        $this->api->setToken($token);

        // Priorité au user en session - éviter de le remplacer par des données API potentiellement incorrectes
        $sessionUser = Session::get('user');
        
        if (!$sessionUser || !isset($sessionUser['role'])) {
            // Pas de user en session - try de récupérer via API
            $user = $this->api->getAuthUser();
            
            if (!$user) {
                // Token invalide ou expiré : déconnexion
                $this->api->clearToken();
                Session::forget('api_token');
                return redirect()->route('login');
            }
            
            // Sauvegarde le user en session
            Session::put('user', $user);
            $sessionUser = $user;
        }

        // Utiliser le user de la session qui est correct
        $user = $sessionUser;

        // Partage des données utilisateur avec toutes les vues
        View::share('user', $user);

        return $next($request);
    }
}
