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
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="query">Search by Name</label>
                                        <div class="input-group">
                                            <input type="text" id="query" name="query" class="form-control" 
                                                placeholder="Type any part of a name..." 
                                                value="{{ request('query') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Leave blank to see all users
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="role">Filter by Role</label>
                                        <select id="role" name="role" class="form-control">
                                            <option value="">All Roles</option>
                                            <option value="relief_center" {{ request('role') == 'relief_center' ? 'selected' : '' }}>Relief Center</option>
                                            <option value="organization" {{ request('role') == 'organization' ? 'selected' : '' }}>Organization</option>
                                            <option value="volunteer" {{ request('role') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Location Filter (optional)</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="number" step="any" name="latitude" class="form-control" 
                                                    placeholder="Latitude" value="{{ request('latitude') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" step="any" name="longitude" class="form-control" 
                                                    placeholder="Longitude" value="{{ request('longitude') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" name="radius" class="form-control" 
                                                    min="1" max="100" placeholder="Radius (km)" 
                                                    value="{{ request('radius', 10) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                    <h5>
                                                        <a href="{{ route('contribution.index', [$user->user_id]) }}">
                                                            @if(request()->filled('query'))
                                                                @foreach(explode(' ', request('query')) as $term)
                                                                    @if(str_contains(strtolower($user->name), strtolower($term)))
                                                                        <mark>{{ $term }}</mark>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                            {{ $user->name }}
                                                        </a>
                                                    </h5>
                                                    <p class="mb-1">
                                                        <span class="badge badge-primary">
                                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                                        </span>
                                                    </p>
                                                    @if($user->profile && $user->profile->location)
                                                        <small class="text-muted">
                                                            <i class="fas fa-map-marker-alt"></i> {{ $user->profile->location }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href=" route('messages.create', ['recipient_id' => $user->id]) }}" 
                                                    class="btn btn-sm btn-outline-primary">
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