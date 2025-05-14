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

    .dashboard-card {
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease-in-out;
        background-color: rgb(214, 221, 174);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .dashboard-icon {
        font-size: 2rem;
    }

    .dashboard-stat {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .dashboard-label {
        font-size: 1rem;
        color: #6c757d;
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

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4>Dashboard Overview</h4>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-3">
                <div class="col-md-3">
                    <div class="card dashboard-card text-center p-3">
                        <div class="dashboard-icon text-primary"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="dashboard-stat">{{ $activeAlertsCount }}</div>
                        <div class="dashboard-label">Active Flood Alerts</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center p-3">
                        <div class="dashboard-icon text-success"><i class="fas fa-hands-helping"></i></div>
                        <div class="dashboard-stat">{{ $activeVolunteersCount }}</div>
                        <div class="dashboard-label">Active Volunteers</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center p-3">
                        <div class="dashboard-icon text-info"><i class="fas fa-building"></i></div>
                        <div class="dashboard-stat">{{ $activeReliefCentersCount }}</div>
                        <div class="dashboard-label">Active Relief Centers</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center p-3">
                        <div class="dashboard-icon text-warning"><i class="fas fa-users"></i></div>
                        <div class="dashboard-stat">{{ $activeOrganizationsCount }}</div>
                        <div class="dashboard-label">Active Organizations</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center p-3">
                        <div class="dashboard-icon text-danger"><i class="fas fa-box-open"></i></div>
                        <div class="dashboard-stat">{{ $donatedContributionsCount }}</div>
                        <div class="dashboard-label">Donations Made</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center p-3">
                        <div class="dashboard-icon text-dark"><i class="fas fa-hand-holding-heart"></i></div>
                        <div class="dashboard-stat">{{ $receivedContributionsCount }}</div>
                        <div class="dashboard-label">Donations Received</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center p-3">
                        <div class="dashboard-icon text-secondary"><i class="fas fa-check-circle"></i></div>
                        <div class="dashboard-stat">{{ $fulfilledRequestsCount }}</div>
                        <div class="dashboard-label">Requests Fulfilled</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center p-3">
                        <div class="dashboard-icon text-muted"><i class="fas fa-newspaper"></i></div>
                        <div class="dashboard-stat">{{ $postCount }}</div>
                        <div class="dashboard-label">Total Posts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4>User Roles Overview</h4>
        </div>
        <div class="card-body d-flex justify-content-center align-items-center" style="height: 500px;">
            <canvas id="userRolesChart"></canvas>
        </div>
    </div>
</main>
@endsection
@push('scripts')
<script>
    const ctx = document.getElementById('userRolesChart').getContext('2d');
    
    // Safely get the PHP variables into JS
    const roleLabels = JSON.parse('{!! addslashes(json_encode($userRolesCount->pluck("role"))) !!}');
    const roleData = JSON.parse('{!! addslashes(json_encode($userRolesCount->pluck("total"))) !!}');

    const userRolesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: roleLabels,
            datasets: [{
                label: 'Total Users',
                data: roleData,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'
                ],
                borderColor: '#ccc',
                borderWidth: 1,
                barThickness: 50
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush

