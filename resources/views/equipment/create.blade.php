@extends('layouts.app')

@section('title', isset($equipmentItem) ? 'Modifier le matériel' : 'Nouveau matériel')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-tools me-2"></i>
                    {{ isset($equipmentItem) ? 'Modifier le matériel' : 'Nouveau matériel' }}
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($equipmentItem) ? route('equipment.update', $equipmentItem['id']) : route('equipment.store') }}">
                    @csrf
                    @if(isset($equipmentItem))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Nom du matériel <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               class="form-control" 
                               value="{{ old('name', $equipmentItem['name'] ?? '') }}" 
                               required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantité <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="quantity" 
                               class="form-control" 
                               value="{{ old('quantity', $equipmentItem['quantity'] ?? '') }}" 
                               min="0" 
                               required>
                        @error('quantity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-secondary">
                            Nombre total d'unités disponibles
                        </small>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_available"
                                   name="is_available" 
                                   value="1"
                                   {{ (old('is_available', $equipmentItem['is_available'] ?? true) ? 'checked' : '') }}>
                            <label class="form-check-label" for="is_available">
                                Matériel disponible pour réservation
                            </label>
                        </div>
                        @error('is_available')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('equipment.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            {{ isset($equipmentItem) ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
