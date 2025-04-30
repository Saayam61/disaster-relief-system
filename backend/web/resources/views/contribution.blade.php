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
        <div class="card d-flex  my-4 p-3">
            <h1 class="h3">New Supplies</h1>
            <div>
                <a href="{{ route('contribution.donation') }}" class="btn btn-success me-2">
                    + Add New Donation
                </a>
                <a href="{{ route('contribution.receive') }}" class="btn btn-primary">
                    + Add New Received
                </a>
            </div>
        </div>
        
        <!-- Supplies Log Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Supplies Log</h3>
                <div>
                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Type
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'donated']) }}">Donations</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'received']) }}">Received Goods</a></li>
                        </ul>
                    </div>
                    
                    <div class="btn-group">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="userTypeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Contributor (Donated By)
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userTypeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['user_type' => '']) }}">All Contributors</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['user_type' => 'user']) }}">General Users</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['user_type' => 'volunteer']) }}">Volunteers</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['user_type' => 'relief_center']) }}">Relief Centers</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['user_type' => 'organization']) }}">Organizations</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($contributions->isEmpty())
                    <div class="alert alert-info">No supplies logged yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Received By</th>
                                    <th>Donated By</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contributions as $contribution)
                                    <tr>
                                        <td>{{ $contribution->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge {{ $contribution->type == 'donated' ? 'bg-success' : 'bg-info' }}">
                                            {{ ucfirst($contribution->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $contribution->name }}</td>
                                        <td>{{ $contribution->quantity }}{{ $contribution->unit }}</td>
                                        <td>
                                            @if($contribution->type === 'donated')
                                                <span class="badge bg-primary">User</span>
                                                {{ $contribution->user->name ?? 'Unknown' }}
                                            @elseif($contribution->type === 'received')
                                                <span class="badge bg-warning text-dark">Relief Center</span>
                                                {{ $contribution->reliefCenter->name ?? 'Unknown' }}
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contribution->type === 'donated')
                                                <span class="badge bg-warning text-dark">Relief Center</span>
                                                {{ $contribution->reliefCenter->name ?? 'Unknown' }}
                                            @elseif($contribution->type === 'received')
                                                @if($contribution->user_id)
                                                    <span class="badge bg-primary">User</span>
                                                    {{ $contribution->user->name }}
                                                @elseif($contribution->volunteer_id)
                                                    <span class="badge bg-success">Volunteer</span>
                                                    {{ $contribution->volunteer->name ?? 'Unknown' }}
                                                @elseif($contribution->org_id)
                                                    <span class="badge bg-info text-dark">Organization</span>
                                                    {{ $contribution->organization->org_name ?? 'Unknown' }}
                                                @else
                                                    <span class="badge bg-secondary">Unknown</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        <td>{{ $contribution->description ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if($contribution->type === 'donated')
                                                    <a href="{{ route('contribution.editDonation', $contribution->contribution_id) }}" class="btn btn-sm btn-warning me-2">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('contribution.editReceive', $contribution->contribution_id) }}" class="btn btn-sm btn-warning me-2">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                <form action="{{ route('contribution.destroy', $contribution->contribution_id) }}" method="POST" class="d-inline">
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
                        {{ $contributions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection