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
                <h3>Contributions Log</h3>
                <div>
                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Type
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'donated']) }}">Donated</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'received']) }}">Received</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($contributions->isEmpty())
                    <div class="alert alert-info">No Contributions has been registered yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name - RC</th>
                                    <th>User</th>
                                    <th>Volunteer</th>
                                    <th>Organization</th>
                                    <th>Title</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contributions as $contribution)
                                    <tr>
                                        <td>{{ $contribution->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ ($contribution->reliefCenter->user->name) ?? '' }}</td>
                                        <td>{{ ($contribution->user->name) ?? '-' }}</td>
                                        <td>{{ ($contribution->volunteer->user->name) ?? '-' }}</td>
                                        <td>{{ ($contribution->organization->user->name) ?? '-' }}</td>
                                        <td>{{ $contribution->name }}</td>
                                        <td>{{ $contribution->quantity}}</td>
                                        <td>{{ $contribution->unit}}</td>
                                        <td>{{ ($contribution->description) ?? ''}}</td>
                                        <td>
                                            @if($contribution->type === 'donated')
                                                <span class="badge bg-success">Donated</span>
                                            @elseif($contribution->type === 'received')
                                                <span class="badge bg-primary">Received</span>
                                            @endif
                                        </td>                                        
                                        <td>
                                            <div class="btn-group d-flex justify-content-start">
                                                <form action="{{ route('admin.contributions.destroy', $contribution->contribution_id) }}" method="POST" class="d-inline">
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
                        {{ $contributions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection