<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        try {
            const themePreference = localStorage.getItem('nexstep-theme');
            document.documentElement.dataset.theme = themePreference === 'dark' ? 'dark' : 'light';
        } catch {
            document.documentElement.dataset.theme = 'light';
        }
    </script>

    <title>Nexstep</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="guest-body">
    {{ $slot }}
    <button type="button" class="theme-toggle guest-theme-toggle" data-theme-toggle aria-label="Ativar modo escuro" title="Ativar modo escuro" aria-pressed="false">
        <i class="bi bi-moon-stars" aria-hidden="true"></i>
    </button>

    @livewireScripts
</body>
</html>
