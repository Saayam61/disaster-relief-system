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
                <h3>Volunteers Log</h3>
                <div>
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
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Active Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}">Active</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}">Inactive</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($volunteers->isEmpty())
                    <div class="alert alert-info">No Volunteer has been registered yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Assigned to</th>
                                    <th>Skills</th>
                                    <th>Availability</th>
                                    <th>Approval Status</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($volunteers as $vol)
                                    <tr>
                                        <td>{{ $vol->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $vol->user->name }}</td>
                                        <td>
                                            <!-- Assign to Relief Center -->
                                            <form action="{{ route('admin.volunteers.updateRC', $vol->volunteer_id) }}" method="POST" class="d-inline-block me-2">
                                                @csrf
                                                @method('PUT')
                                                <select name="center_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="">RC - {{ $vol->reliefCenter->user->name ?? 'None' }}</option>
                                                    <option value="">None</option>
                                                    @foreach($reliefCenters as $rc)
                                                        <option value="{{ $rc->center_id }}">{{ $rc->user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </form>

                                            <!-- Assign to Organization -->
                                            <form action="{{ route('admin.volunteers.updateOrg', $vol->volunteer_id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('PUT')
                                                <select name="org_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="">Org - {{ $vol->organization->user->name ?? 'None' }}</option>
                                                    <option value="">None</option>
                                                    @foreach($organizations as $org)
                                                        <option value="{{ $org->org_id }}">{{ $org->user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td>{{ $vol->skills}}</td>
                                        <td>{{ $vol->availability}}</td>
                                        <td>
                                            @if($vol->approval_status === 'pending')
                                                <span class="badge bg-secondary">Pending</span>
                                            @elseif($vol->approval_status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($vol->approval_status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>                                        
                                        <td>
                                            @if($vol->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($vol->status === 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>                                        
                                        <td>
                                            <div class="btn-group d-flex justify-content-start">
                                                <form action="{{ route('admin.volunteers.destroy', $vol->volunteer_id) }}" method="POST" class="d-inline">
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