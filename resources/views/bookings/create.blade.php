@extends('layouts.app')

@section('title', 'Nouvelle Réservation')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle Demande de Réservation
                </h4>
            </div>
            <div class="card-body">
                <form id="bookingCreateForm" method="POST" action="{{ route('bookings.store') }}">
                    @csrf

                    <!-- Room Selection -->
                    <div class="mb-4">
                        <label class="form-label">Salle <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select" required>
                            <option value="">-- Choisir une salle --</option>
                            @foreach($rooms as $room)
                                @php
                                    $roomId = $room['id'] ?? null;
                                    $roomName = $room['name'] ?? 'Salle sans nom';
                                    $roomCapacity = $room['capacity'] ?? '?';
                                    $roomBuilding = $room['building'] ?? 'Bâtiment non défini';
                                    $roomAvailable = (bool) ($room['is_available'] ?? $room['available'] ?? true);
                                @endphp
                                <option value="{{ $roomId }}" {{ old('room_id') == $roomId ? 'selected' : '' }} {{ $roomAvailable ? '' : 'disabled' }}>
                                    {{ $roomName }} ({{ $roomCapacity }} pers.) - {{ $roomBuilding }}
                                    @if(!$roomAvailable)
                                        [INDISPONIBLE]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date/Time -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Date et heure de début <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   name="starts_at" 
                                   id="starts_at"
                                   class="form-control" 
                                   value="{{ old('starts_at') }}" 
                                   required>
                            <small class="text-secondary">Minimum 2 heures à partir de maintenant.</small>
                            @error('starts_at')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date et heure de fin <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   name="ends_at" 
                                   id="ends_at"
                                   class="form-control" 
                                   value="{{ old('ends_at') }}" 
                                   required>
                            <small class="text-secondary">Durée maximale: 4 heures.</small>
                            @error('ends_at')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div class="mb-4">
                        <label class="form-label">Objet de la réservation <span class="text-danger">*</span></label>
                        <textarea name="purpose" 
                                  class="form-control" 
                                  rows="3" 
                                  required
                                  placeholder="Décrivez le motif de votre réservation...">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Equipment Selection -->
                    <div class="mb-4">
                        <label class="form-label">Matériel supplémentaire (optionnel)</label>
                        <div class="row">
                            @foreach($equipment as $index => $item)
                                @php
                                    $itemId = $item['id'] ?? $index;
                                    $itemName = $item['name'] ?? 'Matériel sans nom';
                                    $itemQuantity = (int) ($item['quantity'] ?? 0);
                                    $itemAvailable = (bool) ($item['is_available'] ?? $item['available'] ?? true);
                                    $canSelect = $itemAvailable && $itemQuantity > 0;
                                @endphp
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input equipment-checkbox" 
                                               type="checkbox" 
                                               id="equipment_{{ $itemId }}"
                                               name="equipment[{{ $index }}][equipment_id]" 
                                               value="{{ $itemId }}"
                                               data-equipment-id="{{ $itemId }}"
                                               @if(!$canSelect) disabled @endif>
                                        <label class="form-check-label" for="equipment_{{ $itemId }}">
                                            {{ $itemName }}
                                            <small class="text-secondary">(disponible: {{ $itemQuantity }})</small>
                                            @if(!$canSelect)
                                                <span class="badge bg-secondary ms-1">Indisponible</span>
                                            @endif
                                        </label>
                                    </div>
                                    <div id="qty_{{ $itemId }}" class="ms-4 mt-1" style="display: none;">
                                        <input type="number" 
                                               id="equipment_qty_{{ $itemId }}"
                                               name="equipment[{{ $index }}][quantity]" 
                                               class="form-control form-control-sm" 
                                               style="width: 100px; display: inline-block;"
                                               min="1" 
                                               max="{{ max(1, $itemQuantity) }}" 
                                               value="1"
                                               disabled
                                               placeholder="Qté">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-secondary">
                            <i class="bi bi-info-circle me-1"></i>
                            Le matériel est attribué sous réserve de disponibilité.
                        </small>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Soumettre la demande
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Box -->
        <div class="card mt-4 border-info">
            <div class="card-body">
                <h6 class="card-title text-info">
                    <i class="bi bi-info-circle me-2"></i>Informations
                </h6>
                <ul class="mb-0">
                    <li>Les réservations doivent être faites au moins 2 heures à l'avance</li>
                    <li>La durée maximale d'une réservation est de 4 heures</li>
                    <li>Vous recevrez un email de confirmation lors de la validation</li>
                    <li>En cas de conflit, votre réservation pourra être refusée</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toDateTimeLocalValue(date) {
    const tzOffset = date.getTimezoneOffset() * 60000;
    return new Date(date.getTime() - tzOffset).toISOString().slice(0, 16);
}

