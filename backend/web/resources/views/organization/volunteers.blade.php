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
                <h3>Volunteer Log</h3>
                <div>
                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}">Active</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}">Inactive</a></li>
                        </ul>
                    </div>

                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Approval Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['approval_status' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['approval_status' => 'pending']) }}">Pending</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['approval_status' => 'approved']) }}">Approved</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['approval_status' => 'rejected']) }}">Rejected</a></li>
                        </ul>
                    </div>

                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="radiusFilterDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Radius
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="radiusFilterDropdown">
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
            <div class="card-body">
                @if($volunteers->isEmpty())
                    <div class="alert alert-info">No volunteer has joined yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Skills</th>
                                    <th>Availability</th>
                                    <th>Status</th>
                                    <th>Approval Stauts</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($volunteers as $vol)
                                    <tr>
                                        <td>{{ $vol->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $vol->user->name }}</td>
                                        <td>{{ $vol->user->address }}</td>
                                        <td>{{ $vol->skills ?? 'N/A'}}</td>
                                        <td>{{ $vol->availability ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $vol->stauts == 'status' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($vol->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($vol->approval_status === 'pending')
                                                <span class="badge bg-primary">Pending</span>
                                            @elseif($vol->approval_status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($vol->approval_status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning">Error</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group d-flex justify-content-start">
                                            @if ($vol->approval_status === 'pending')
                                                <form action="{{ route('volunteers.approve', $vol->volunteer_id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm mx-2">
                                                        <i class="fas fa-circle-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('volunteers.reject', $vol->volunteer_id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm mx-2">
                                                        <i class="fas fa-circle-xmark"></i>
                                                    </button>
                                                </form>
                                            @elseif ($vol->approval_status === 'approved')
                                                <span class="badge bg-success"><i class="fas fa-check"></i></span>
                                            @else ($vol->approval_status === 'rejected')
                                                <span class="badge bg-danger"><i class="fas fa-xmark"></i></span>
                                            @endif

                                                <form action="{{ route('volunteers.destroy', $vol->volunteer_id) }}" method="POST" class="d-inline">
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
                        {{ $volunteers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection