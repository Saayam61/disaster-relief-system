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
        <h1 class="mb-4">Manage Supplies</h1>
        
        <!-- Form for logging Donations -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Log Donation</h3>
            </div>
            <pre>{{ var_dump($contribution) }}</pre>

            <div class="card-body">
                <form action="{{ isset($contribution) ? route('contribution.update', $contribution->contribution_id) : route('contribution.store') }}" method="POST">
                    @csrf
                    @if(isset($contribution))
                        @method('PUT')
                    @endif
                    <div class="form-group mb-3">
                        <label for="user_id">User (Optional)</label>
                        <select class="form-control" name="user_id">
                            <option value="">Select User</option>
                            @foreach($generalUsers as $user)
                                <option value="{{ $user->user_id }}" 
                                    {{ old('user_id', $contribution->user_id) == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="name">Type</label>
                        <input type="text" class="form-control" name="type" value="donated" {{ old('type', $contribution->type) ? 'selected' : '' }} required readonly>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="name">Item Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $contribution->name) }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" name="quantity" value="{{ old('quantity', $contribution->quantity) }}" required>
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="unit">Unit</label>
                            <input type="text" class="form-control" name="unit" value="{{ old('unit', $contribution->unit) }}" required>
                            @error('unit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description">Description (optional)</label>
                        <textarea class="form-control" name="description">{{ old('description', $contribution->description) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        {{ isset($contribution) ? 'Update Contribution' : 'Add Contribution' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Form for logging Received Goods -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Log Received Goods</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('contribution.storeR') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="center_id">Relief Center</label>
                        <select class="form-control" name="center_id" required>
                            <option value="">Select Relief Center</option>
                            @foreach($reliefCenters as $center)
                                <option value="{{ $center->center_id }}">{{ $center->name }}</option>
                            @endforeach
                        </select>
                        @error('center_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="org_id">From User (Optional)</label>
                        <select class="form-control" name="user_id">
                            <option value="">Select User</option>
                            @foreach($generalUsers as $user)
                                <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="org_id">From Volunteer (Optional)</label>
                        <select class="form-control" name="volunteer_id">
                            <option value="">Select Volunteer</option>
                            @foreach($volunteers as $volunteer)
                                <option value="{{ $volunteer->volunteer_id }}">{{ $volunteer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="org_id">From Organization (Optional)</label>
                        <select class="form-control" name="org_id">
                            <option value="">Select Organization</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->org_id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="name">Item Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" name="quantity" value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="unit">Unit</label>
                            <input type="text" class="form-control" name="unit" value="{{ old('unit') }}" required>
                            @error('unit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description">Description (optional)</label>
                        <textarea class="form-control" name="description">{{ old('description') }}</textarea>
                    </div>

                    <input type="hidden" name="type" value="received">

                    <button type="submit" class="btn btn-primary mt-3">Log Received Goods</button>
                </form>
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
                                                <a href="{{ route('contribution.edit', $contribution->contribution_id) }}" class="btn btn-sm btn-warning me-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
                    
                    <div class="mt-3">
                        {{ $contributions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection