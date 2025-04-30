<!-- Sidebar -->
<aside id="sidebar" class="sidebar d-flex flex-column">
    <h4 class="text-center mb-4">Relief Center</h4>
    <div class="text-center">
        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=FFFFFF&background=263749' }}" class="user-avatar">
    </div>
    <p class="text-center">{{ Auth::user()->name }}</p>

    <hr class="bg-light">

    <a href="{{ route('home') }}"><i class="fas fa-user"></i> Home / Profile</a>
    <a href="{{ route('contribution.index') }}"><i class="fas fa-box"></i>Contribution</a>
    <a href="{{ route('volunteer.index') }}"><i class="fas fa-hands-helping"></i> Volunteers</a>
    <a href="{{ route('request.index') }}"><i class="fas fa-hand-holding-heart"></i> Requests</a>
    <a href="{{ route('news-feed.index') }}"><i class="fas fa-newspaper"></i> News Feed</a>

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
</script>
@endpush