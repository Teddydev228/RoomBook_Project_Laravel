@extends('layouts.app')

@section('title', 'Gestion des Salles')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-door-open me-2"></i>Gestion des Salles
            </h2>
            <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Ajouter une salle
            </a>
        </div>

        @if(empty($rooms))
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-door-open display-4 text-secondary mb-3"></i>
                    <h5>Aucune salle enregistrée</h5>
                    <a href="{{ route('rooms.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter une salle
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
                                    <th>Bâtiment</th>
                                    <th>Capacité</th>
                                    <th>Disponible</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rooms as $room)
                                    <tr>
                                        <td>
                                            <strong>{{ $room['name'] }}</strong>
                                        </td>
                                        <td>{{ $room['building'] }}</td>
                                        <td>{{ $room['capacity'] }} personnes</td>
                                        <td>
                                            @if($room['is_available'])
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
                                                <a href="{{ route('rooms.edit', $room['id']) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('rooms.destroy', $room['id']) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Supprimer cette salle ?')">
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
