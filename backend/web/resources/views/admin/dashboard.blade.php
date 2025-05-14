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
            <form method="POST" action="{{ route('admin.updateUser') }}">
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
</main>

@endsection


