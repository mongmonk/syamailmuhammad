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
        
        <!-- PWA Meta Tags -->
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Syamail">
        <meta name="application-name" content="Syamail">
        <meta name="msapplication-TileColor" content="#4f46e5">
        <meta name="display-mode" content="standalone">
        <meta name="orientation" content="portrait">
        
    <!-- PWA Manifest Link -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
        
        <!-- PWA Status Bar -->
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="theme-color" media="(prefers-color-scheme: light)" content="#4f46e5">
        <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#4f46e5">
        
        <!-- Icons -->
        <link rel="icon" type="image/jpg" href="{{ asset('icon.jpg') }}">
        <link rel="shortcut icon" type="image/jpg" href="{{ asset('icon.jpg') }}">
        <link rel="apple-touch-icon" href="{{ asset('icon.jpg') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icon.jpg') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icon.jpg') }}">
        <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('icon.jpg') }}">
        
        <!-- iOS Splash Screen Images -->
        <link rel="apple-touch-startup-image" href="{{ asset('icon.jpg') }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="{{ asset('icon.jpg') }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="{{ asset('icon.jpg') }}" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)">
        <link rel="apple-touch-startup-image" href="{{ asset('icon.jpg') }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)">
        <link rel="icon" sizes="32x32" href="{{ asset('icon.jpg') }}">
        <link rel="icon" sizes="16x16" href="{{ asset('icon.jpg') }}">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            /* Local Arabic font */
            @font-face {
                font-family: 'LPMQ IsepMisbah';
                src: url('{{ asset('LPMQ IsepMisbah.ttf') }}') format('truetype');
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }

            /* Set default alphabet (Latin) font to Mulish */
            html, body {
                font-family: 'Mulish', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            }

            /* Arabic text styles use local font */
            .arabic-text, .arabic-heading {
                font-family: 'LPMQ IsepMisbah', serif;
                line-height: 2.0;
                font-size: 1.25rem;
            }
            .arabic-heading {
                font-weight: 600;
                font-size: 1.5rem;
            }
        </style>

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
                        <a href="{{ route('posts.index') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('posts.*') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">Galeri</a>
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
                    <a href="{{ route('posts.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('posts.*') ? 'bg-gray-100 text-emerald-700' : 'text-gray-700 hover:bg-gray-100 hover:text-emerald-700' }}">Galeri</a>
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
                        <a href="{{ route('posts.index') }}" class="block text-gray-600 hover:text-emerald-700">Galeri</a>
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
                            <a href="{{ config('app.contact_instagram_url') }}" aria-label="Instagram" class="text-gray-500 hover:text-emerald-700">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2c2.7 0 3 0 4.1.1 1 0 1.5.2 1.9.4.5.2.8.4 1.2.8.4.4.6.7.8 1.2.2.4.3.9.4 1.9.1 1.1.1 1.4.1 4.1s0 3-.1 4.1c0 1-.2 1.5-.4 1.9-.2.5-.4.8-.8 1.2-.4.4-.7.6-1.2.8-.4.2-.9.3-1.9.4-1.1.1-1.4.1-4.1.1s-3 0-4.1-.1c-1 0-1.5-.2-1.9-.4-.5-.2-.8-.4-1.2-.8-.4-.4-.6-.7-.8-1.2-.2-.4-.3-.9-.4-1.9C2 13 2 12.7 2 10s0-3 .1-4.1c0-1 .2-1.5.4-1.9.2-.5.4-.8.8-1.2.4-.4.7-.6 1.2-.8.4-.2.9-.3 1.9-.4C7 2 7.3 2 10 2zm0-2C7.3 0 7 0 5.9.1 4.8.1 4 .3 3.4.6 2.8.9 2.3 1.2 1.8 1.8 1.2 2.3.9 2.8.6 3.4.3 4 .1 4.8.1 5.9 0 7 0 7.3 0 10s0 3 .1 4.1c0 1.1.2 1.9.5 2.5.3.6.6 1.1 1.2 1.7.5.5 1.1.8 1.7 1.2.6.3 1.4.5 2.5.5 1.1.1 1.4.1 4.1.1s3 0 4.1-.1c1.1 0 1.9-.2 2.5-.5.6-.3 1.1-.6 1.7-1.2.5-.5.8-1.1 1.2-1.7.3-.6.5-1.4.5-2.5.1-1.1.1-1.4.1-4.1s0-3-.1-4.1c0-1.1-.2-1.9-.5-2.5-.3-.6-.6-1.1-1.2-1.7C17.2 1.2 16.7.9 16.1.6 15.5.3 14.7.1 13.6.1 12.5 0 12.2 0 9.5 0h.5zm0 4.9c-2.8 0-5.1 2.3-5.1 5.1s2.3 5.1 5.1 5.1 5.1-2.3 5.1-5.1-2.3-5.1-5.1-5.1zm0 8.4c-1.8 0-3.3-1.5-3.3-3.3s1.5-3.3 3.3-3.3 3.3 1.5 3.3 3.3-1.5 3.3-3.3 3.3zm5.4-9.2c-.7 0-1.2.5-1.2 1.2s.5 1.2 1.2 1.2 1.2-.5 1.2-1.2-.5-1.2-1.2-1.2z"/></svg>
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

        // PWA Install Prompt
        let deferredPrompt = null;
        
        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('beforeinstallprompt event triggered');
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;
            // Check if not already installed
            if (!isInStandaloneMode()) {
                showInstallPrompt();
            }
        });

        // Check if app is installed
        function isInStandaloneMode() {
            return (window.matchMedia('(display-mode: standalone)').matches) || 
                   (window.navigator.standalone) || // for iOS
                   document.referrer.includes('android-app://');
        }
        
        // Listen for appinstalled event
        window.addEventListener('appinstalled', (e) => {
            console.log('PWA was installed');
            // Clear the deferredPrompt
            deferredPrompt = null;
            // Hide the install prompt if visible
            const prompt = document.querySelector('.pwa-install-prompt');
            if (prompt) {
                prompt.remove();
            }
            // Save to localStorage that app was installed
            localStorage.setItem('pwa-installed', 'true');
        });
        
        function showInstallPrompt() {
            // Check if already installed or dismissed
            if (localStorage.getItem('pwa-install-dismissed') === 'true') {
                return;
            }
            
            // Remove existing prompt if any
            const existingPrompt = document.querySelector('.pwa-install-prompt');
            if (existingPrompt) {
                existingPrompt.remove();
            }
            
            const installButton = document.createElement('div');
            installButton.className = 'pwa-install-prompt';
            installButton.innerHTML = `
                <div class="fixed bottom-4 right-4 bg-emerald-600 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold">Install Syamail</div>
                            <div class="text-sm opacity-90">Pasang aplikasi di perangkat Anda</div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button type="button" onclick="dismissPWAInstall()" class="text-white hover:bg-emerald-700 p-1 rounded">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <button type="button" onclick="installPWA()" class="bg-white text-emerald-600 px-3 py-1 rounded text-sm font-medium hover:bg-emerald-50">
                                Install
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(installButton);
        }
        
        async function installPWA() {
            console.log('installPWA called');
            
            // Handle iOS Safari
            if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
                alert('Untuk menginstal di iOS: \n1. Ketuk ikon Share/Bagikan\n2. Scroll ke bawah\n3. Ketuk "Add to Home Screen"');
                return;
            }

            // Handle Chrome/Edge/Other browsers
            if (deferredPrompt) {
                try {
                    // Show the prompt
                    await deferredPrompt.prompt();
                    console.log('Install prompt shown');
                    
                    // Wait for the user to respond to the prompt
                    const choiceResult = await deferredPrompt.userChoice;
                    console.log('User choice:', choiceResult.outcome);
                    
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the installation');
                    } else {
                        console.log('User dismissed the installation');
                    }
                } catch (err) {
                    console.error('Error during PWA installation:', err);
                    // If prompt fails, try manual installation
                    const isChrome = /Chrome/.test(navigator.userAgent);
                    if (isChrome) {
                        alert('Untuk menginstal manual:\n1. Klik titik tiga di kanan atas Chrome (⋮)\n2. Pilih "Install Syamail"');
                    }
                } finally {
                    // Clear the prompt
                    deferredPrompt = null;
                    
                    // Remove install button
                    const prompt = document.querySelector('.pwa-install-prompt');
                    if (prompt) {
                        prompt.remove();
                    }
                }
            } else {
                console.log('No installation prompt available, showing manual instructions');
                // Show manual installation instructions
                const isChrome = /Chrome/.test(navigator.userAgent);
                if (isChrome) {
                    alert('Untuk menginstal:\n1. Klik titik tiga di kanan atas Chrome (⋮)\n2. Pilih "Install Syamail"');
                }
            }
        }
        
        function dismissPWAInstall() {
            localStorage.setItem('pwa-install-dismissed', 'true');
            const prompt = document.querySelector('.pwa-install-prompt');
            if (prompt) {
                prompt.remove();
            }
        }
        // Check if running as standalone PWA
        function isPWA() {
            return window.matchMedia('(display-mode: standalone)').matches ||
                   window.navigator.standalone ||
                   document.referrer.includes('android-app://') ||
                   window.matchMedia('(display-mode: fullscreen)').matches ||
                   window.matchMedia('(display-mode: minimal-ui)').matches;
        }

        // Show install button for Safari on iOS
        function showIOSInstall() {
            const installButton = document.createElement('div');
            installButton.className = 'pwa-install-prompt';
            installButton.innerHTML = `
                <div class="fixed bottom-4 right-4 bg-emerald-600 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold">Install Syamail di iOS</div>
                            <div class="text-sm opacity-90">1. Ketuk ikon Share/Bagikan<br>2. Pilih "Add to Home Screen"</div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <button type="button" onclick="dismissPWAInstall()" class="text-white hover:bg-emerald-700 p-1 rounded">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(installButton);
        }

        // Function to check browser and show appropriate install UI
        function checkInstallability() {
            // Get browser info
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            const isPWAInstalled = isPWA();
            
            console.log('Checking installability:', { 
                isIOS, 
                isSafari, 
                isPWA: isPWAInstalled,
                hasServiceWorker: 'serviceWorker' in navigator,
                hasBeforeInstallPrompt: !!deferredPrompt,
                isHttps: location.protocol === 'https:',
                displayMode: window.matchMedia('(display-mode: standalone)').matches ? 'standalone' : 'browser'
            });
            
            if (isPWAInstalled) {
                console.log('Already installed as PWA');
                return;
            }

            if (isIOS) {
                console.log('iOS device detected, showing Safari install instructions');
                showIOSInstall();
            } else if (!deferredPrompt) {
                console.log('Creating manual install button');
                const installDiv = document.createElement('div');
                installDiv.className = 'pwa-install-prompt';
                installDiv.innerHTML = `
                    <div class="fixed bottom-4 right-4 bg-emerald-600 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">Install Syamail</div>
                                <div class="text-sm opacity-90">Pasang aplikasi di perangkat Anda</div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button type="button" onclick="dismissPWAInstall()" class="text-white hover:bg-emerald-700 p-1 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="installPWA()" class="bg-white text-emerald-600 px-3 py-1 rounded text-sm font-medium hover:bg-emerald-50">
                                    Install
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(installDiv);
            }
        }

                // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', async () => {
                try {
                    if (document.location.protocol === 'http:') {
                        console.log('Service Worker requires HTTPS');
                        return;
                    }

                    const registration = await navigator.serviceWorker.register('/sw.js', { 
                        scope: '/',
                        updateViaCache: 'none'
                    });
                    
                    console.log('SW registered:', registration);
                    
                    // Check if already installed
                    const relList = document.createElement('link').relList;
                    const manifestSupport = relList && relList.supports && relList.supports('manifest');
                    const standalone = window.matchMedia('(display-mode: standalone)').matches;
                    
                    if (manifestSupport && !standalone) {
                        // Wait for service worker to be ready
                        await navigator.serviceWorker.ready;
                        console.log('Service Worker is ready');

                        // Show install prompt if available
                        if (deferredPrompt) {
                            console.log('Install prompt available, showing banner');
                            showInstallPrompt();
                        } else {
                            console.log('No install prompt available, waiting for beforeinstallprompt');
                            checkInstallability();
                        }
                    } else {
                        console.log('PWA already installed or manifest not supported');
                    }
                } catch (error) {
                    console.error('SW registration failed:', error);
                }
            });
        }
        
        // Initialize PWA checks when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            if (!isPWA()) {
                checkInstallability();
            }
        });
        </script>
    </body>
</html>