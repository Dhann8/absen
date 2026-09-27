@extends('layouts.app')

@section('title', 'Data Rekap Absensi Siswa')

@section('content')
<div class="space-y-3">
    <!-- Header Title Card -->
    <div class="glass-card p-3.5 border-l-4 border-l-red-600 flex items-center justify-between gap-2">
        <div>
            <h2 class="heading-font text-base font-bold text-slate-800">Data Absensi Siswa</h2>
        </div>

        <!-- Download & Action Buttons -->
        <div class="flex items-center gap-2">
            <a href="{{ route('absen.download', request()->query()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3 py-2 rounded-xl shadow transition-all flex items-center gap-1.5 btn-active-scale">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Download</span>
            </a>
            <button onclick="window.print()" class="bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs px-3 py-2 rounded-xl shadow transition-all flex items-center gap-1.5 btn-active-scale">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Cetak</span>
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="glass-card p-3 space-y-2">
        <form method="GET" action="{{ route('absen.records') }}" class="space-y-2">
            <div class="grid grid-cols-2 gap-2">
                <!-- Search Name -->
                <div class="col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full px-2 py-1 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <!-- Class -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Kelas</label>
                    <select name="kelas" class="w-full px-2 py-1 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ request('kelas') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Status</label>
                    <select name="status" class="w-full px-2 py-1 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="alfa" {{ request('status') == 'alfa' ? 'selected' : '' }}>Alfa</option>
                    </select>
                </div>

                <!-- Filter Submit / Reset -->
                <div class="flex items-end gap-1.5">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs py-1 px-2.5 rounded-lg shadow-sm">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'tanggal', 'kelas', 'status']))
                        <a href="{{ route('absen.records') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs py-1 px-2 rounded-lg">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Data List Container Card -->
    <div class="glass-card overflow-hidden flex flex-col">
        <!-- Fixed Title Header Bar -->
        <div class="p-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center text-xs font-bold text-slate-700 shrink-0">
            <span>Daftar Absensi (Total: {{ $attendances->total() }})</span>
            <span class="text-[10px] text-slate-500 font-normal">Urutan: Hari & Kelas</span>
        </div>

        <!-- SCROLLABLE COMPACT DATA LIST -->
        <div class="divide-y divide-slate-100 max-h-[50vh] overflow-y-auto">
            @forelse($attendances as $row)
                @php
                    $badgeStyle = match($row->status) {
                        'hadir' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'sakit' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'izin' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                        'alfa' => 'bg-red-100 text-red-800 border-red-200',
                        default => 'bg-slate-100 text-slate-800 border-slate-200'
                    };

                        <!-- Detail Button -->
                        <button type="button" data-item="{{ json_encode([
                            'nama' => $row->nama,
                            'kelas' => $row->kelas,
                            'osis_mpk' => $row->osis_mpk ?? 'Bukan',
                            'status' => ucfirst($row->status),
                            'kelengkapan' => ($row->status == 'hadir') ? ($row->kelengkapan == 'tidak_lengkap' ? 'Tidak Lengkap' : 'Lengkap') : '-',
                            'keterangan' => $row->keterangan ?? '-',
                            'tanggal' => $row->tanggal ? $row->tanggal->format('d/m/Y') : '-',
                            'waktu_masuk' => $row->created_at ? $row->created_at->format('H:i') . ' WIB' : '-',
                        ]) }}" onclick="openDetailModal(this)" class="bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold px-2 py-1 rounded-lg text-[10px] border border-blue-200 transition-colors">
                            Detail
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('absen.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data presensi {{ $row->nama }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 p-1 transition-colors">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-xs text-slate-400 space-y-2">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-300"></i>
                    <div>Tidak ada data presensi yang ditemukan.</div>
                </div>
            @endforelse
        </div>

        <!-- Custom Modern Mobile Pagination Footer -->
        @if($attendances->hasPages())
            <div class="p-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs gap-2 shrink-0">
                <div class="text-slate-600 font-medium text-[11px]">
                    Hal <span class="font-extrabold text-blue-700">{{ $attendances->currentPage() }}</span> / <span class="font-extrabold text-slate-800">{{ $attendances->lastPage() }}</span>
                </div>

                <div class="flex items-center gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($attendances->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-slate-400 bg-slate-200/60 font-semibold cursor-not-allowed text-[11px] flex items-center gap-1">
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Prev
                        </span>
                    @else
                        <a href="{{ $attendances->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-white bg-blue-600 hover:bg-blue-700 font-bold shadow-sm transition-all text-[11px] flex items-center gap-1 btn-active-scale">
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Prev
                        </a>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($attendances->hasMorePages())
                        <a href="{{ $attendances->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-white bg-blue-600 hover:bg-blue-700 font-bold shadow-sm transition-all text-[11px] flex items-center gap-1 btn-active-scale">
                            Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-slate-400 bg-slate-200/60 font-semibold cursor-not-allowed text-[11px] flex items-center gap-1">
                            Next <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Sleek Detail Modal Popup -->
<div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all">
    <div class="glass-card w-full max-w-sm overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="p-4 bg-gradient-to-r from-blue-700 to-indigo-700 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="user-check" class="w-5 h-5"></i>
                <h3 class="heading-font text-sm font-bold">Detail Presensi Siswa</h3>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-white/80 hover:text-white p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-4 space-y-3 text-xs">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400">Nama Siswa</span>
                <div id="modalNama" class="text-sm font-bold text-slate-900 mt-0.5"></div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400">Kelas</span>
                    <div id="modalKelas" class="font-bold text-slate-800 mt-0.5"></div>
                </div>

                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400">Organisasi</span>
                    <div id="modalOrganisasi" class="font-bold mt-0.5"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400">Status Kehadiran</span>
                    <div id="modalStatus" class="font-bold mt-0.5"></div>
                </div>

                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400">Waktu Masuk</span>
                    <div id="modalWaktuMasuk" class="font-semibold text-blue-700 mt-0.5"></div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400">Kelengkapan Atribut</span>
                <div id="modalKelengkapan" class="font-semibold mt-0.5"></div>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400">Keterangan / Catatan</span>
                <div id="modalKeterangan" class="p-2.5 bg-slate-50 rounded-lg text-slate-700 mt-1 font-medium border border-slate-200"></div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3 bg-slate-50 border-t border-slate-100 text-right">
            <button type="button" onclick="closeDetailModal()" class="bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs px-4 py-2 rounded-xl transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDetailModal(elOrItem) {
    const item = (typeof elOrItem.dataset !== 'undefined' && elOrItem.dataset.item) 
        ? JSON.parse(elOrItem.dataset.item) 
        : elOrItem;
    document.getElementById('modalNama').textContent = item.nama;
    document.getElementById('modalKelas').textContent = item.kelas;
    
    // Organisasi
    const orgEl = document.getElementById('modalOrganisasi');
    orgEl.textContent = item.osis_mpk;
    orgEl.className = `font-bold ${item.osis_mpk === 'MPK' ? 'text-red-600' : (item.osis_mpk === 'OSIS' ? 'text-blue-600' : 'text-slate-600')}`;

    // Status
    const statusEl = document.getElementById('modalStatus');
    statusEl.textContent = item.status;

    // Waktu Masuk
    document.getElementById('modalWaktuMasuk').textContent = `${item.tanggal} - ${item.waktu_masuk}`;

    // Kelengkapan
    const kelengkapanEl = document.getElementById('modalKelengkapan');
    kelengkapanEl.textContent = item.kelengkapan;
    kelengkapanEl.className = `font-semibold ${item.kelengkapan === 'Tidak Lengkap' ? 'text-red-600' : 'text-emerald-600'}`;

    // Keterangan
    document.getElementById('modalKeterangan').textContent = item.keterangan;

    document.getElementById('detailModal').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}
</script>
@endpush
