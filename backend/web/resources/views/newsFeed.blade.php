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

<!-- Main Section -->
<main id="main-content" class="main-content col">
    <div class="container mt-4">
        <!-- Display Posts -->
        @foreach($posts as $post)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($post->reliefCenter->user->name).'&color=FFFFFF&background=263749' }}" 
                            class="rounded-circle me-2" 
                            style="width: 40px; height: 40px; object-fit: cover;">
                        <span class="fw-semibold">{{ $post->reliefCenter->user->name ?? 'Unknown' }}</span>
                    </div>
                    <text class="text-muted">{{ $post->created_at->diffForHumans() }}</text>
                </div>

                <div class="card-body">
                    <strong>Title: {{ $post->title }}</strong>
                    <p>Content: {{ $post->content }}</p>
                    @if($post->image_url)
                        <img src="{{ asset('storage/' . $post->image_url) }}" alt="Post Image" class="img-fluid mt-2">
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</main>
@endsection
