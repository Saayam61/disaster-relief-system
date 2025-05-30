<nav id="navbar" class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
    <div class="container">
        <button class="btn btn-outline-light me-3" id="toggleSidebar">
            <i class="fas fa-align-justify"></i>
        </button>
        
        <a class="navbar-brand" href="#">
            <img src="/logo.png" alt="Logo" class="logo me-2 rounded" height="35px">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                @auth
                <li class="nav-item">
                    @if(Auth::user()->role === 'Administrator')
                        <a class="nav-link" href="{{ route('admin.home') }}"><i class="fas fa-home me-1"></i> Home</a>
                    @elseif(Auth::user()->role === 'Organization')
                        <a class="nav-link" href="{{ route('org.home') }}"><i class="fas fa-home me-1"></i> Home</a>
                    @else
                        <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-home me-1"></i> Home</a>
                    @endif                
                </li>
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                @auth
                    @if(Auth::user()->role !== 'Administrator')
                        <li class="nav-item">
                            <div class="search-box">
                            <form action="{{ route('search') }}" method="GET">
                                <i class="fas fa-search"></i>
                                <input class="form-control" name="query" type="search" placeholder="Search..." aria-label="Search">
                            </form>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link action-icon" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="notification-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
                                @endif
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="min-width: 300px;">
                                <!-- Dropdown Header -->
                                <li class="dropdown-header notification">
                                    <strong>Flood Alerts</strong>
                                </li>
                                
                                <!-- Notifications List -->
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <li>
                                        <a class="dropdown-item notification d-flex align-items-start" href="{{ route('notifications.read', $notification->id) }}">
                                            <div>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small><br>
                                                <span>{{ $notification->data['message'] ?? 'New flood alert!' }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li>
                                        <span class="dropdown-item text-muted">No new alerts</span>
                                    </li>
                                @endforelse

                                <!-- Divider -->
                                <li><hr class="dropdown-divider"></li>

                                <!-- View All Notifications Link -->
                                <li>
                                    <a style="background-color: white;" class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                                        <strong>View all</strong>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link action-icon" href="#">
                                <i class="fas fa-envelope"></i>
                                <span class="notification-badge">1</span>
                            </a>
                        </li>
                    @endif
                @endauth
                
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i> {{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus me-1"></i> {{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            @if(Auth::check())
                                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=FFFFFF&background=263749' }}" class="user-avatar">
                                {{ Auth::user()->name }}
                            @endif
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                               <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>