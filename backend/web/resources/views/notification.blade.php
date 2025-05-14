@extends('layouts.app')

@section('content')
<style>
    /* Page-specific styles */
    body {
        background: #f4f6f9;
        overflow-x: hidden;
    }

    img{
        align-items: center;
    }

    .main-content {
        margin-left: 220px;
        transition: all 0.3s;
        padding-top: 56px; 
        min-height: calc(100vh - 56px); 
    }

    .main-content.expanded {
        margin-left: 0;
    }
    /* Add other page-specific styles here */
</style>
<main id="main-content" class="main-content col">

    <div class="container">
        <h4>Your Notifications</h4>
        <ul class="list-group">
            @forelse($notifications as $notification)
                <li class="list-group-item {{ $notification->read_at ? '' : 'list-group-item-warning' }}">
                    {{ $notification->data['message'] ?? 'Flood alert received' }}
                    <span class="float-end text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="list-group-item">You're safe. No alerts.</li>
            @endforelse
        </ul>
        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
</main>
@endsection
