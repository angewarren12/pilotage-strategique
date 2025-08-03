@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2"></i>
                Gestion des Utilisateurs
            </h1>
            <p class="text-muted">Administration des utilisateurs et des rôles</p>
        </div>
        
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nouvel Utilisateur
        </a>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Liste des Utilisateurs
                <span class="badge bg-primary ms-2">{{ $users->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if($user->id === Auth::id())
                                    <span class="badge bg-info ms-2">Vous</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role ? 'success' : 'warning' }}">
                                    {{ $user->role ? $user->role->libelle : 'Aucun rôle' }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('users.show', $user) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($user->id !== Auth::id())
                                    <form action="{{ route('users.destroy', $user) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun utilisateur trouvé</h5>
                <p class="text-muted">Commencez par créer votre premier utilisateur.</p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Créer le premier utilisateur
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 