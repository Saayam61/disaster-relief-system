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
        <!-- Form for logging Received Goods -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Log Received Goods</h3>
            </div>
            <div class="card-body">
            @if(isset($contribution))
                <form action="{{ route('contribution.update', $contribution->contribution_id) }}" method="POST">
                    @csrf
                    @method('PUT')
            @else
                <form action="{{ route('contribution.store') }}" method="POST">
                    @csrf
            @endif  
                    <div class="form-group mb-3">
                        <label for="user_id">From User (Optional)</label>
                        <select class="form-control" name="user_id">
                            <option value="">Select User</option>
                            @foreach($generalUsers as $user)
                                <option value="{{ $user->user_id }}"
                                    {{ old('user_id', $contribution->user_id ?? '') == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="volunteer_id">From Volunteer (Optional)</label>
                        <select class="form-control" name="volunteer_id">
                            <option value="">Select Volunteer</option>
                            @foreach($volunteers as $volunteer)
                                <option value="{{ $volunteer->volunteer_id }}"
                                    {{ old('user_id', $contribution->volunteer_id ?? '') == $volunteer->volunteer_id ? 'selected' : '' }}>
                                    {{ $volunteer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="org_id">From Organization (Optional)</label>
                        <select class="form-control" name="org_id">
                            <option value="">Select Organization</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->org_id }}"
                                {{ old('user_id', $contribution->org_id ?? '') == $org->org_id ? 'selected' : '' }}>
                                {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="type">Type</label>
                        <input type="text" class="form-control" name="type" value="received" {{ old('type', $contribution->type ?? '') == 'received' ? 'selected' : '' }} required readonly>
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="name">Item Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $contribution->name ?? '')}}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" name="quantity" value="{{  $contribution->quantity ?? '' }}" required>
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="unit">Unit</label>
                            <input type="text" class="form-control" name="unit" value="{{ old('unit', $contribution->unit ?? '') }}" required>
                            @error('unit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description">Description (optional)</label>
                        <textarea class="form-control" name="description">{{ old('description', $contribution->description ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Log Received Goods</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection