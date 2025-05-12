<!-- Sidebar -->
<aside id="sidebar" class="sidebar d-flex flex-column">
    @if (Auth::user()->role === 'Relief Center')
        <h4 class="text-center mb-4">Relief Center Panel</h4>
    @elseif(Auth::user()->role === 'Administrator')
        <h4 class="text-center mb-4">Admin Panel</h4>
    @else
        <h4 class="text-center mb-4">Organization Panel</h4>
    @endif
    <div class="text-center">
        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=FFFFFF&background=263749' }}" class="user-avatar">
    </div>
    <p class="text-center">{{ Auth::user()->name }}</p>

    @if (Auth::user()->role !== 'Administrator')
    <hr class="bg-light">
        <a href="{{ route('profile.index') }}"><i class="fas fa-user"></i>Profile</a>
        <a href="{{ route('contribution.index', [Auth::id()]) }}"><i class="fas fa-box"></i>Contribution</a>
        <a href="{{ route('volunteer.index') }}"><i class="fas fa-hands-helping"></i> Volunteers</a>
        <a href="{{ route('request.index') }}"><i class="fas fa-hand-holding-heart"></i> Requests</a>
        <a href="{{ route('news-feed.index') }}"><i class="fas fa-newspaper"></i> News Feed</a>
    @else
        <hr class="bg-light">
        <a href=" route('admin.alerts') }}"><i class="fas fa-triangle-exclamation"></i> Alerts</a>
        <a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a>
        <a href="{{ route('admin.reliefcenters') }}"><i class="fa-solid fa-house-chimney-medical"></i> Relief Centers</a>
        <a href="{{ route('admin.volunteers') }}"><i class="fas fa-hands-helping"></i> Volunteers</a>
        <a href="{{ route('admin.contributions') }}"><i class="fas fa-box"></i> Contributions</a>
        <a href="{{ route('admin.requests') }}"><i class="fas fa-hand-holding-heart"></i> Requests</a>
        <a href="{{ route('admin.posts') }}"><i class="fas fa-newspaper"></i> Posts</a>
        <a href="{{ route('news-feed.index') }}"><i class="fas fa-newspaper"></i> News Feed</a>
    @endif

    <a href="{{ route('logout') }}" 
    onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
    class="btn btn-danger mt-auto mx-3 mb-3">
        Logout
    </a>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</aside>
@push('scripts')
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    const toggleButton = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');

    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });

    window.onload = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                fetch('/update-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    })
                });
            });
        } else {
            console.warn("Geolocation is not supported by this browser.");
        }
    };
</script>
@endpush