function applyBookingTimeRules() {
    const startsAt = document.getElementById('starts_at');
    const endsAt = document.getElementById('ends_at');

    if (!startsAt || !endsAt) {
        return;
    }

    const now = new Date();
    const minStart = new Date(now.getTime() + 2 * 60 * 60 * 1000);
    const minStartValue = toDateTimeLocalValue(minStart);
    startsAt.min = minStartValue;

    if (!startsAt.value || new Date(startsAt.value) < minStart) {
        startsAt.value = minStartValue;
    }

    if (startsAt.value) {
        const startDate = new Date(startsAt.value);
        const minEnd = new Date(startDate.getTime() + 30 * 60 * 1000);
        const maxEnd = new Date(startDate.getTime() + 4 * 60 * 60 * 1000);
        endsAt.min = toDateTimeLocalValue(minEnd);
        endsAt.max = toDateTimeLocalValue(maxEnd);

        if (!endsAt.value || new Date(endsAt.value) < minEnd) {
            endsAt.value = toDateTimeLocalValue(minEnd);
        }

        if (endsAt.value) {
            const endDate = new Date(endsAt.value);
            if (endDate > maxEnd) {
                endsAt.value = toDateTimeLocalValue(maxEnd);
            }
        }
    }
}

function toggleQuantity(equipmentId) {
    const checkbox = document.getElementById('equipment_' + equipmentId);
    const qtyDiv = document.getElementById('qty_' + equipmentId);
    const qtyInput = document.getElementById('equipment_qty_' + equipmentId);
    qtyDiv.style.display = checkbox.checked ? 'block' : 'none';

    if (qtyInput) {
        qtyInput.disabled = !checkbox.checked;
        qtyInput.required = checkbox.checked;
        if (!checkbox.checked) {
            qtyInput.value = 1;
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const startsAt = document.getElementById('starts_at');
    const endsAt = document.getElementById('ends_at');
    const form = document.getElementById('bookingCreateForm');
    const equipmentCheckboxes = document.querySelectorAll('.equipment-checkbox');

    equipmentCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const equipmentId = this.getAttribute('data-equipment-id');
            if (equipmentId) {
                toggleQuantity(equipmentId);
            }
        });
    });

    applyBookingTimeRules();

    if (startsAt) {
        startsAt.addEventListener('change', applyBookingTimeRules);
    }

    if (endsAt) {
        endsAt.addEventListener('change', applyBookingTimeRules);
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            if (!startsAt || !endsAt || !startsAt.value || !endsAt.value) {
                return;
            }

            const startDate = new Date(startsAt.value);
            const endDate = new Date(endsAt.value);
            const minStart = new Date(Date.now() + 2 * 60 * 60 * 1000);
            const maxEnd = new Date(startDate.getTime() + 4 * 60 * 60 * 1000);

            if (startDate < minStart) {
                event.preventDefault();
                alert('La réservation doit commencer au moins 2 heures à l\'avance.');
                return;
            }

            if (endDate <= startDate) {
                event.preventDefault();
                alert('La date de fin doit être après la date de début.');
                return;
            }

            if (endDate > maxEnd) {
                event.preventDefault();
                alert('La durée maximale d\'une réservation est de 4 heures.');
            }
        });
    }
});
</script>
@endpush
