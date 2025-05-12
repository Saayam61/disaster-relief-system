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
                <h3>Users Log</h3>
                <div>
                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Role
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => 'General User']) }}">General User</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => 'Volunteer']) }}">Volunteer</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => 'Relief Center']) }}">Relief Center</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['role' => 'Organization']) }}">Organization</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($users->isEmpty())
                    <div class="alert alert-info">No User has been registered yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->address }}</td>
                                        <td>{{ $user->email}}</td>
                                        <td>{{ $user->phone}}</td>
                                        <td>
                                            @if($user->role === 'General User')
                                                <span class="badge bg-primary">General User</span>
                                            @elseif($user->role === 'Volunteer')
                                                <span class="badge bg-secondary">Volunteer</span>
                                            @elseif($user->role === 'Relief Center')
                                                <span class="badge bg-success">Relief Center</span>
                                            @else
                                                <span class="badge bg-warning text-black">Organization</span>
                                            @endif
                                        </td>                                        
                                        <td>{{ $user->latitude}}</td>
                                        <td>{{ $user->longitude}}</td>
                                        <td>
                                            <div class="btn-group d-flex justify-content-start">
                                                <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" class="d-inline">
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
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection