<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Presensi Siswa Mobile') - App Presensi</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-blue: #1d4ed8;
            --primary-blue-hover: #1e40af;
            --primary-red: #dc2626;
            --primary-red-hover: #b91c1c;
            --accent-gradient: linear-gradient(135deg, #1d4ed8 0%, #dc2626 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            padding-bottom: 85px; /* space for mobile bottom navbar */
        }

        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        .gradient-text {
            background: linear-gradient(135deg, #1d4ed8 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .blue-red-border {
            border-image: linear-gradient(to right, #1d4ed8, #dc2626) 1;
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            border-radius: 1rem;
        }

        .top-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 45%, #b91c1c 100%);
        }

        /* Ripple / Focus active states */
        .btn-active-scale:active {
            transform: scale(0.97);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="min-h-screen antialiased flex flex-col justify-between">



    <!-- Main Responsive Container -->
    <main class="w-full max-w-4xl mx-auto px-4 py-5 flex-grow">
        <!-- Toast Notification Flash -->
        @if(session('success'))
            <div id="flash-toast" class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3 shadow-sm transition-all duration-300">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                <div class="flex-grow font-medium">{{ session('success') }}</div>
                <button onclick="document.getElementById('flash-toast').remove()" class="text-emerald-500 hover:text-emerald-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="flash-toast-error" class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3 shadow-sm transition-all duration-300">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                <div class="flex-grow font-medium">{{ session('error') }}</div>
                <button onclick="document.getElementById('flash-toast-error').remove()" class="text-rose-500 hover:text-rose-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Navigation Bar -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-lg">
        <div class="max-w-xl mx-auto px-4 py-1.5 flex justify-around items-center">
            
            <!-- Form Absen -->
            <a href="{{ route('absen.form') }}" class="flex flex-col items-center py-1 group {{ request()->routeIs('absen.form') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <div class="p-1.5 rounded-xl transition-all duration-200 {{ request()->routeIs('absen.form') ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200' : 'group-hover:bg-slate-100' }}">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] mt-0.5">Input Absen</span>
            </a>

            <!-- Status (Sudah/Belum Absen) -->
            <a href="{{ route('absen.status') }}" class="flex flex-col items-center py-1 group {{ request()->routeIs('absen.status') ? 'text-indigo-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <div class="p-1.5 rounded-xl transition-all duration-200 {{ request()->routeIs('absen.status') ? 'bg-indigo-50 text-indigo-700 shadow-sm ring-1 ring-indigo-200' : 'group-hover:bg-slate-100' }}">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] mt-0.5">Sudah/Belum</span>
            </a>

            <!-- Master Data Siswa -->
            <a href="{{ route('students.index') }}" class="flex flex-col items-center py-1 group {{ request()->routeIs('students.index') ? 'text-purple-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <div class="p-1.5 rounded-xl transition-all duration-200 {{ request()->routeIs('students.index') ? 'bg-purple-50 text-purple-700 shadow-sm ring-1 ring-purple-200' : 'group-hover:bg-slate-100' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] mt-0.5">Data Siswa</span>
            </a>

            <!-- Dashboard -->
            <a href="{{ route('absen.dashboard') }}" class="flex flex-col items-center py-1 group {{ request()->routeIs('absen.dashboard') ? 'text-blue-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <div class="p-1.5 rounded-xl transition-all duration-200 {{ request()->routeIs('absen.dashboard') ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200' : 'group-hover:bg-slate-100' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] mt-0.5">Dashboard</span>
            </a>

            <!-- Data Absen -->
            <a href="{{ route('absen.records') }}" class="flex flex-col items-center py-1 group {{ request()->routeIs('absen.records') ? 'text-red-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <div class="p-1.5 rounded-xl transition-all duration-200 {{ request()->routeIs('absen.records') ? 'bg-red-50 text-red-700 shadow-sm ring-1 ring-red-200' : 'group-hover:bg-slate-100' }}">
                    <i data-lucide="table" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] mt-0.5">Rekap Absen</span>
            </a>

        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
