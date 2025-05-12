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
                <h3>Relief Centers Log</h3>
                <div>
                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Active Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['is_active' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['is_active' => '1']) }}">Active</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['is_active' => '0']) }}">Inactive</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($reliefCenters->isEmpty())
                    <div class="alert alert-info">No Relief Center has been registered yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Capacity</th>
                                    <th>Current Occupancy</th>
                                    <th>Total Volunteers</th>
                                    <th>Total Supplies</th>
                                    <th>Contact Numbers</th>
                                    <th>Is Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reliefCenters as $reliefCenter)
                                    <tr>
                                        <td>{{ $reliefCenter->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $reliefCenter->user->name }}</td>
                                        <td>{{ $reliefCenter->capacity }}</td>
                                        <td>{{ $reliefCenter->current_occupancy}}</td>
                                        <td>{{ $reliefCenter->total_volunteers}}</td>
                                        <td>{{ $reliefCenter->total_supplies}}</td>
                                        <td>{{ $reliefCenter->contact_numbers}}</td>
                                        <td>
                                            @if($reliefCenter->is_active == 1)
                                                <span class="badge bg-success">Active</span>
                                            @elseif($reliefCenter->is_active == 0)
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>                                        
                                        <td>
                                            <div class="btn-group d-flex justify-content-start">
                                                <form action="{{ route('admin.reliefcenters.destroy', $reliefCenter->center_id) }}" method="POST" class="d-inline">
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
                        {{ $reliefCenters->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection