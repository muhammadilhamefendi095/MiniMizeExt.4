<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'MINI MIZE EXT.4') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar entry-fade">
        <div class="logo-container">
            <a href="{{ route('home') }}" style="color:#FFF200; font-weight:900; letter-spacing:1px; text-decoration:none; font-size:1.1rem;">
                MINI MIZE EXT.4
            </a>
        </div>
    </nav>

    <div style="min-height:70vh; display:flex; align-items:center; justify-content:center;">
        {{ $slot }}
    </div>

    <script src="{{ asset('js/site.js') }}"></script>
</body>
</html>
