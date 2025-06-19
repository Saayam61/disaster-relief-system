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
        <!-- Form for logging Donations -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Create User</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.create') }}" method="POST">
                    @csrf
                    <div class="col-md-12">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="phone">Phone</label>
                        <input type="number" class="form-control" name="phone" required>
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" required>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" name="address" required>
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="role">Role</label>
                        <select class="form-control" name="role">
                            <option value="">Select User</option>
                                <option value="Relief Center"> 
                                    Relief Center
                                </option>
                                <option value="Organization"> 
                                    Organization
                                </option>
                        </select>
                        @error('role')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="latitude">Latitude</label>
                        <input type="text" class="form-control" name="latitude" required>
                        @error('latitude')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="longitude">Longitude</label>
                        <input type="text" class="form-control" name="longitude" required>
                        @error('longitude')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection