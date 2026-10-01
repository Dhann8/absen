<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Mobile form for continuous attendance entry.
     */
    public function form(Request $request)
    {
        $classes = Attendance::AVAILABLE_CLASSES;
        $todayCount = Attendance::whereDate('tanggal', Carbon::today())->count();
        $recentEntries = Attendance::with('student')->orderBy('id', 'desc')->take(5)->get();

        // Option to pre-select a student from query parameter
        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = Student::find($request->student_id);
        }

        return view('attendance.form', compact('classes', 'todayCount', 'recentEntries', 'selectedStudent'));
    }

    /**
     * Store new attendance entry (Supports AJAX continuous submission).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'nama' => 'required|string|max:255',
            'kelas' => ['required', 'string', Rule::in(Attendance::AVAILABLE_CLASSES)],
            'osis_mpk' => 'nullable|string|in:OSIS,MPK,Bukan',
            'status' => 'required|in:hadir,sakit,izin,alfa',
            'kelengkapan' => 'nullable|required_if:status,hadir|in:lengkap,tidak_lengkap',
            'keterangan' => 'nullable|required_if:status,sakit,izin,alfa|required_if:kelengkapan,tidak_lengkap|string|max:1000',
            'tanggal' => 'nullable|date|date_format:Y-m-d',
        ]);

        $tanggal = $validated['tanggal'] ?? Carbon::today()->format('Y-m-d');
        $nama = trim($validated['nama']);
        $kelas = $validated['kelas'];

        // Find or link student
        $studentId = $validated['student_id'] ?? null;
        if (! $studentId) {
            $matchedStudent = Student::where('nama', $nama)->where('kelas', $kelas)->first();
            if ($matchedStudent) {
                $studentId = $matchedStudent->id;
            } else {
                // Automatically create master student if not existing yet
                $newStudent = Student::create([
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'osis_mpk' => $validated['osis_mpk'] ?? 'Bukan',
                    'is_active' => true,
                ]);
                $studentId = $newStudent->id;
            }
        }

        // Prevent duplicate attendance entry on the same date for the same student
        $existingQuery = Attendance::whereDate('tanggal', $tanggal);
        if ($studentId) {
            $existingQuery->where(function ($q) use ($studentId, $nama, $kelas) {
                $q->where('student_id', $studentId)
                    ->orWhere(function ($q2) use ($nama, $kelas) {
                        $q2->where('nama', $nama)->where('kelas', $kelas);
                    });
            });
        } else {
            $existingQuery->where('nama', $nama)->where('kelas', $kelas);
        }

        if ($existingQuery->exists()) {
            $formattedDate = Carbon::parse($tanggal)->format('d/m/Y');
            $errorMessage = "Siswa atas nama {$nama} ({$kelas}) sudah dicatat presensinya pada tanggal {$formattedDate}!";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => [
                        'nama' => [$errorMessage],
                    ],
                ], 422);
            }

            return back()->withInput()->with('error', $errorMessage);
        }

        // Clean conditional fields
        $kelengkapan = ($validated['status'] === 'hadir') ? ($validated['kelengkapan'] ?? 'lengkap') : null;
        $keterangan = $validated['keterangan'] ?? null;

        $attendance = Attendance::create([
            'student_id' => $studentId,
            'tanggal' => $tanggal,
            'nama' => $nama,
            'kelas' => $kelas,
            'osis_mpk' => $validated['osis_mpk'] ?? 'Bukan',
            'class_sort_order' => Attendance::getClassSortOrder($kelas),
            'status' => $validated['status'],
            'kelengkapan' => $kelengkapan,
            'keterangan' => $keterangan,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Presensi atas nama {$attendance->nama} ({$attendance->kelas}) berhasil disimpan!",
                'data' => $attendance,
                'today_count' => Attendance::whereDate('tanggal', Carbon::today())->count(),
            ]);
        }

        return redirect()->route('absen.form')->with('success', "Presensi {$attendance->nama} ({$attendance->kelas}) berhasil dicatat!");
    }

    /**
     * Dashboard page with summaries & analytics.
     */
    public function dashboard(Request $request)
    {
        $selectedDate = $request->get('tanggal', Carbon::today()->format('Y-m-d'));

        $query = Attendance::whereDate('tanggal', $selectedDate);
        $totalMasterStudents = Student::where('is_active', true)->count();

        $stats = [
            'total' => (clone $query)->count(),
            'hadir' => (clone $query)->where('status', 'hadir')->count(),
            'sakit' => (clone $query)->where('status', 'sakit')->count(),
            'izin' => (clone $query)->where('status', 'izin')->count(),
            'alfa' => (clone $query)->where('status', 'alfa')->count(),
            'lengkap' => (clone $query)->where('status', 'hadir')->where('kelengkapan', 'lengkap')->count(),
            'tidak_lengkap' => (clone $query)->where('status', 'hadir')->where('kelengkapan', 'tidak_lengkap')->count(),
            'total_siswa' => $totalMasterStudents,
            'belum_absen' => max(0, $totalMasterStudents - (clone $query)->count()),
        ];

        // Completeness percentage
        $stats['kelengkapan_pct'] = $stats['hadir'] > 0
            ? round(($stats['lengkap'] / $stats['hadir']) * 100, 1)
            : 0;

        // Class breakdown
        $classBreakdown = Attendance::selectRaw("kelas, class_sort_order, COUNT(*) as total_siswa, SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as total_hadir, SUM(CASE WHEN status='sakit' THEN 1 ELSE 0 END) as total_sakit, SUM(CASE WHEN status='izin' THEN 1 ELSE 0 END) as total_izin, SUM(CASE WHEN status='alfa' THEN 1 ELSE 0 END) as total_alfa")
            ->whereDate('tanggal', $selectedDate)
            ->groupBy('kelas', 'class_sort_order')
            ->orderBy('class_sort_order', 'asc')
            ->get();

        $recentEntries = Attendance::whereDate('tanggal', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('attendance.dashboard', compact('stats', 'selectedDate', 'classBreakdown', 'recentEntries'));
    }

    /**
     * Dedicated Status View: Who HAS attended vs Who HAS NOT attended.
     */
    public function status(Request $request)
    {
        $selectedDate = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $selectedKelas = $request->get('kelas', '');
        $search = trim($request->get('search', ''));

        $classes = Student::AVAILABLE_CLASSES;

        // Master Students Query
        $studentQuery = Student::where('is_active', true);

        if ($selectedKelas !== '') {
            $studentQuery->where('kelas', $selectedKelas);
        }

        if ($search !== '') {
            $escaped = addcslashes($search, '%_\\');
            $studentQuery->where(function ($q) use ($escaped) {
                $q->where('nama', 'like', "%{$escaped}%")
                    ->orWhere('nis', 'like', "%{$escaped}%");
            });
        }

        $allStudents = $studentQuery->orderBy('class_sort_order', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        // Attendance records for selected date
        $attendancesQuery = Attendance::whereDate('tanggal', $selectedDate);
        if ($selectedKelas !== '') {
            $attendancesQuery->where('kelas', $selectedKelas);
        }
        $attendances = $attendancesQuery->get();

        // Map attendance by student_id or (nama + kelas)
        $attendanceMapByStudentId = $attendances->whereNotNull('student_id')->keyBy('student_id');
        $attendanceMapByNameKey = $attendances->keyBy(fn ($item) => strtolower(trim($item->nama)).'|'.strtolower(trim($item->kelas)));

        $sudahAbsen = collect();
        $belumAbsen = collect();

        foreach ($allStudents as $student) {
            $nameKey = strtolower(trim($student->nama)).'|'.strtolower(trim($student->kelas));
            $attendanceRecord = $attendanceMapByStudentId->get($student->id) ?? $attendanceMapByNameKey->get($nameKey);

            if ($attendanceRecord) {
                $student->attendance = $attendanceRecord;
                $sudahAbsen->push($student);
            } else {
                $belumAbsen->push($student);
            }
        }

        $stats = [
            'total_siswa' => $allStudents->count(),
            'sudah_absen' => $sudahAbsen->count(),
            'belum_absen' => $belumAbsen->count(),
            'persentase_absen' => $allStudents->count() > 0 ? round(($sudahAbsen->count() / $allStudents->count()) * 100, 1) : 0,
        ];

        return view('attendance.status', compact('sudahAbsen', 'belumAbsen', 'stats', 'classes', 'selectedDate', 'selectedKelas', 'search'));
    }

    /**
     * Attendance records page with filters & pagination.
     */
    public function records(Request $request)
    {
        $classes = Attendance::AVAILABLE_CLASSES;

        $query = Attendance::query();

        if ($request->filled('search')) {
            $escaped = addcslashes($request->search, '%_\\');
            $query->where('nama', 'like', '%'.$escaped.'%');
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('tanggal', 'desc')
            ->orderBy('class_sort_order', 'asc')
            ->orderBy('nama', 'asc');

        $attendances = $query->paginate(20)->withQueryString();

        return view('attendance.records', compact('attendances', 'classes'));
    }

    /**
     * Download attendance data in Excel CSV format with custom strict class & date sorting.
     */
    public function download(Request $request)
    {
        $query = Attendance::query();

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('tanggal', 'asc')
            ->orderBy('class_sort_order', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        $filename = 'rekap_absen_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'No',
                'Tanggal',
                'Nama Siswa',
                'Kelas',
                'Status OSIS/MPK',
                'Status Kehadiran',
                'Form Kelengkapan',
                'Keterangan / Catatan',
                'Waktu Masuk',
            ]);

            foreach ($attendances as $index => $row) {
                $statusFormatted = match ($row->status) {
                    'hadir' => 'Hadir',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                    'alfa' => 'Alfa',
                    default => ucfirst($row->status)
                };

                $kelengkapanFormatted = '-';
                if ($row->status === 'hadir') {
                    $kelengkapanFormatted = ($row->kelengkapan === 'tidak_lengkap') ? 'Tidak Lengkap' : 'Lengkap';
                }

                fputcsv($file, [
                    $index + 1,
                    $row->tanggal ? $row->tanggal->format('d/m/Y') : '-',
                    $row->nama,
                    $row->kelas,
                    $row->osis_mpk ?? 'Bukan',
                    $statusFormatted,
                    $kelengkapanFormatted,
                    $row->keterangan ?? '-',
                    $row->created_at ? $row->created_at->format('H:i:s') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete an attendance entry.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Get autocomplete name suggestions based on master Student data.
     */
    public function suggestions(Request $request)
    {
        $query = trim($request->query('q') ?? $request->input('q') ?? '');
        $kelas = trim($request->query('kelas') ?? $request->input('kelas') ?? '');

        $studentQuery = Student::where('is_active', true);

        if ($kelas !== '') {
            $studentQuery->where('kelas', $kelas);
        }

        if ($query !== '') {
            $escaped = addcslashes($query, '%_\\');
            $studentQuery->where(function ($q) use ($escaped) {
                $q->where('nama', 'like', $escaped.'%')
                    ->orWhere('nis', 'like', $escaped.'%');
            });
        }

        $students = $studentQuery->select('id', 'nis', 'nama', 'kelas', 'osis_mpk')
            ->limit(10)
            ->get();

        return response()->json($students);
    }
}
