<?php

namespace App\Http\Controllers;

use App\Mail\BookingStatusUpdatedMail;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function myBookings(Request $request): JsonResponse
    {
        $bookings = Booking::with(['room', 'equipment'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $bookings,
            'total' => $bookings->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')],
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'purpose' => 'required|string|max:1000',
            'equipment' => 'sometimes|array',
            'equipment.*.equipment_id' => ['required', 'integer', Rule::exists('equipment', 'id')],
            'equipment.*.quantity' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        if (! $room->is_available) {
            return response()->json([
                'message' => 'Cette salle n est pas disponible.',
            ], 422);
        }

        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt = Carbon::parse($validated['ends_at']);

        if ($this->hasAcceptedConflict($room->id, $startsAt, $endsAt)) {
            return response()->json([
                'message' => 'Conflit detecte: une reservation acceptee existe deja pour cette salle sur ce creneau.',
            ], 409);
        }

        $syncData = $this->buildEquipmentSyncData($validated['equipment'] ?? []);

        $booking = DB::transaction(function () use ($request, $validated, $startsAt, $endsAt, $syncData) {
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'room_id' => $validated['room_id'],
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'purpose' => $validated['purpose'],
                'status' => 'pending',
            ]);

            if (! empty($syncData)) {
                $booking->equipment()->sync($syncData);
            }

            return $booking->load(['room', 'equipment', 'user']);
        });

        return response()->json([
            'message' => 'Demande de reservation creee avec succes.',
            'data' => $booking,
        ], 201);
    }

    public function pending(): JsonResponse
    {
        $bookings = Booking::with(['user', 'room', 'equipment'])
            ->where('status', 'pending')
            ->orderBy('starts_at')
            ->get();

        return response()->json([
            'data' => $bookings,
            'total' => $bookings->count(),
        ]);
    }

    public function decide(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string|max:1000',
        ]);

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Seules les demandes en attente peuvent etre traitees.',
            ], 422);
        }

        if ($validated['status'] === 'accepted' && $this->hasAcceptedConflict(
            $booking->room_id,
            $booking->starts_at,
            $booking->ends_at,
            $booking->id
        )) {
            return response()->json([
                'message' => 'Impossible d accepter: conflit detecte avec une reservation deja acceptee.',
            ], 409);
        }

        $booking->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['status'] === 'rejected'
                ? $validated['rejection_reason']
                : null,
            'decided_by' => $request->user()->id,
            'decided_at' => now(),
        ]);

        $booking->load(['user', 'room', 'equipment', 'decidedBy']);
        Mail::to($booking->user->email)->send(new BookingStatusUpdatedMail($booking));

        return response()->json([
            'message' => 'Decision enregistree avec succes.',
            'data' => $booking,
        ]);
    }

    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Vous ne pouvez annuler que vos propres reservations.',
            ], 403);
        }

        if ($booking->status !== 'accepted') {
            return response()->json([
                'message' => 'Seules les reservations acceptees peuvent etre annulees.',
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'rejection_reason' => null,
        ]);

        return response()->json([
            'message' => 'Reservation annulee avec succes.',
            'data' => $booking->load(['room', 'equipment']),
        ]);
    }

    private function hasAcceptedConflict(
        int $roomId,
        Carbon $startsAt,
        Carbon $endsAt,
        ?int $ignoreBookingId = null
    ): bool {
        return Booking::where('room_id', $roomId)
            ->where('status', 'accepted')
            ->when($ignoreBookingId, fn ($query) => $query->where('id', '!=', $ignoreBookingId))
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query
                    ->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->exists();
    }

    private function buildEquipmentSyncData(array $items): array
    {
        $syncData = [];

        foreach ($items as $item) {
            $equipment = Equipment::findOrFail($item['equipment_id']);

            if (! $equipment->is_available || $item['quantity'] > $equipment->quantity) {
                throw ValidationException::withMessages([
                    'equipment' => ["Quantite invalide ou materiel indisponible pour {$equipment->name}."],
                ]);
            }

            $syncData[$equipment->id] = ['quantity' => $item['quantity']];
        }

        return $syncData;
    }
}
