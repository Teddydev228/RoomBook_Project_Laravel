<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statut de reservation mis a jour</title>
</head>
<body>
    <h2>Bonjour {{ $booking->user->name }},</h2>

    <p>Le statut de votre demande de reservation a change.</p>

    <ul>
        <li>Salle: {{ $booking->room->name }}</li>
        <li>Debut: {{ $booking->starts_at?->format('d/m/Y H:i') }}</li>
        <li>Fin: {{ $booking->ends_at?->format('d/m/Y H:i') }}</li>
        <li>Nouveau statut: <strong>{{ strtoupper($booking->status) }}</strong></li>
    </ul>

    @if($booking->status === 'rejected' && $booking->rejection_reason)
        <p><strong>Motif du refus:</strong> {{ $booking->rejection_reason }}</p>
    @endif

    <p>Equipe RoomBook</p>
</body>
</html>
