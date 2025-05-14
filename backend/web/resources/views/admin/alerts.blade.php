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
        <div class="card d-flex my-4 p-3">
            <h1 class="h3">New Alert</h1>
            <div>
                <a href="{{ route('admin.checkFloods') }}" class="btn btn-success me-2">
                    + Send Alerts
                </a>
            </div>
        </div>

        
        <!-- Supplies Log Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Alerts Log</h3>
                <div>
                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Severity
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['severity' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['severity' => 'low']) }}">Low</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['severity' => 'medium']) }}">Medium</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['severity' => 'high']) }}">High</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['severity' => 'max']) }}">Max</a></li>
                        </ul>
                    </div>
                    
                    <div class="btn-group">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="userTypeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Active Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userTypeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['is_active' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['is_active' => 1]) }}">Active</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['is_active' => 0]) }}">Inactive</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($alerts->isEmpty())
                    <div class="alert alert-info">No alerts logged yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Message</th>
                                    <th>Description</th>
                                    <th>Severity</th>
                                    <th>IsActive</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alerts as $alert)
                                    <tr>
                                        <td>{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $alert->user->name }}</td>
                                        <td>{{ $alert->message }}</td>
                                        <td>{{ $alert->description }}</td>
                                        <td>
                                            @if($alert->severity === 'low')
                                                <span class="badge bg-secondary">Low</span>
                                            @elseif($alert->severity === 'medium')
                                                <span class="badge bg-primary">Medium</span>
                                            @elseif($alert->severity === 'high')
                                                <span class="badge bg-warning text-black">High</span>
                                            @else
                                                <span class="badge bg-danger">Max</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($alert->is_active === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <form action="{{ route('admin.alerts.destroy', $alert->alert_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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
                    <div>
                        {{ $alerts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection