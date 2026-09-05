<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Librairie'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:var(--space-6);background:var(--color-paper-soft)">

    <div style="width:100%;max-width:420px">
        <div style="text-align:center;margin-bottom:var(--space-8)">
            <a href="{{ url('/') }}" class="site-logo" style="font-size:var(--text-3xl)">Choumou3<span>.</span></a>
        </div>

        <div class="card" style="box-shadow:var(--shadow-md)">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="padding-inline-start:var(--space-4);list-style:disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @yield('content')
        </div>

        <p class="text-center text-muted" style="margin-top:var(--space-6);font-size:var(--text-sm)">
            <a href="{{ url('/') }}">&larr; Retour à la boutique</a>
        </p>
    </div>

</body>
</html>
