@auth
<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <!-- Sidebar Toggle Button on the Left -->
    <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
        <i class="fas fa-bars"></i>
    </a>

    <!-- Right Side of Navbar -->
    <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle nav-link-lg nav-link-user" data-bs-toggle="dropdown" aria-expanded="false">
                <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle me-1">
                <div class="d-sm-none d-lg-inline-block">
                    Namaskaram, {{ substr(auth()->user()->name, 0, 10) }}
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <div class="dropdown-header">
                        Welcome, {{ substr(auth()->user()->name, 0, 10) }}
                    </div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user me-2"></i> Edit Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a href="{{ route('logout') }}" class="dropdown-item text-danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>
@endauth
