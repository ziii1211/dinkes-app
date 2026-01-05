<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login Dinas Kesehatan ' }}</title>
    
    {{-- PERBAIKAN: Mengganti logo favicon ke file baru, bukan logo pemprov --}}
    <link rel="icon" href="{{ asset('Coat_of_arms_of_South_Kalimantan.svg.png') }}" type="image/png">
    
    {{-- Google Fonts: Plus Jakarta Sans lebih modern untuk UI Dashboard --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-900">
    {{-- Slot utama, biarkan kosong tanpa style wrapper agar fleksibel --}}
    {{ $slot }}

    @livewireScripts
</body>
</html>