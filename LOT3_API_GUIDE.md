# RoomBook - Lot 3 (Backend Reservation)

Ce dossier contient une implementation complete du lot 3, compatible avec le lot 1 deja en place.

## Fonctionnalites couvertes

- Creation d'une demande de reservation (role `enseignant`)
- Consultation de ses demandes (role `enseignant`)
- Consultation des demandes en attente (role `responsable`)
- Acceptation ou refus d'une demande avec motif (role `responsable`)
- Annulation d'une reservation acceptee (role `enseignant`)
- Detection des conflits de salle sur reservations `accepted`
- Validation metier `ends_at > starts_at`
- Notification email a l'enseignant lors d'une decision (accepte/refuse)

## Endpoints Lot 3

Toutes ces routes exigent `auth:sanctum`.

### Enseignant

- `GET /api/bookings/my`
- `POST /api/bookings`
- `POST /api/bookings/{booking}/cancel`

Exemple body `POST /api/bookings`:

```json
{
  "room_id": 1,
  "starts_at": "2026-04-25 08:00:00",
  "ends_at": "2026-04-25 10:00:00",
  "purpose": "Cours de programmation web",
  "equipment": [
    { "equipment_id": 1, "quantity": 1 },
    { "equipment_id": 2, "quantity": 2 }
  ]
}
```

### Responsable

- `GET /api/bookings/pending`
- `POST /api/bookings/{booking}/decision`

Exemple body `POST /api/bookings/{booking}/decision`:

```json
{
  "status": "rejected",
  "rejection_reason": "Salle reservee pour un examen"
}
```

Ou:

```json
{
  "status": "accepted"
}
```

## Donnees et schema ajoutes

- `rooms` (minimum technique pour supporter le lot 3)
- `equipment` (minimum technique pour supporter le lot 3)
- `bookings`
- `booking_equipment` (pivot)

## Fichiers principaux

- `app/Http/Controllers/BookingController.php`
- `app/Mail/BookingStatusUpdatedMail.php`
- `resources/views/emails/booking-status-updated.blade.php`
- `database/migrations/2026_04_20_000005_create_bookings_table.php`
- `database/migrations/2026_04_20_000006_create_booking_equipment_table.php`
- `routes/api.php`

## Demarrage local rapide

1. `composer install`
2. Configurer `.env`
3. `php artisan migrate`
4. `php artisan db:seed`
5. `php artisan serve`

Les comptes de test du lot 1 sont conserves.
