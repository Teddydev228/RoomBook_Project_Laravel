@extends('layouts.app')

@section('title', 'Gestion du Matériel')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-tools me-2"></i>Gestion du Matériel
            </h2>
            <a href="{{ route('equipment.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Ajouter du matériel
            </a>
        </div>

        @if(empty($equipment))
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-tools display-4 text-secondary mb-3"></i>
                    <h5>Aucun matériel enregistré</h5>
                    <a href="{{ route('equipment.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter du matériel
                    </a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Quantité</th>
                                    <th>Disponible</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equipment as $item)
                                    <tr>
                                        <td><strong>{{ $item['name'] }}</strong></td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>
                                            @if($item['is_available'])
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Disponible
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Indisponible
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('equipment.edit', $item['id']) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('equipment.destroy', $item['id']) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Supprimer cet équipement ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
