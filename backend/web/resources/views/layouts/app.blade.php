<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Disaster Relief System') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* Global styles that should apply to all pages */
        .navbar{
            position: fixed;
            top: 0;
            left: 220px; 
            right: 0;
            z-index: 1020;
            transition: all 0.3s;
        }
    
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            color: white;
            padding-top: 20px;
            position: fixed;
            width: 220px;
            left: 0;
            top: 0;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
            height: 100vh;
        }

        .sidebar.collapsed {
            width: 0;
            padding: 0;
            overflow: hidden;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 10px 20px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: 0.3s;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #495057;
        }

        .sidebar i {
            color: white;
            margin-right: 10px;
            width: 20px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.5rem;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .search-box {
            position: relative;
            margin-right: 15px;
        }
        
        .search-box input {
            background: #ccc;
            border: none;
            padding-left: 35px;
            border-radius: 20px;
            width: 200px;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            width: 250px;
            background: #fff;
            box-shadow: none;
        }
        
        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: black;
        }
        
        .action-icon {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            margin: 0 10px;
            position: relative;
            transition: all 0.3s;
        }
        
        .action-icon:hover {
            color: white;
            transform: translateY(-2px);
        }

        footer{
            background-color: rgb(38, 55, 73);
            color: #ccc;
            padding: 40px 0;
            margin-left: 220px;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
            width: calc(100% - 220px); 
        }

        .quick-links a{
            color: #fff;
            text-decoration: none;
        }

        .social-icons a {
            color: #fff;
            margin: 0 10px;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: blue;
        }

        .sidebar.collapsed ~ .navbar {
            left: 0;
        }

        .sidebar.collapsed ~ .main-content,
        .sidebar.collapsed ~ footer {
            margin-left: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="app">
        @if(Auth::check())
            @include('partials.sidebar')
        @endif
        @include('partials.header')
        
        <main class="py-4">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
        
        @include('partials.footer')
    </div>

    @stack('scripts')
</body>
</html>