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
                <h3>Request Log</h3>
                <div>
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

                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'processing']) }}">Processing</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'Fulfilled']) }}">Fulfilled</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Rejected</a></li>
                        </ul>
                    </div>

                    <div class="btn-group me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="typeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter by Type
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="typeFilterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => '']) }}">All Types</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => 'supply']) }}">Supply</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => 'evacuation']) }}">Evacuation</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => 'medical']) }}">Medical</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['request_type' => 'other']) }}">Other</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($requests->isEmpty())
                    <div class="alert alert-info">No Request has been received yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Urgency</th>
                                    <th>Request Type</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $req)
                                    <tr>
                                        <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $req->user->name }}</td>
                                        <td>{{ $req->user->address }}</td>
                                        <td>{{ $req->quantity ?? 'N/A'}}</td>
                                        <td>{{ $req->unit ?? 'N/A' }}</td>
                                        <td>
                                            @if($req->urgency === 'low')
                                                <span class="badge bg-primary">Low</span>
                                            @elseif($req->urgency === 'medium')
                                                <span class="badge bg-warning text-black">Medium</span>
                                            @elseif($req->urgency === 'high')
                                                <span class="badge bg-danger">High</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($req->request_type === 'supply')
                                                <span class="badge bg-success">Supply</span>
                                            @elseif($req->request_type === 'evacuation')
                                                <span class="badge bg-primary">Evacuation</span>
                                            @elseif($req->request_type === 'medical')
                                                <span class="badge bg-danger">Medical</span>
                                            @elseif($req->request_type === 'other')
                                                <span class="badge bg-warning text-black">Other</span>
                                            @else
                                                <span class="badge bg-secondary">Error</span>
                                            @endif
                                        </td>
                                        <td>{{ $req->description }}</td>
                                        <td>
                                            @if($req->status === 'pending')
                                                <span class="badge bg-warning text-black">Pending</span>
                                            @elseif($req->status === 'processing')
                                                <span class="badge bg-primary">Processing</span>
                                            @elseif($req->status === 'fulfilled')
                                                <span class="badge bg-success">Fulfilled</span>
                                            @elseif($req->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">Error</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group d-flex justify-content-start">
                                                <form action="{{ route('request.update', $req->request_id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="processing" {{ $req->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                                        <option value="fulfilled" {{ $req->status == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                                                        <option value="rejected" {{ $req->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    </select>
                                                </form>
                                                <form action="{{ route('request.destroy', $req->request_id) }}" method="POST" class="d-inline">
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