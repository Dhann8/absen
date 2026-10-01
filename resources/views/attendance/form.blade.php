@extends('layouts.app')

@section('title', 'Form Input Presensi Siswa')

@section('content')
<div class="space-y-4">
    <!-- Continuous Submission Live Alert Banner -->
    <div id="live-alert" class="hidden p-4 rounded-xl text-sm font-medium transition-all duration-300 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div id="live-alert-icon" class="w-8 h-8 rounded-full flex items-center justify-center text-white shrink-0"></div>
            <span id="live-alert-text"></span>
        </div>
        <button type="button" onclick="document.getElementById('live-alert').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Attendance Form -->
    <div class="glass-card p-5 border-t-4 border-t-blue-600">
        <form id="attendanceForm" action="{{ route('absen.store') }}" method="POST" class="space-y-4">
            @csrf

            @if(isset($selectedStudent) && $selectedStudent)
                <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                        <span>Presensi untuk master siswa: <strong>{{ $selectedStudent->nama }}</strong> ({{ $selectedStudent->kelas }})</span>
                    </div>
                    <a href="{{ route('absen.form') }}" class="text-blue-600 hover:text-blue-800 underline text-[11px]">Batal</a>
                </div>
            @endif

            <!-- Tanggal -->
            <div class="flex items-center justify-between text-xs text-slate-500 pb-2 border-b border-slate-100">
                <label for="tanggal" class="font-semibold text-slate-700 flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i> Tanggal Presensi:
                </label>
                <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" class="px-2.5 py-1 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
            </div>

            <!-- Nama Siswa -->
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                    Nama Siswa <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $selectedStudent->nama ?? '') }}" required autocomplete="off" placeholder="Ketik nama lengkap siswa..." class="w-full px-3.5 py-2.5 pl-10 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                    </div>

                    <!-- Autocomplete Suggestions Dropdown -->
                    <div id="nama-suggestions-box" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-52 overflow-y-auto divide-y divide-slate-100">
                    </div>
                </div>
            </div>

            <!-- Kelas (RPL & DKV) -->
            <div>
                <label for="kelas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                    Kelas <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select id="kelas" name="kelas" required class="w-full px-3.5 py-2.5 pl-10 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none bg-white transition-all shadow-sm appearance-none">
                        <option value="" disabled {{ !isset($selectedStudent) ? 'selected' : '' }}>Pilih Kelas</option>
                        @foreach($classes as $classItem)
                            <option value="{{ $classItem }}" {{ (isset($selectedStudent) && $selectedStudent->kelas === $classItem) ? 'selected' : '' }}>{{ $classItem }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-red-600"></i>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <!-- OSIS / MPK Radio Button -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1">
                    Organisasi <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <!-- OSIS (Blue Background) -->
                    <label class="relative flex items-center justify-between p-3.5 border-2 border-blue-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all select-none group bg-blue-50/50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-600 has-[:checked]:text-white shadow-sm">
                        <input type="radio" name="osis_mpk" value="OSIS" checked class="hidden osis-mpk-radio">
                        <span class="text-sm font-extrabold group-has-[:checked]:text-white text-blue-900">OSIS</span>
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-blue-600 group-has-[:checked]:text-white opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>

                    <!-- MPK (Red Background) -->
                    <label class="relative flex items-center justify-between p-3.5 border-2 border-red-200 rounded-xl cursor-pointer hover:border-red-400 transition-all select-none group bg-red-50/50 has-[:checked]:border-red-600 has-[:checked]:bg-red-600 has-[:checked]:text-white shadow-sm">
                        <input type="radio" name="osis_mpk" value="MPK" class="hidden osis-mpk-radio">
                        <span class="text-sm font-extrabold group-has-[:checked]:text-white text-red-900">MPK</span>
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-red-600 group-has-[:checked]:text-white opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>
                </div>
            </div>

            <!-- Status Kehadiran -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Status Kehadiran <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-2.5">
                    <!-- Hadir -->
                    <label class="relative flex items-center justify-between p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-300 transition-all select-none group has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/80 shadow-sm">
                        <input type="radio" name="status" value="hadir" checked class="hidden status-radio">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs group-has-[:checked]:bg-blue-600 group-has-[:checked]:text-white">
                                H
                            </span>
                            <span class="text-sm font-semibold text-slate-800">Hadir</span>
                        </div>
                        <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>

                    <!-- Sakit -->
                    <label class="relative flex items-center justify-between p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-amber-300 transition-all select-none group has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/80 shadow-sm">
                        <input type="radio" name="status" value="sakit" class="hidden status-radio">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs group-has-[:checked]:bg-amber-500 group-has-[:checked]:text-white">
                                S
                            </span>
                            <span class="text-sm font-semibold text-slate-800">Sakit</span>
                        </div>
                        <i data-lucide="check-circle" class="w-5 h-5 text-amber-500 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>

                    <!-- Izin -->
                    <label class="relative flex items-center justify-between p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-cyan-300 transition-all select-none group has-[:checked]:border-cyan-600 has-[:checked]:bg-cyan-50/80 shadow-sm">
                        <input type="radio" name="status" value="izin" class="hidden status-radio">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-xs group-has-[:checked]:bg-cyan-600 group-has-[:checked]:text-white">
                                I
                            </span>
                            <span class="text-sm font-semibold text-slate-800">Izin</span>
                        </div>
                        <i data-lucide="check-circle" class="w-5 h-5 text-cyan-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>

                    <!-- Alfa -->
                    <label class="relative flex items-center justify-between p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-red-300 transition-all select-none group has-[:checked]:border-red-600 has-[:checked]:bg-red-50/80 shadow-sm">
                        <input type="radio" name="status" value="alfa" class="hidden status-radio">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold text-xs group-has-[:checked]:bg-red-600 group-has-[:checked]:text-white">
                                A
                            </span>
                            <span class="text-sm font-semibold text-slate-800">Alfa</span>
                        </div>
                        <i data-lucide="check-circle" class="w-5 h-5 text-red-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>
                </div>
            </div>

            <!-- CONDITIONAL BLOCK 1: Form Kelengkapan (Hanya jika status = Hadir) -->
            <div id="kelengkapan-wrapper" class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-100 transition-all duration-300">
                <label class="block text-xs font-bold text-blue-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i> Kelengkapan Atribut
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <!-- Lengkap -->
                    <label class="flex items-center justify-center gap-2 p-2.5 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-blue-400 has-[:checked]:bg-blue-600 has-[:checked]:text-white has-[:checked]:border-blue-600 text-slate-700 text-xs font-semibold transition-all">
                        <input type="radio" name="kelengkapan" value="lengkap" checked class="hidden kelengkapan-radio">
                        <i data-lucide="check" class="w-4 h-4"></i> Lengkap
                    </label>

                    <!-- Tidak Lengkap -->
                    <label class="flex items-center justify-center gap-2 p-2.5 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-red-400 has-[:checked]:bg-red-600 has-[:checked]:text-white has-[:checked]:border-red-600 text-slate-700 text-xs font-semibold transition-all">
                        <input type="radio" name="kelengkapan" value="tidak_lengkap" class="hidden kelengkapan-radio">
                        <i data-lucide="x" class="w-4 h-4"></i> Tidak Lengkap
                    </label>
                </div>
            </div>

            <!-- CONDITIONAL BLOCK 2: Keterangan / Catatan Alasan -->
            <div id="keterangan-wrapper" class="hidden transition-all duration-300">
                <label id="keterangan-label" for="keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                    Keterangan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <textarea id="keterangan" name="keterangan" rows="2" placeholder="Tuliskan keterangan/alasan lengkap..." class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:outline-none transition-all shadow-sm"></textarea>
                </div>
                <p id="keterangan-hint" class="text-[11px] text-slate-500 mt-1 italic"></p>
            </div>

            <!-- Submit Button (Glow Accent) -->
            <div class="pt-2 flex items-center gap-2">
                <button type="submit" id="submitBtn" class="flex-1 bg-gradient-to-r from-blue-700 via-blue-600 to-red-600 hover:from-blue-800 hover:to-red-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 btn-active-scale">
                    <span>SIMPAN ABSENSI</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('attendanceForm');
    const namaInput = document.getElementById('nama');
    const kelasSelect = document.getElementById('kelas');
    const statusRadios = document.querySelectorAll('.status-radio');
    const kelengkapanRadios = document.querySelectorAll('.kelengkapan-radio');
    
    const kelengkapanWrapper = document.getElementById('kelengkapan-wrapper');
    const keteranganWrapper = document.getElementById('keterangan-wrapper');
    const keteranganInput = document.getElementById('keterangan');
    const keteranganLabel = document.getElementById('keterangan-label');
    const keteranganHint = document.getElementById('keterangan-hint');
    
    const liveAlert = document.getElementById('live-alert');
    const liveAlertText = document.getElementById('live-alert-text');
    const liveAlertIcon = document.getElementById('live-alert-icon');
    const submitBtn = document.getElementById('submitBtn');

    // Focus initial name field for instant continuous typing
    namaInput.focus();

    // Autocomplete Suggestions logic
    const suggestionsBox = document.getElementById('nama-suggestions-box');
    let debounceTimer;

    function highlightMatch(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<mark class="bg-amber-200 text-slate-900 rounded-xs px-0.5 font-bold">$1</mark>');
    }

    namaInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();

        if (query.length < 1) {
            suggestionsBox.classList.add('hidden');
            suggestionsBox.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const currentKelas = kelasSelect ? kelasSelect.value : '';
                const response = await fetch(`{{ route('absen.suggestions') }}?q=${encodeURIComponent(query)}&kelas=${encodeURIComponent(currentKelas)}`);
                const data = await response.json();

                if (data.length > 0) {
                    suggestionsBox.innerHTML = '';
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition-colors text-xs border-b border-slate-100 last:border-b-0';
                        
                        const highlightedNama = highlightMatch(item.nama, query);
                        const nisText = item.nis ? `<span class="text-slate-400 font-mono text-[10px] ml-1.5">(NIS: ${highlightMatch(item.nis, query)})</span>` : '';

                        div.innerHTML = `
                            <div>
                                <span class="font-bold text-slate-800 text-xs">${highlightedNama}</span>
                                ${nisText}
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">${item.kelas}</span>
                                ${item.osis_mpk && item.osis_mpk !== 'Bukan' && item.osis_mpk !== 'bukan' ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold text-white ${item.osis_mpk === 'MPK' ? 'bg-indigo-600' : 'bg-blue-600'}">${item.osis_mpk}</span>` : ''}
                            </div>
                        `;
                        div.addEventListener('click', () => {
                            namaInput.value = item.nama;
                            
                            // Attach hidden student_id input if not already present
                            let studentIdInput = form.querySelector('input[name="student_id"]');
                            if (!studentIdInput) {
                                studentIdInput = document.createElement('input');
                                studentIdInput.type = 'hidden';
                                studentIdInput.name = 'student_id';
                                form.appendChild(studentIdInput);
                            }
                            studentIdInput.value = item.id;

                            if (item.kelas && kelasSelect) {
                                kelasSelect.value = item.kelas;
                            }
                            if (item.osis_mpk && (item.osis_mpk === 'OSIS' || item.osis_mpk === 'MPK')) {
                                const osisRadio = document.querySelector(`input[name="osis_mpk"][value="${item.osis_mpk}"]`);
                                if (osisRadio) osisRadio.checked = true;
                            }
                            suggestionsBox.classList.add('hidden');
                        });
                        suggestionsBox.appendChild(div);
                    });
                    suggestionsBox.classList.remove('hidden');
                } else {
                    suggestionsBox.classList.add('hidden');
                }
            } catch (err) {
                console.error(err);
            }
        }, 50);
    });

    // Close suggestions box when clicking outside
    document.addEventListener('click', (e) => {
        if (!namaInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('hidden');
        }
    });

    // Logic to toggle dynamic form fields
    function updateFormConditions() {
        const selectedStatus = document.querySelector('input[name="status"]:checked')?.value;
        const selectedKelengkapan = document.querySelector('input[name="kelengkapan"]:checked')?.value;

        if (selectedStatus === 'hadir') {
            kelengkapanWrapper.classList.remove('hidden');

            if (selectedKelengkapan === 'tidak_lengkap') {
                keteranganWrapper.classList.remove('hidden');
                keteranganInput.required = true;
                keteranganLabel.innerHTML = 'Keterangan Tidak Lengkap <span class="text-red-500">*</span>';
                keteranganHint.textContent = 'Jelaskan barang / seragam yang tidak lengkap.';
            } else {
                keteranganWrapper.classList.add('hidden');
                keteranganInput.required = false;
                keteranganInput.value = '';
            }
        } else {
            // Status is Sakit, Izin, or Alfa
            kelengkapanWrapper.classList.add('hidden');
            keteranganWrapper.classList.remove('hidden');
            keteranganInput.required = true;
            keteranganLabel.innerHTML = `Keterangan Alasan (${selectedStatus.toUpperCase()}) <span class="text-red-500">*</span>`;
            keteranganHint.textContent = `Tuliskan detail alasan mengapa siswa ${selectedStatus}.`;
        }
    }

    // Attach change event listeners
    statusRadios.forEach(radio => radio.addEventListener('change', updateFormConditions));
    kelengkapanRadios.forEach(radio => radio.addEventListener('change', updateFormConditions));

    // Run once on initial load
    updateFormConditions();

    // Continuous AJAX Submission Handler
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const originalBtnHtml = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> <span>Menyimpan...</span>';
        lucide.createIcons();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Show success toast
                liveAlert.className = 'p-3.5 rounded-xl text-xs font-semibold shadow-sm flex items-center justify-between transition-all bg-emerald-50 text-emerald-800 border border-emerald-200 mb-4';
                liveAlertIcon.className = 'w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0';
                liveAlertIcon.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>';
                liveAlertText.textContent = result.message;
                liveAlert.classList.remove('hidden');

                // Continuous Reset: Clear name and notes, reset status to Hadir, keep selected Class for fast entry!
                namaInput.value = '';
                document.querySelector('input[name="osis_mpk"][value="OSIS"]').checked = true;
                document.querySelector('input[name="status"][value="hadir"]').checked = true;
                document.querySelector('input[name="kelengkapan"][value="lengkap"]').checked = true;
                keteranganInput.value = '';

                updateFormConditions();

                // Re-focus on student name field for seamless continuous entries!
                namaInput.focus();

            } else {
                let errorMsg = result.message || 'Terjadi kesalahan saat menyimpan.';
                if (result.errors) {
                    const firstErrorKey = Object.keys(result.errors)[0];
                    if (firstErrorKey && result.errors[firstErrorKey][0]) {
                        errorMsg = result.errors[firstErrorKey][0];
                    }
                }
                throw new Error(errorMsg);
            }
        } catch (err) {
            liveAlert.className = 'p-3.5 rounded-xl text-xs font-semibold shadow-sm flex items-center justify-between transition-all bg-red-50 text-red-800 border border-red-200 mb-4';
            liveAlertIcon.className = 'w-7 h-7 rounded-lg bg-red-600 text-white flex items-center justify-center shrink-0';
            liveAlertIcon.innerHTML = '<i data-lucide="alert-triangle" class="w-4 h-4"></i>';
            liveAlertText.textContent = err.message || 'Gagal terhubung ke server.';
            liveAlert.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
            lucide.createIcons();
        }
    });
});
</script>
@endpush
