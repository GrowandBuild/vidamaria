<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts (Build de Produção) -->
        @php
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
                $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            }
        @endphp

        @if (!empty($cssFile))
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if (!empty($jsFile))
            <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased gradient-navy-gold">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-6">
            <!-- Logo -->
            <div class="mb-6 sm:mb-8 animate-fade-in">
                <a href="/" class="transition-transform duration-300 hover:scale-105 block">
                    <img src="{{ asset('logo.svg') }}" alt="Esmalteria Vida Maria" class="h-20 sm:h-28 w-auto mx-auto">
                </a>
            </div>

            <!-- Card de Login -->
            <div class="w-full max-w-md">
                <div class="bg-white shadow-2xl overflow-hidden rounded-2xl border-t-4 border-vm-gold">
                    <div class="px-6 sm:px-8 py-6 sm:py-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
            
            <!-- Rodapé -->
            <p class="mt-6 text-vm-gold-200 text-xs sm:text-sm text-center px-4">
                © {{ date('Y') }} Esmalteria Vida Maria<br class="sm:hidden">
                <span class="hidden sm:inline"> - </span>Elegância e Sofisticação
            </p>
        </div>
    </body>
</html>
