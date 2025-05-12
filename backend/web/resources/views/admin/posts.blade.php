@extends('layouts.app')

@section('content')
<style>
    body {
        background: #f4f6f9;
        overflow-x: hidden;
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

</style>

<div id="main-content" class="main-content">
    <div class="container-fluid">
        <!-- Supplies Log Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Posts Log</h3>
                <div>
                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Relief Center
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['center_id' => '']) }}">All Centers</a>
                            </li>
                            @foreach ($reliefCenters as $reliefCenter)
                                <li>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['center_id' => $reliefCenter->center_id]) }}">
                                        {{ $reliefCenter->user->name}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($posts->isEmpty())
                    <div class="alert alert-info">No Posts has been registered yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posts as $post)
                                    <tr>
                                        <td>{{ $post->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $post->reliefCenter->user->name }}</td>
                                        <td>{{ $post->title }}</td>
                                        <td>{{ $post->content}}</td>
                                        <td>
                                            @if($post->image_url)
                                                <img src="{{ asset('storage/' . $post->image_url) }}" alt="Post Image" class="img-fluid">
                                            @endif
                                        </td>                                       
                                        <td>
                                            <div class="btn-group d-flex justify-content-start">
                                                <form action="{{ route('admin.posts.destroy', $post->post_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger mx-2" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection