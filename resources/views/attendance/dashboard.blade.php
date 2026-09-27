@extends('layouts.app')

@section('title', 'Dashboard Presensi Siswa')

@section('content')
<div class="space-y-4">
    <!-- Header Title Card -->
    <div class="glass-card p-4 border-l-4 border-l-blue-600 flex items-center justify-between">
        <div>
            <h2 class="heading-font text-lg font-bold text-slate-800">Dashboard Presensi</h2>
            <p class="text-xs text-slate-500">Ringkasan & statistik absensi siswa</p>
        </div>

        <!-- Date Filter -->
        <form method="GET" action="{{ route('absen.dashboard') }}" class="flex items-center gap-1.5">
            <input type="date" name="tanggal" value="{{ $selectedDate }}" onchange="this.form.submit()" class="px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg font-semibold focus:ring-2 focus:ring-blue-500 focus:outline-none bg-slate-50">
        </form>
    </div>

    <!-- Stats Grid Overview -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Total Absen -->
        <div class="glass-card p-3.5 border-t-2 border-t-blue-600 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase">Total Input</div>
                <div class="heading-font text-2xl font-extrabold text-blue-900 mt-0.5">{{ $stats['total'] }}</div>
                <div class="text-[10px] text-slate-400">Siswa tercatat</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Hadir -->
        <div class="glass-card p-3.5 border-t-2 border-t-emerald-600 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-emerald-700 uppercase">Hadir</div>
                <div class="heading-font text-2xl font-extrabold text-emerald-900 mt-0.5">{{ $stats['hadir'] }}</div>
                <div class="text-[10px] text-emerald-600">Siswa di kelas</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Sakit / Izin -->
        <div class="glass-card p-3.5 border-t-2 border-t-amber-500 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-amber-700 uppercase">Sakit / Izin</div>
                <div class="heading-font text-2xl font-extrabold text-amber-900 mt-0.5">{{ $stats['sakit'] + $stats['izin'] }}</div>
                <div class="text-[10px] text-amber-600">S: {{ $stats['sakit'] }} | I: {{ $stats['izin'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <i data-lucide="file-text" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Alfa -->
        <div class="glass-card p-3.5 border-t-2 border-t-red-600 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-red-700 uppercase">Alfa</div>
                <div class="heading-font text-2xl font-extrabold text-red-900 mt-0.5">{{ $stats['alfa'] }}</div>
                <div class="text-[10px] text-red-600">Tanpa keterangan</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center">
                <i data-lucide="user-x" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Completeness Card (Form Kelengkapan %) -->
    <div class="glass-card p-4 border-l-4 border-l-red-600">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h3 class="heading-font text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-red-600"></i> Kelengkapan Seragam & Peralatan
                </h3>
                <p class="text-[11px] text-slate-500">Persentase kedisiplinan siswa hadir</p>
            </div>
            <span class="heading-font text-xl font-extrabold text-blue-700">{{ $stats['kelengkapan_pct'] }}%</span>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200">
            <div class="bg-gradient-to-r from-blue-600 to-red-600 h-full rounded-full transition-all duration-500" style="width: {{ $stats['kelengkapan_pct'] }}%"></div>
        </div>

        <div class="flex justify-between text-[11px] text-slate-600 mt-2 font-medium">
            <span class="flex items-center gap-1 text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Lengkap: <strong>{{ $stats['lengkap'] }}</strong>
            </span>
            <span class="flex items-center gap-1 text-red-700">
                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tidak Lengkap: <strong>{{ $stats['tidak_lengkap'] }}</strong>
            </span>
        </div>
    </div>

    <!-- Class Breakdown (Hierarchical Order) -->
    <div class="glass-card p-4">
        <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2">
            <h3 class="heading-font text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="bar-chart-2" class="w-4 h-4 text-blue-600"></i> Rekapitualisasi Per Kelas
            </h3>
            <span class="text-[10px] text-slate-400">Kelas RPL & DKV</span>
        </div>

        <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
            @forelse($classBreakdown as $row)
                @php
                    $hadirPct = $row->total_siswa > 0 ? round(($row->total_hadir / $row->total_siswa) * 100) : 0;
                @endphp
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200">{{ $row->kelas }}</span>
                        <span class="text-[11px] font-semibold text-slate-600">{{ $row->total_hadir }} / {{ $row->total_siswa }} Hadir ({{ $hadirPct }}%)</span>
                    </div>

                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden flex">
                        <div class="bg-blue-600 h-full" style="width: {{ $row->total_siswa > 0 ? ($row->total_hadir / $row->total_siswa)*100 : 0 }}%"></div>
                        <div class="bg-amber-500 h-full" style="width: {{ $row->total_siswa > 0 ? ($row->total_sakit / $row->total_siswa)*100 : 0 }}%"></div>
                        <div class="bg-cyan-500 h-full" style="width: {{ $row->total_siswa > 0 ? ($row->total_izin / $row->total_siswa)*100 : 0 }}%"></div>
                        <div class="bg-red-600 h-full" style="width: {{ $row->total_siswa > 0 ? ($row->total_alfa / $row->total_siswa)*100 : 0 }}%"></div>
                    </div>

                    <div class="flex items-center justify-between text-[10px] text-slate-500 font-medium pt-0.5">
                        <span class="text-blue-700">H: {{ $row->total_hadir }}</span>
                        <span class="text-amber-700">S: {{ $row->total_sakit }}</span>
                        <span class="text-cyan-700">I: {{ $row->total_izin }}</span>
                        <span class="text-red-700">A: {{ $row->total_alfa }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-xs text-slate-400">Tidak ada data presensi pada tanggal ini.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
