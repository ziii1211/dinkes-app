<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login - Sistem Informasi Kinerja Terintegrasi' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#0d6efd', // Warna biru tombol seperti di gambar
                    }
                }
            }
        }
    </script>
    <style>
        /* CSS untuk membuat background kotak-kotak halus */
        .bg-grid-pattern {
            background-image: 
                linear-gradient(to right, #f0f0f0 1px, transparent 1px),
                linear-gradient(to bottom, #f0f0f0 1px, transparent 1px);
            background-size: 40px 40px;
        }
        /* Efek blur gradasi di pojok background */
        .gradient-blob {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            z-index: -1;
        }
    </style>
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-600 bg-white relative overflow-hidden h-screen flex items-center justify-center">

    <div class="absolute inset-0 bg-grid-pattern z-[-2]"></div>

    <div class="gradient-blob bg-blue-300 -top-40 -left-40"></div>
    <div class="gradient-blob bg-green-200 -bottom-40 -right-40"></div>

    <div class="w-full relative z-10">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>