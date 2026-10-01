@extends('layouts.app')

@section('title', 'Master Data Siswa')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="top-banner p-6 rounded-2xl text-white shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold tracking-wide uppercase mb-2">
                <i data-lucide="users" class="w-3.5 h-3.5"></i> Master Data
            </div>
            <h1 class="text-2xl font-bold heading-font">Daftar Siswa Terdaftar</h1>
            <p class="text-xs text-blue-100 mt-1">Data master siswa dipisah dari data absensi agar dapat memantau siapa yang belum & sudah hadir</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <form action="{{ route('students.seed_default') }}" method="POST" onsubmit="return confirm('Muat ulang dataset siswa resmi dari seeder?')">
                @csrf
                <button type="submit" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium text-xs backdrop-blur-md transition flex items-center gap-1.5 border border-white/20">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset Master
                </button>
            </form>
            <button onclick="openAddStudentModal()" class="px-4 py-2 bg-white text-blue-800 rounded-xl font-semibold text-xs shadow hover:bg-blue-50 transition flex items-center gap-1.5">
                <i data-lucide="user-plus" class="w-4 h-4 text-blue-600"></i> Tambah Siswa Baru
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="glass-card p-4 border-l-4 border-l-purple-600">
            <span class="text-[11px] font-medium text-slate-500 uppercase">Total Siswa</span>
            <div class="text-2xl font-bold text-slate-800 heading-font mt-1">{{ $stats['total'] }}</div>
            <span class="text-[10px] text-slate-400">Terdaftar di Sistem</span>
        </div>

        <div class="glass-card p-4 border-l-4 border-l-emerald-500">
            <span class="text-[11px] font-medium text-slate-500 uppercase">Siswa Aktif</span>
            <div class="text-2xl font-bold text-emerald-700 heading-font mt-1">{{ $stats['active'] }}</div>
            <span class="text-[10px] text-emerald-600 font-medium">Aktif Mengikuti Absensi</span>
        </div>

        <div class="glass-card p-4 border-l-4 border-l-blue-500">
            <span class="text-[11px] font-medium text-slate-500 uppercase">Anggota OSIS</span>
            <div class="text-2xl font-bold text-blue-700 heading-font mt-1">{{ $stats['osis'] }}</div>
            <span class="text-[10px] text-blue-600">Pengurus OSIS</span>
        </div>

        <div class="glass-card p-4 border-l-4 border-l-indigo-500">
            <span class="text-[11px] font-medium text-slate-500 uppercase">Anggota MPK</span>
            <div class="text-2xl font-bold text-indigo-700 heading-font mt-1">{{ $stats['mpk'] }}</div>
            <span class="text-[10px] text-indigo-600">Pengurus MPK</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card p-4">
        <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500 mb-1">Cari Nama / NIS</label>
                <div class="relative">
                    <input type="text" name="search" id="student_search_input" value="{{ request('search') }}" placeholder="Ketik nama atau NIS (contoh: a, assy)..." oninput="debounceSearch(this.form)" autocomplete="off" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Filter Kelas</label>
                <select name="kelas" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ request('kelas') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Filter Organisasi</label>
                <select name="osis_mpk" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua</option>
                    <option value="OSIS" {{ request('osis_mpk') === 'OSIS' ? 'selected' : '' }}>OSIS</option>
                    <option value="MPK" {{ request('osis_mpk') === 'MPK' ? 'selected' : '' }}>MPK</option>
                    <option value="Bukan" {{ request('osis_mpk') === 'Bukan' ? 'selected' : '' }}>Bukan OSIS/MPK</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Student Table Card -->
    <div class="glass-card overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="list" class="w-4 h-4 text-purple-600"></i> Master Database Siswa
            </h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} siswa</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/70 border-b border-slate-200 text-[11px] font-semibold text-slate-600 uppercase tracking-wider">
                        <th class="p-3">No</th>
                        <th class="p-3">NIS</th>
                        <th class="p-3">Nama Siswa</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">OSIS / MPK</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3 text-slate-400 font-mono text-[11px]">
                                {{ $students->firstItem() + $index }}
                            </td>
                            <td class="p-3 font-mono text-slate-600">
                                {{ $student->nis ?? '-' }}
                            </td>
                            <td class="p-3 font-semibold text-slate-800">
                                {{ $student->nama }}
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md font-medium text-[11px]">
                                    {{ $student->kelas }}
                                </span>
                            </td>
                            <td class="p-3">
                                @if($student->osis_mpk === 'OSIS')
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 font-bold rounded text-[10px]">OSIS</span>
                                @elseif($student->osis_mpk === 'MPK')
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 font-bold rounded text-[10px]">MPK</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if($student->is_active)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full font-bold text-[10px]">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full text-[10px]">Nonaktif</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick='editStudent(@json($student))' class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Siswa">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus siswa {{ addslashes($student->nama) }} from master data?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Siswa">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-xs">
                                <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                Belum ada data siswa. Klik tombol "Tambah Siswa Baru" atau "Reset Master" untuk mengisi data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Add Student -->
<div id="addStudentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-5 h-5 text-purple-600"></i> Tambah Master Siswa Baru
            </h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('students.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">NIS (Opsional)</label>
                <input type="text" name="nis" placeholder="Nomor Induk Siswa..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
                <input type="text" name="nama" required placeholder="Nama lengkap..." class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas *</label>
                <select name="kelas" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status OSIS / MPK</label>
                <select name="osis_mpk" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500">
                    <option value="Bukan">Bukan OSIS / MPK</option>
                    <option value="OSIS">Pengurus OSIS</option>
                    <option value="MPK">Pengurus MPK</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-xl text-xs font-semibold hover:bg-purple-700 transition">
                    Simpan Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Student -->
<div id="editStudentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="edit-3" class="w-5 h-5 text-blue-600"></i> Edit Master Siswa
            </h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editStudentForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">NIS (Opsional)</label>
                <input type="text" name="nis" id="edit_nis" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
                <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas *</label>
                <select name="kelas" id="edit_kelas" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status OSIS / MPK</label>
                <select name="osis_mpk" id="edit_osis_mpk" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl">
                    <option value="Bukan">Bukan OSIS / MPK</option>
                    <option value="OSIS">Pengurus OSIS</option>
                    <option value="MPK">Pengurus MPK</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddStudentModal() {
        document.getElementById('addStudentModal').classList.remove('hidden');
        document.getElementById('addStudentModal').classList.add('flex');
    }

    function closeAddModal() {
        document.getElementById('addStudentModal').classList.add('hidden');
        document.getElementById('addStudentModal').classList.remove('flex');
    }

    function editStudent(student) {
        document.getElementById('edit_nis').value = student.nis || '';
        document.getElementById('edit_nama').value = student.nama;
        document.getElementById('edit_kelas').value = student.kelas;
        document.getElementById('edit_osis_mpk').value = student.osis_mpk || 'Bukan';

        const form = document.getElementById('editStudentForm');
        form.action = `/siswa/${student.id}`;

        document.getElementById('editStudentModal').classList.remove('hidden');
        document.getElementById('editStudentModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editStudentModal').classList.add('hidden');
        document.getElementById('editStudentModal').classList.remove('flex');
    }

    let searchTimer;
    function debounceSearch(form) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            form.submit();
        }, 350);
    }
</script>
@endpush
