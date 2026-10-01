@extends('layouts.app')

@section('title', 'Status Absen - Sudah & Belum Absen')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="top-banner p-6 rounded-2xl text-white shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold tracking-wide uppercase mb-2">
                <i data-lucide="user-check" class="w-3.5 h-3.5"></i> Monitoring Presensi
            </div>
            <h1 class="text-2xl font-bold heading-font">Status Kehadiran Siswa</h1>
            <p class="text-xs text-blue-100 mt-1">Cek siswa yang sudah absen vs belum absen berdasarkan data master</p>
        </div>
        <a href="{{ route('absen.form') }}" class="px-4 py-2 bg-white text-blue-800 rounded-xl font-semibold text-xs shadow hover:bg-blue-50 transition inline-flex items-center gap-2 shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4 text-blue-600"></i> Input Absen Baru
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card p-4">
        <form method="GET" action="{{ route('absen.status') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal</label>
                <div class="relative">
                    <input type="date" name="tanggal" value="{{ $selectedDate }}" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Pilih Kelas</label>
                <select name="kelas" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ $selectedKelas === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Cari Nama / NIS</label>
                <div class="relative flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama siswa..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-xl text-xs font-medium hover:bg-blue-700 transition">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Overview KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="glass-card p-4 border-l-4 border-l-slate-600">
            <span class="text-[11px] font-medium text-slate-500 uppercase">Total Siswa</span>
            <div class="text-2xl font-bold text-slate-800 heading-font mt-1">{{ $stats['total_siswa'] }}</div>
            <span class="text-[10px] text-slate-400">Master Terdaftar</span>
        </div>

        <div class="glass-card p-4 border-l-4 border-l-emerald-500 bg-emerald-50/20">
            <span class="text-[11px] font-medium text-emerald-700 uppercase">Sudah Absen</span>
            <div class="text-2xl font-bold text-emerald-700 heading-font mt-1">{{ $stats['sudah_absen'] }}</div>
            <span class="text-[10px] text-emerald-600 font-medium">{{ $stats['persentase_absen'] }}% Terabsen</span>
        </div>

        <div class="glass-card p-4 border-l-4 border-l-rose-500 bg-rose-50/20">
            <span class="text-[11px] font-medium text-rose-700 uppercase">Belum Absen</span>
            <div class="text-2xl font-bold text-rose-700 heading-font mt-1">{{ $stats['belum_absen'] }}</div>
            <span class="text-[10px] text-rose-600 font-medium">Perlu Diabsen</span>
        </div>

        <div class="glass-card p-4 border-l-4 border-l-blue-600">
            <span class="text-[11px] font-medium text-blue-700 uppercase">Progres Kehadiran</span>
            <div class="w-full bg-slate-100 rounded-full h-2.5 mt-3 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $stats['persentase_absen'] }}%"></div>
            </div>
            <span class="text-[10px] text-slate-500 mt-2 block">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div x-data="{ tab: 'belum' }" class="space-y-4">
        
        <!-- Tab Navigation Buttons -->
        <div class="flex border-b border-slate-200">
            <button @click="tab = 'belum'" :class="tab === 'belum' ? 'border-rose-600 text-rose-700 font-bold bg-rose-50/50' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 text-xs border-b-2 font-medium rounded-t-xl transition-all inline-flex items-center gap-2">
                <i data-lucide="user-x" class="w-4 h-4 text-rose-600"></i>
                Belum Absen
                <span class="px-2 py-0.5 text-[10px] rounded-full bg-rose-100 text-rose-800 font-bold">{{ $stats['belum_absen'] }}</span>
            </button>

            <button @click="tab = 'sudah'" :class="tab === 'sudah' ? 'border-emerald-600 text-emerald-700 font-bold bg-emerald-50/50' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 text-xs border-b-2 font-medium rounded-t-xl transition-all inline-flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i>
                Sudah Absen
                <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-100 text-emerald-800 font-bold">{{ $stats['sudah_absen'] }}</span>
            </button>
        </div>

        <!-- TAB 1: BELUM ABSEN LIST -->
        <div x-show="tab === 'belum'" class="space-y-3">
            @if($belumAbsen->isEmpty())
                <div class="glass-card p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Semua Siswa Sudah Absen!</h3>
                    <p class="text-xs text-slate-500 mt-1">Tidak ada siswa yang belum absen untuk tanggal dan kelas ini.</p>
                </div>
            @else
                <div class="glass-card overflow-hidden">
                    <div class="p-3 bg-rose-50/80 border-b border-rose-100 flex justify-between items-center">
                        <span class="text-xs font-semibold text-rose-800 flex items-center gap-1.5">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i> Daftar Siswa Belum Presensi
                        </span>
                        <span class="text-[11px] text-rose-600 font-medium">{{ $belumAbsen->count() }} orang</span>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                        @foreach($belumAbsen as $student)
                            <div class="p-3.5 hover:bg-slate-50 transition flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-rose-100 text-rose-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($student->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800">{{ $student->nama }}</div>
                                        <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-medium rounded-md text-[11px]">{{ $student->kelas }}</span>
                                            @if($student->nis)
                                                <span class="text-slate-400">NIS: {{ $student->nis }}</span>
                                            @endif
                                            @if($student->osis_mpk && $student->osis_mpk !== 'Bukan')
                                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded">{{ $student->osis_mpk }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                    <button onclick="quickAbsen({{ $student->id }}, '{{ addslashes($student->nama) }}', '{{ $student->kelas }}', '{{ $student->osis_mpk }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                                        <i data-lucide="check-square" class="w-3.5 h-3.5"></i> Absen Sekarang
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- TAB 2: SUDAH ABSEN LIST -->
        <div x-show="tab === 'sudah'" class="space-y-3" style="display: none;">
            @if($sudahAbsen->isEmpty())
                <div class="glass-card p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="inbox" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Data Absensi</h3>
                    <p class="text-xs text-slate-500 mt-1">Belum ada siswa yang mengisi absensi pada tanggal ini.</p>
                </div>
            @else
                <div class="glass-card overflow-hidden">
                    <div class="p-3 bg-emerald-50/80 border-b border-emerald-100 flex justify-between items-center">
                        <span class="text-xs font-semibold text-emerald-800 flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i> Daftar Siswa Sudah Presensi
                        </span>
                        <span class="text-[11px] text-emerald-600 font-medium">{{ $sudahAbsen->count() }} orang</span>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                        @foreach($sudahAbsen as $student)
                            @php $att = $student->attendance; @endphp
                            <div class="p-3.5 hover:bg-slate-50 transition flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($student->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800">{{ $student->nama }}</div>
                                        <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-medium rounded-md text-[11px]">{{ $student->kelas }}</span>
                                            @if($att && $att->created_at)
                                                <span class="text-slate-400"><i data-lucide="clock" class="w-3 h-3 inline"></i> {{ $att->created_at->format('H:i') }} WIB</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($att)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                            @if($att->status === 'hadir') bg-emerald-100 text-emerald-800 border border-emerald-200
                                            @elseif($att->status === 'sakit') bg-amber-100 text-amber-800 border border-amber-200
                                            @elseif($att->status === 'izin') bg-blue-100 text-blue-800 border border-blue-200
                                            @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                                            {{ $att->status }}
                                        </span>

                                        @if($att->status === 'hadir' && $att->kelengkapan === 'tidak_lengkap')
                                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-semibold border border-amber-200 rounded">Tidak Lengkap</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Quick Absen Modal -->
<div id="quickAbsenModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="user-check" class="w-5 h-5 text-blue-600"></i> Presensi Cepat Siswa
            </h3>
            <button onclick="closeQuickModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="quickForm" action="{{ route('absen.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="student_id" id="modal_student_id">
            <input type="hidden" name="nama" id="modal_nama">
            <input type="hidden" name="kelas" id="modal_kelas">
            <input type="hidden" name="osis_mpk" id="modal_osis_mpk">
            <input type="hidden" name="tanggal" value="{{ $selectedDate }}">

            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div id="modal_student_display" class="font-bold text-slate-800 text-sm"></div>
                <div id="modal_class_display" class="text-xs text-slate-500"></div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kehadiran</label>
                <div class="grid grid-cols-4 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="hadir" checked onchange="toggleQuickFields()" class="peer sr-only">
                        <div class="py-2 text-center text-xs font-bold border border-slate-200 rounded-xl peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 transition">
                            Hadir
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="sakit" onchange="toggleQuickFields()" class="peer sr-only">
                        <div class="py-2 text-center text-xs font-bold border border-slate-200 rounded-xl peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600 transition">
                            Sakit
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="izin" onchange="toggleQuickFields()" class="peer sr-only">
                        <div class="py-2 text-center text-xs font-bold border border-slate-200 rounded-xl peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition">
                            Izin
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="alfa" onchange="toggleQuickFields()" class="peer sr-only">
                        <div class="py-2 text-center text-xs font-bold border border-slate-200 rounded-xl peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 transition">
                            Alfa
                        </div>
                    </label>
                </div>
            </div>

            <div id="quick_kelengkapan_box">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Form Kelengkapan</label>
                <select name="kelengkapan" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                    <option value="lengkap">Lengkap</option>
                    <option value="tidak_lengkap">Tidak Lengkap</option>
                </select>
            </div>

            <div id="quick_keterangan_box" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Catatan</label>
                <textarea name="keterangan" rows="2" placeholder="Tuliskan keterangan..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeQuickModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition">
                    Simpan Presensi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Alpine.js CDN for tab navigation -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    function quickAbsen(id, nama, kelas, osisMpk) {
        document.getElementById('modal_student_id').value = id;
        document.getElementById('modal_nama').value = nama;
        document.getElementById('modal_kelas').value = kelas;
        document.getElementById('modal_osis_mpk').value = osisMpk || 'Bukan';

        document.getElementById('modal_student_display').innerText = nama;
        document.getElementById('modal_class_display').innerText = 'Kelas: ' + kelas + (osisMpk && osisMpk !== 'Bukan' ? ' • ' + osisMpk : '');

        document.getElementById('quickAbsenModal').classList.remove('hidden');
        document.getElementById('quickAbsenModal').classList.add('flex');
    }

    function closeQuickModal() {
        document.getElementById('quickAbsenModal').classList.add('hidden');
        document.getElementById('quickAbsenModal').classList.remove('flex');
    }

    function toggleQuickFields() {
        const status = document.querySelector('input[name="status"]:checked').value;
        const kelengkapanBox = document.getElementById('quick_kelengkapan_box');
        const keteranganBox = document.getElementById('quick_keterangan_box');

        if (status === 'hadir') {
            kelengkapanBox.classList.remove('hidden');
            keteranganBox.classList.add('hidden');
        } else {
            kelengkapanBox.classList.add('hidden');
            keteranganBox.classList.remove('hidden');
        }
    }
</script>
@endpush
