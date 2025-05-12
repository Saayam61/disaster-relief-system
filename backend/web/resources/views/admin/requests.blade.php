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
                <h3>Requests Log</h3>
                <div>
                <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Relief Center
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['center_id' => '']) }}">All Centers</a>
                            </li>
                            @foreach ($reliefCenters as $reliefCenter)
                                <li>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['center_id' => $reliefCenter->center_id]) }}">
                                        {{ $reliefCenter->user->name}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Request Type
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => 'supply']) }}">Supply</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => 'evacuation']) }}">Evacuation</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => 'medical']) }}">Medical</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => 'other']) }}">Other</a></li>
                        </ul>
                    </div>

                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'processing']) }}">Processing</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'fulfilled']) }}">Fulfilled</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Rejected</a></li>
                        </ul>
                    </div>

                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Urgency
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['urgency' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['urgency' => 'low']) }}">Low</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['urgency' => 'medium']) }}">Medium</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['urgency' => 'high']) }}">High</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($requests->isEmpty())
                    <div class="alert alert-info">No Requests has been registered yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User Name</th>
                                    <th>Relief Center</th>
                                    <th>Request Type</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Urgency</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $request->user->name }}</td>
                                        <td>{{ $request->reliefCenter->user->name }}</td>
                                        <td>
                                            @if($request->request_type === 'supply')
                                                <span class="badge bg-success">Supply</span>
                                            @elseif($request->request_type === 'evacuation')
                                                <span class="badge bg-primary">Evacuation</span>
                                            @elseif($request->request_type === 'medical')
                                                <span class="badge bg-danger">Medical</span>
                                            @elseif($request->request_type === 'other')
                                                <span class="badge bg-warning text-black">Other</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->status === 'pending')
                                                <span class="badge bg-warning text-black">Pending</span>
                                            @elseif($request->status === 'processing')
                                                <span class="badge bg-primary">Processing</span>
                                            @elseif($request->status === 'fulfilled')
                                                <span class="badge bg-success">Fulfilled</span>
                                            @elseif($request->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->description}}</td>
                                        <td>{{ $request->quantity}}</td>
                                        <td>{{ $request->unit}}</td>
                                        <td>
                                            @if($request->urgency === 'low')
                                                <span class="badge bg-primary">Low</span>
                                            @elseif($request->urgency === 'medium')
                                                <span class="badge bg-warning text-black">Medium</span>
                                            @elseif($request->urgency === 'high')
                                                <span class="badge bg-danger">High</span>
                                            @endif
                                        </td>                                        
                                        <td>
                                            <div class="btn-group d-flex justify-content-start">
                                                <form action="{{ route('admin.requests.destroy', $request->request_id) }}" method="POST" class="d-inline">
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
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection