<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@hasSection('title') @yield('title') @else {{ config('app.name', 'Syamail Muhammadiyah') }} @endif</title>
        @yield('meta')
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="theme-color" content="#4f46e5">
        
        <!-- Icons -->
        <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('icon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('icon.png') }}">
        <link rel="icon" sizes="32x32" href="{{ asset('icon.png') }}">
        <link rel="icon" sizes="16x16" href="{{ asset('icon.png') }}">
        
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
@php($isLanding = request()->routeIs('home') || request()->is('/'))
@if(!$isLanding)
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
                        <a href="{{ route('posts.index') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('posts.*') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Artikel</a>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Dashboard</a>
                                @role('admin')
                                <a href="{{ url('/admin') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('admin.*') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Admin</a>
                                @endrole
                                <a href="{{ route('profile.show') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('profile.*') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Profil</a>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium transition-colors text-gray-700 hover:text-emerald-700">Keluar</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium transition-colors text-gray-700 hover:text-emerald-700">Login</a>
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
                    <a href="{{ route('posts.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('posts.*') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Artikel</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Dashboard</a>
                            @role('admin')
                            <a href="{{ url('/admin') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.*') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Admin</a>
                            @endrole
                            <a href="{{ route('profile.show') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('profile.*') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Profil</a>
                            <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                                @csrf
                                <button type="submit" class="w-full text-left text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-emerald-700 rounded-md">
                                    Keluar
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors text-gray-700 hover:bg-gray-100 hover:text-emerald-700">Login</a>
                        @endauth
                    @endif
                </div>
            </div>
        </header>
        @endif

        <!-- Main Content -->
        <main id="main-content" class="flex-1" role="main">
            <div class="max-w-7xl mx-auto px-4 py-6 transition-opacity duration-500 opacity-0" data-page-container>
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t" role="contentinfo">
            <div class="max-w-7xl mx-auto px-4 py-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
                    <div>
                        <a href="{{ url('/') }}" class="text-base font-bold text-emerald-700 hover:text-emerald-800">{{ config('app.name', 'Syamail Muhammadiyah') }}</a>
                        <p class="mt-2 text-gray-600">{{ config('app.footer_description') }}</p>
                        <div class="mt-3 flex items-center gap-3 text-gray-500" aria-label="Lencana kepercayaan">
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 2l6 3v5c0 4-2.686 7.5-6 9-3.314-1.5-6-5-6-9V5l6-3z" clip-rule="evenodd" />
                                </svg>
                                SSL Secured
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5 8a3 3 0 116 0v1h1a1 1 0 011 1v5a1 1 0 01-1 1H4a1 1 0 01-1-1V10a1 1 0 011-1h1V8zM7 8a1 1 0 112 0v1H7V8z" clip-rule="evenodd" />
                                </svg>
                                Privasi Dilindungi
                            </span>
                        </div>
                    </div>
                    <nav class="space-y-2" aria-label="Navigasi Footer">
                        <div class="font-semibold text-gray-900">Navigasi</div>
                        <a href="{{ url('/') }}" class="block text-gray-600 hover:text-emerald-700">Beranda</a>
                        <a href="{{ route('chapters.index') }}" class="block text-gray-600 hover:text-emerald-700">Bab</a>
                        <a href="{{ route('posts.index') }}" class="block text-gray-600 hover:text-emerald-700">Artikel</a>
                        <a href="#demo" class="block text-gray-600 hover:text-emerald-700">Demo</a>
                    </nav>
                    <nav class="space-y-2" aria-label="Kebijakan">
                        <div class="font-semibold text-gray-900">Kebijakan</div>
                        <a href="#kebijakan-privasi" class="block text-gray-600 hover:text-emerald-700">Kebijakan Privasi</a>
                        <a href="#kebijakan-privasi" class="block text-gray-600 hover:text-emerald-700">Ketentuan Layanan</a>
                        <a href="#kebijakan-privasi" class="block text-gray-600 hover:text-emerald-700">Keamanan</a>
                    </nav>
                    <div class="space-y-2" aria-label="Kontak">
                        <div class="font-semibold text-gray-900">Kontak</div>
                        <a href="mailto:{{ config('app.contact_email') }}" class="block text-gray-600 hover:text-emerald-700">{{ config('app.contact_email') }}</a>
                        <a href="tel:{{ config('app.contact_phone') }}" class="block text-gray-600 hover:text-emerald-700">{{ config('app.contact_phone') }}</a>
                        <div class="flex items-center gap-3 pt-1">
                            <a href="{{ config('app.contact_twitter_url') }}" aria-label="Twitter" class="text-gray-500 hover:text-emerald-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.29 18c7.547 0 11.675-6.155 11.675-11.49 0-.175 0-.349-.012-.522A8.18 8.18 0 0020 3.92a8.27 8.27 0 01-2.357.637 4.077 4.077 0 001.804-2.23 8.19 8.19 0 01-2.605.981A4.106 4.106 0 009.85 6.034a11.65 11.65 0 01-8.457-4.23 4.05 4.05 0 001.27 5.478A4.1 4.1 0 01.8 6.75v.052A4.11 4.11 0 004.09 10.8a4.13 4.13 0 01-1.852.07 4.1 4.1 0 003.83 2.82A8.233 8.233 0 010 16.407 11.616 11.616 0 006.29 18" /></svg>
                            </a>
                            <a href="{{ config('app.contact_linkedin_url') }}" aria-label="LinkedIn" class="text-gray-500 hover:text-emerald-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5 3.5A2.5 2.5 0 112.5 6 2.5 2.5 0 015 3.5zM3 7h4v10H3zm6 0h3.6v1.7h.05a3.95 3.95 0 013.55-1.95c3.8 0 4.5 2.5 4.5 5.7V17H17v-3.9c0-.9 0-2.1-1.3-2.1s-1.5 1-1.5 2V17H10z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500">
                    <div>&copy; {{ date('Y') }} {{ config('app.name', 'Syamail Muhammadiyah') }}. All rights reserved.</div>
                    <div class="mt-2 sm:mt-0">
                        <span class="text-gray-400">v</span> {{ config('app.version') }}
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