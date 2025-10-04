<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@hasSection('title') @yield('title') @else {{ config('app.name', 'Syamail Muhammadiyah') }} @endif</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Arabic Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Amiri+Quran&family=KFGQPC+Uthman+Taha+Naskh&family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        @endif

        @env('dusk')
        <style>
          /* Override efek fade agar elemen terlihat saat Dusk menjalankan assert */
          [data-page-container]{opacity:1 !important; transition:none !important}
          #mobile-menu{opacity:1 !important; transition:none !important}
        </style>
        @endenv
    </head>
    <body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
        <!-- Navigation -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-xl font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
                            {{ config('app.name', 'Syamail Muhammadiyah') }}
                        </a>
                    </div>
                    <nav class="hidden md:flex items-center space-x-6" role="navigation" aria-label="Primary">
                        <a href="{{ url('/') }}" class="text-sm font-medium transition-colors {{ request()->is('/') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Beranda</a>
                        <a href="{{ route('chapters.index') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('chapters.*') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Bab</a>
                        <a href="{{ route('search.form') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('search.form') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Cari</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('profile.show') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('profile.*') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Profil</a>
                                @if (! auth()->user()->hasVerifiedEmail())
                                    <a href="{{ route('verification.notice') }}" class="text-sm font-medium transition-colors text-yellow-700 hover:text-yellow-800">Verifikasi</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium transition-colors text-gray-700 hover:text-emerald-700">Keluar</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium transition-colors text-gray-700 hover:text-emerald-700">Login</a>
                                @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-medium transition-colors text-gray-700 hover:text-emerald-700">Register</a>
                                @endif
                            @endauth
                        @endif
                    </nav>

                    <!-- Mobile menu button -->
                    <button id="mobile-menu-button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-emerald-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-600"
                        aria-controls="mobile-menu"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Mobile menu -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 transition-opacity duration-200 opacity-0" role="navigation" aria-label="Mobile Primary">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('/') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Beranda</a>
                    <a href="{{ route('chapters.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('chapters.*') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Bab</a>
                    <a href="{{ route('search.form') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('search.form') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Cari</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('profile.show') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('profile.*') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Profil</a>
                            @if (! auth()->user()->hasVerifiedEmail())
                            <a href="{{ route('verification.notice') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors text-yellow-700 hover:bg-yellow-100 hover:text-yellow-800">Verifikasi</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                                @csrf
                                <button type="submit" class="w-full text-left text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-emerald-700 rounded-md">
                                    Keluar
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors text-gray-700 hover:bg-gray-100 hover:text-emerald-700">Login</a>
                            @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors text-gray-700 hover:bg-gray-100 hover:text-emerald-700">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 py-6 transition-opacity duration-500 opacity-0" data-page-container>
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500">
                    <div>&copy; {{ date('Y') }} {{ config('app.name', 'Syamail Muhammadiyah') }}. All rights reserved.</div>
                    <div class="mt-2 sm:mt-0">
                        <span class="text-gray-400">v</span> {{ app()->version() }}
                    </div>
                </div>
            </div>
        </footer>

        @stack('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('mobile-menu-button');
            var menu = document.getElementById('mobile-menu');
            if (btn && menu) {
                btn.addEventListener('click', function () {
                    var willShow = menu.classList.contains('hidden');
                    if (willShow) {
                        // show with fade-in
                        menu.classList.remove('hidden');
                        requestAnimationFrame(function () {
                            menu.classList.remove('opacity-0');
                            menu.classList.add('opacity-100');
                        });
                        btn.setAttribute('aria-expanded', 'true');
                    } else {
                        // hide with fade-out
                        menu.classList.add('opacity-0');
                        menu.classList.remove('opacity-100');
                        btn.setAttribute('aria-expanded', 'false');
                        setTimeout(function () {
                            menu.classList.add('hidden');
                        }, 200);
                    }
                });
            }
            var page = document.querySelector('[data-page-container]');
            if (page) {
                requestAnimationFrame(function () {
                    page.classList.remove('opacity-0');
                    page.classList.add('opacity-100');
                });
            }
        });
        </script>
    </body>
</html>