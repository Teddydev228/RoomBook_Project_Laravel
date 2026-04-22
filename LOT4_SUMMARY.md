# ✅ RoomBook - Lot 4 : Interface Utilisateur

## 📦 Livrables

### Structure complète du frontend Laravel

Ce dossier `roombook4` contient l'interface utilisateur complète pour le projet RoomBook.

## 🔥 Points clés

### ✅ Authentification
- Connexion/déconnexion avec token Sanctum
- Middleware d'authentification API (`auth.api`)
- Session PHP pour stockage utilisateur
- Redirection automatique selon le rôle

### ✅ Interface dark theme
- Layout principal `resources/views/layouts/app.blade.php`
- Style sombre inspiré HubSpot (couleurs #0d1117, #161b22)
- Bootstrap 5 + Icons
- Responsive design (mobile-first)

### ✅ Vues par rôle

| Rôle | Routes | Fichiers |
|------|--------|----------|
| Enseignant | `/enseignant/dashboard`, `/bookings`, `/bookings/create` | `dashboard/teacher.blade.php`, `bookings/index.blade.php`, `bookings/create.blade.php` |
| Responsable | `/responsable/dashboard`, `/bookings/pending` | `dashboard/manager.blade.php`, `bookings/pending.blade.php` |
| Administrateur | `/admin/dashboard`, `/users/*`, `/rooms/*`, `/equipment/*` | `dashboard/admin.blade.php`, `users/*`, `rooms/*`, `equipment/*` |

### ✅ Planning interactif
- Calendrier hebdomadaire généré en JavaScript
- Navigation semaine précédente/suivante
- Affichage des réservations acceptées
- Intégré aux dashboards enseignant & responsable

### ✅ CRUD complet (Admin)
- Gestion utilisateurs (création, édition, suppression, rôles)
- Gestion salles (nom, capacité, bâtiment, disponibilité)
- Gestion matériel (stock, disponibilité)

### ✅ Intégration API
- Service `ApiService` centralisé
- Gestion des erreurs avec fallback mock data
- Support mode développement sans API
- Configuration flexible via `.env`

### ✅ Mode Mock/Demo
- Données fictives pour développement isolé
- Activation via `USE_MOCK_DATA=true`
- Permet de tester l'UI sans backend

## 📁 Fichiers principaux

### Configuration
- `config/cors.php` : Autorise les requêtes cross-origin (frontend → backend)
- `config/roombook.php` : Paramètres API RoomBook
- `.env.example` : Variables d'environnement
- `bootstrap/app.php` : Middleware CORS + alias auth.api

### Contrôleurs
- `app/Http/Controllers/AuthController.php` : Login/logout
- `app/Http/Controllers/DashboardController.php` : 3 dashboards
- `app/Http/Controllers/BookingController.php` : Réservations + décision
- `app/Http/Controllers/RoomController.php` : CRUD salles
- `app/Http/Controllers/EquipmentController.php` : CRUD matériel
- `app/Http/Controllers/UserController.php` : CRUD utilisateurs

### Services
- `app/Services/ApiService.php` : Appels HTTP vers l'API backend

### Middleware
- `app/Http/Middleware/ApiAuthMiddleware.php` : Vérifie token API + session

### Vues (Blade)
```
resources/views/
├── layouts/app.blade.php         (layout principal)
├── auth/
│   ├── login.blade.php           (page de connexion)
│   └── unauthorized.blade.php    (accès refusé)
├── dashboard/
│   ├── teacher.blade.php         (enseignant)
│   ├── manager.blade.php         (responsable)
│   └── admin.blade.php           (admin)
├── bookings/
│   ├── index.blade.php           (liste de mes réservations)
│   ├── create.blade.php          (formulaire de création)
│   ├── pending.blade.php         (demandes en attente)
│   └── show.blade.php            (détails)
├── rooms/
│   ├── index.blade.php           (liste salles)
│   ├── create.blade.php          (formulaire création/édition)
│   └── edit.blade.php
├── equipment/
│   ├── index.blade.php           (liste matériel)
│   ├── create.blade.php          (formulaire création/édition)
│   └── edit.blade.php
└── users/
    ├── index.blade.php           (liste utilisateurs)
    ├── create.blade.php          (formulaire création/édition)
    └── edit.blade.php
```

## 🗄️ Modèles de données attendus (API)

### User
```json
{
  "id": 1,
  "name": "Jean Dupont",
  "email": "jean@exemple.fr",
  "role": "enseignant|responsable|admin",
  "created_at": "2025-01-01"
}
```

### Room
```json
{
  "id": 1,
  "name": "Salle A101",
  "capacity": 30,
  "building": "Bâtiment A",
  "is_available": true
}
```

### Equipment
```json
{
  "id": 1,
  "name": "Projecteur",
  "quantity": 10,
  "is_available": true
}
```

### Booking
```json
{
  "id": 1,
  "room_id": 1,
  "room": {"name": "Salle A101", "building": "Bâtiment A"},
  "starts_at": "2025-06-01 09:00:00",
  "ends_at": "2025-06-01 11:00:00",
  "purpose": "Cours de Laravel",
  "status": "pending|accepted|rejected|cancelled",
  "rejection_reason": null,
  "user": {"name": "Jean Dupont"},
  "equipment": []
}
```

## 🧪 Comptes de test

Mode mock (`USE_MOCK_DATA=true`):

| Email | Rôle | Mot de passe |
|-------|------|--------------|
| admin@test.com | Administrateur | (n'importe) |
| responsable@test.com | Responsable | (n'importe) |
| enseignant@test.com | Enseignant | (n'importe) |

Mode réel (`USE_MOCK_DATA=false`):
- Utiliser les comptes créés via l'API du backend
- Le mot de passe est celui défini dans le backend

## 🔧 Commandes utiles

```bash
# Lancer le serveur de développement
php artisan serve --port=8000

# Installer les dépendances
composer install
npm install

# Compiler les assets
npm run dev      # mode watch
npm run build    # production

# Vider les caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Lister les routes
php artisan route:list

# Tests
php artisan test --compact

# Générer l'URL d'accès
php artisan about
```

## 🚀 Démarrage rapide

1. **Lancer le backend (Lot 3)** sur le port 8001 :
   ```bash
   cd ../roombook-lot3
   php artisan serve --port=8001
   ```

2. **Lancer le frontend (Lot 4)** sur le port 8000 :
   ```bash
   php artisan serve --port=8000
   ```

3. **Accéder** à http://localhost:8000

4. **Se connecter** avec un compte test

## ⚠️ Configuration backend requise

Le Lot 4 nécessite que le backend (Lots 1+2+3) soit opérationnel :

- API accessible sur `ROOMBOOK_API_URL`
- Authentification Sanctum fonctionnelle
- Endpoints CRUD utilisateurs, salles, matériel
- Endpoints réservations avec gestion des conflits
- Envoi d'emails configuré (optionnel)

Voir la documentation des Lots 1, 2 et 3 pour l'installation du backend.

## 📖 Documentation

- `README_FR.md` : Documentation détaillée du Lot 4
- `PROJET.md` : Documentation globale du projet RoomBook
- `../RoomBook1/README.md` : Documentation Lot 1
- `../RoomBook2/README.md` : Documentation Lot 2
- `../roombook-lot3/LOT3_API_GUIDE.md` : Guide API Lot 3

## 🎯 Statut du projet

✅ **Lot 4 terminé** - Interface utilisateur complète et fonctionnelle

### Couverture fonctionnelle

| Fonctionnalité | Enseignant | Responsable | Admin |
|---------------|------------|-------------|-------|
| Consultation planning | ✅ | ✅ | ✅ |
| Créer réservation | ✅ | ❌ | ❌ |
| Annuler réservation | ✅ | ❌ | ❌ |
| Traiter demandes | ❌ | ✅ | ❌ |
| Gérer utilisateurs | ❌ | ❌ | ✅ |
| Gérer salles | ❌ | ❌ | ✅ |
| Gérer matériel | ❌ | ❌ | ✅ |

### Technologies utilisées
- **Backend PHP** : Laravel 13
- **Frontend** : Blade templates, Bootstrap 5, Vanilla JS
- **API** : RESTful avec Sanctum authentication
- **Styles** : CSS custom dark theme
- **Icônes** : Bootstrap Icons

---

**RoomBook - Projet académique 2025-2026**  
Lot 4 : Interface Utilisateur & Vues Planning
