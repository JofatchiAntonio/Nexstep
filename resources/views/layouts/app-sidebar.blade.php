<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title', 'Os meus quadros') · Nexstep</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="workspace-body">
    <div class="workspace-shell">
        <aside class="workspace-sidebar">
            <a href="{{ route('boards.index') }}" class="workspace-brand text-decoration-none">
                <span class="workspace-brand-mark"><i class="bi bi-kanban-fill"></i></span>
                <span>
                    <strong>Nexstep</strong>
                    <small>Área de trabalho</small>
                </span>
            </a>

            <div class="sidebar-section-label">MENU PRINCIPAL</div>
            <nav class="workspace-nav" aria-label="Navegação principal">
                <a href="{{ route('boards.index') }}" class="workspace-nav-link {{ request()->routeIs('boards.index', 'boards.show') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Os meus quadros</span>
                </a>
                <a href="{{ route('boards.trash') }}" class="workspace-nav-link {{ request()->routeIs('boards.trash') ? 'active' : '' }}">
                    <i class="bi bi-trash3"></i>
                    <span>Lixeira</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="sidebar-help-card">
                    <span class="help-icon"><i class="bi bi-lightbulb"></i></span>
                    <strong>Um passo de cada vez</strong>
                    <p>Organize as suas ideias e acompanhe o progresso dos seus projetos.</p>
                </div>

                <div class="workspace-user">
                    <a href="{{ route('profile.show') }}" class="user-avatar-link" aria-label="Editar perfil">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="" class="user-avatar-photo">
                        @else
                            <span class="user-avatar">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                        @endif
                    </a>
                    <div class="user-details">
                        <strong>{{ Auth::user()->name }}</strong>
                        <span>{{ Auth::user()->email }}</span>
                    </div>
                    <a href="{{ route('profile.show') }}" class="logout-button profile-button" aria-label="Editar perfil" title="Editar perfil">
                        <i class="bi bi-person-gear"></i>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-button" aria-label="Terminar sessão" title="Terminar sessão">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <div class="topbar-eyebrow">GESTÃO DE PROJETOS</div>
                    <div class="topbar-title">@yield('header', 'Os meus quadros')</div>
                </div>
                <div class="topbar-user">
                    <span class="topbar-status"><i class="bi bi-circle-fill"></i> Sessão ativa</span>
                    <a href="{{ route('profile.show') }}" class="user-avatar-link" aria-label="Editar perfil">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="" class="user-avatar-photo user-avatar-photo-small">
                        @else
                            <span class="user-avatar user-avatar-small">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                        @endif
                    </a>
                </div>
            </header>

            <main class="workspace-content">
                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <footer class="workspace-footer">
                <span>Nexstep</span>
                <span>Organize o trabalho. Alcance os seus objetivos.</span>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
