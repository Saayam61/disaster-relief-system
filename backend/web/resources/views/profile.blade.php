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
        <!-- Post Creation Form -->
        @if(auth()->user()->role === 'Relief Center')

            <div class="card mb-4">
                <div class="card-header">Create Post</div>
                <div class="card-body">
                @if(isset($post))
                    <form action="{{ route('profile.update', $post->post_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                @else
                    <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                @endif
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $post->title ?? '') }}" required>
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea name="content" id="content" rows="4" class="form-control" required>{{ old('content', $post->content ?? '') }}</textarea>
                            @error('content')
                                <span class="text-danger">{{ $mssage }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image (optional)</label>
                            <input type="file" name="image_url" id="image_url" class="form-control">
                            @if(isset($post) && $post->image_url)
                                <p class="mt-2">Current Image:</p>
                                <img src="{{ asset('storage/' . $post->image_url) }}" alt="Post Image" class="img-fluid" style="max-height: 200px;">
                            @endif
                            @error('image_url')
                                <span class="text-danger">{{$mesage}}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ isset($post) ? 'Update Post' : 'Post' }}
                        </button>
                    </form>
                </div>
            </div>
        @endif

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
                    @php
                        $reliefCenter = \App\Models\ReliefCenter::where('user_id', Auth::id())->firstOrFail();
                    @endphp
                    @if(Auth::user()->role === 'Relief Center' && $reliefCenter && $reliefCenter->center_id === $post->center_id)
                        <div class="btn-group">
                            <a href="{{ route('profile.edit', $post->post_id) }}" class="btn btn-sm btn-success rounded mx-2">Edit</a>
                            <form action="{{ route('profile.destroy', $post->post_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger rounded mx-2">Delete</button>
                            </form>
                        </div>
                    @endif
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
