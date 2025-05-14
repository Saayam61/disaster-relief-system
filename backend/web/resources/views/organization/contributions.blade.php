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
                                        <td>{{ ($contribution->organization->user->name) ?? '-' }}</td>
                                        <td>{{ $contribution->name }}</td>
                                        <td>{{ $contribution->quantity}}</td>
                                        <td>{{ $contribution->unit}}</td>
                                        <td>{{ ($contribution->description) ?? ''}}</td>
                                        <td>
                                            <span class="badge bg-success">Donated</span>
                                        </td>                                        
                                        <td>
                                        @php
                                            $org = Auth::user()->organizations ?? null;
                                        @endphp
                                            @if(Auth::user()->role === 'Organization' && $org->org_id === $contribution->org_id)
                                            <div class="btn-group d-flex justify-content-start">
                                                <form action="{{ route('contributions.destroy', $contribution->contribution_id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger mx-2" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @else
                                                <span class="badge bg-secondary">No Actions</span>
                                            @endif
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