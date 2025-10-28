<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Syamail Muhammadiyah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/jpg" href="{{ asset('icon.jpg') }}">
    <meta name="theme-color" content="#4f46e5">
    
    <!-- PWA Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Syamail">
    <meta name="application-name" content="Syamail">
    
    <!-- PWA Manifest Link -->
    @if(file_exists(public_path('build/manifest.webmanifest')))
        <link rel="manifest" href="{{ asset('build/manifest.webmanifest') }}">
    @elseif(file_exists(public_path('manifest.json')))
        <link rel="manifest" href="{{ asset('manifest.json') }}">
    @endif
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center p-8 max-w-md mx-auto">
        <img src="{{ asset('icon.jpg') }}" alt="Syamail" class="w-24 h-24 mx-auto mb-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Anda Sedang Offline</h1>
        <p class="text-gray-600 mb-6">Konten yang tersimpan tetap dapat diakses. Periksa koneksi internet Anda untuk mendapatkan konten terbaru.</p>
        
        <div class="space-y-3">
            <button onclick="window.location.reload()" class="w-full bg-emerald-600 text-white px-6 py-3 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                Coba Lagi
            </button>
            <a href="/" class="block w-full bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                Kembali ke Beranda
            </a>
        </div>
        
        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-blue-900 mb-2">📖 Fitur Offline</h3>
            <ul class="text-sm text-blue-800 text-left space-y-1">
                <li>• Baca hadis yang sudah diakses sebelumnya</li>
                <li>• Akses bab dan konten tersimpan</li>
                <li>• Gunakan fitur pencarian untuk konten lokal</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Service Worker Registration for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then((registration) => {
                        console.log('SW registered: ', registration);
                    })
                    .catch((registrationError) => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
        
        // Check connection status periodically
        setInterval(async () => {
            try {
                const response = await fetch('/offline', {
                    method: 'HEAD',
                    cache: 'no-cache'
                });
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                // Still offline
            }
        }, 30000); // Check every 30 seconds
    </script>
</body>
</html>