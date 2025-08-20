@extends('layouts.app')

@section('title', 'Test Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Test des Notifications
                    </h5>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Titre</th>
                                        <th>Message</th>
                                        <th>Priorité</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr class="{{ $notification->isUnread() ? 'table-warning' : '' }}">
                                            <td>
                                                <i class="{{ $notification->type_icon }}"></i>
                                                <span class="badge bg-secondary">{{ $notification->type }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $notification->title }}</strong>
                                            </td>
                                            <td>{{ Str::limit($notification->message, 100) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $notification->priority_color }}">
                                                    {{ $notification->priority }}
                                                </span>
                                            </td>
                                            <td>{{ $notification->formatted_created_at }}</td>
                                            <td>
                                                @if($notification->isUnread())
                                                    <span class="badge bg-primary">Non lue</span>
                                                @else
                                                    <span class="badge bg-success">Lue</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($notification->isUnread())
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="markAsRead({{ $notification->id }})">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteNotification({{ $notification->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bell-slash text-muted fs-1"></i>
                            <p class="text-muted mt-2">Aucune notification trouvée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    if (confirm('Marquer cette notification comme lue ?')) {
        // Ici vous pouvez ajouter une requête AJAX pour marquer comme lue
        location.reload();
    }
}

function deleteNotification(notificationId) {
    if (confirm('Supprimer cette notification ?')) {
        // Ici vous pouvez ajouter une requête AJAX pour supprimer
        location.reload();
    }
}
</script>
@endsection 