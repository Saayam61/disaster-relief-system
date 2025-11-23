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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            @if($results->count() > 0 )
                                Browse Users
                            @else
                                Search Results
                            @endif
                        </h4>
                    </div>

                    <div class="card-body">
                        <form method="GET" action="{{ route('search') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="input-group">
                                        <input type="text" id="query" name="query" class="form-control"
                                            placeholder="Search Name"
                                            value="{{ request('query') }}">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-2">
                                    <div class="btn-group w-100">
                                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="typeFilterDropdown"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            Filter by Role
                                        </button>
                                        <ul class="dropdown-menu w-100" aria-labelledby="typeFilterDropdown">
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => '']) }}">All Types</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => 'Relief Center']) }}">Relief Center</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => 'Organization']) }}">Organization</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => 'Volunteer']) }}">Volunteer</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-2">
                                    <div class="btn-group w-100">
                                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="radiusFilterDropdown"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            Filter by Radius
                                        </button>
                                        <ul class="dropdown-menu w-100" aria-labelledby="radiusFilterDropdown">
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['radius' => 500]) }}">All Within Nepal</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['radius' => 10]) }}">10 km</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['radius' => 25]) }}">25 km</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['radius' => 50]) }}">50 km</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['radius' => 100]) }}">100 km</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['radius' => 250]) }}">250 km</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <hr class="bg-light">

                        </form>

                        @if($results->count() > 0)
                            <div class="search-results">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>
                                        Found {{ $results->total() }} matching users
                                    </h5>
                                    <small class="text-muted">Showing {{ $results->firstItem() }}-{{ $results->lastItem() }}</small>
                                </div>
                                
                                <div class="list-group">
                                    @foreach($results as $user)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="d-flex align-items-center">
                                                        @if ($user->role === 'Relief Center')
                                                        <a href="{{ route('contribution.index', [$user->user_id]) }}">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=FFFFFF&background=263749' }}" class="user-avatar">
                                                            <span class="badge bg-success">{{ $user->name }}</span>
                                                            </div>
                                                        </a>
                                                        @elseif ($user->role === 'Organization')
                                                        <a href="{{ route('contributions.index', [$user->user_id]) }}">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=FFFFFF&background=263749' }}" class="user-avatar">
                                                            <span class="badge bg-success">{{ $user->name }}</span>
                                                            </div>
                                                        </a>
                                                        @else
                                                        <a href="{{ route('contributionv.index', [$user->user_id]) }}">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=FFFFFF&background=263749' }}" class="user-avatar">
                                                            <span class="badge bg-success">{{ $user->name }}</span>
                                                            </div>
                                                        </a>
                                                        @endif
                                                        <span class="ms-5"><i class="fa-solid fa-location-dot"></i></i>
                                                                {{ $user->address }}
                                                        </span>
                                                    </h5>
                                                    <p class="mb-1">
                                                        <span class="badge bg-primary">
                                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                                        </span>
                                                    </p>
                                                </div>
                                                <div>
                                                    <a href="{{ route('ui', $user->user_id) }}" 
                                                        class="badge bg-dark text-decoration-none fs-6">
                                                        <i class="fas fa-envelope"></i> Message
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-3">
                                    {{ $results->appends($searchParams)->links() }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                    No users found matching your search criteria.
                                    No users found in the system.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection