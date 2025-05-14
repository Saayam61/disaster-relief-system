@extends('layouts.app')

@section('content')
<style>
    /* Page-specific styles */
    body {
        background: #f4f6f9;
        overflow-x: hidden;
    }

    img{
        align-items: center;
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
    /* Add other page-specific styles here */
</style>

<!-- Main content -->
<main id="main-content" class="main-content col">
    <!-- Profile Overview - Editable -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4>Edit Your Account Details</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('org.updateUser') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-2">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ Auth::user()->phone }}">
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" value="{{ Auth::user()->address }}">
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label>Latitude</label>
                        <input type="number" name="latitude" class="form-control" value="{{ Auth::user()->latitude }}" readonly>
                    </div>

                    <div class="col-md-2">
                        <label>Longitude</label>
                        <input type="number" name="longitude" class="form-control" value="{{ Auth::user()->longitude }}" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </div>

    <!-- Full User Info Edit Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4>Edit Your Organization Profile</h4>
        </div>
        <div class="card-body">
            @php
                $org = Auth::user()->organizations ?? null;
            @endphp
            <form method="POST" action="{{ route('org.updateOrg') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label>Type</label>
                        <select name="type" class="form-select">
                            <option value="">Select Type</option>
                            <option value="i/ngo" {{ (old('type', $org->type ?? '') === 'i/ngo') ? 'selected' : '' }}>I/NGO</option>
                            <option value="private" {{ (old('type', $org->type ?? '') === 'private') ? 'selected' : '' }}>Private</option>
                        </select>
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label>Total Volunteers</label>
                        <input type="number" name="total_volunteers" class="form-control" value="{{ $org->total_volunteers ?? '' }}">
                        @error('total_volunteers')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1"{{ old('is_active', optional($org)->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Is Active</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Save Profile</button>
            </form>
        </div>
    </div>
</main>

@endsection


