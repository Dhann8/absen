<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AttendanceApiController extends Controller
{
    /**
     * Display a listing of attendance records with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Attendance::with('student');

        if ($request->filled('search')) {
            $escaped = addcslashes($request->search, '%_\\');
            $query->where('nama', 'like', "%{$escaped}%");
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

        $perPage = (int) $request->get('per_page', 20);
        $attendances = $query->orderBy('tanggal', 'desc')
            ->orderBy('class_sort_order', 'asc')
            ->orderBy('nama', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data absensi berhasil diambil.',
            'data' => $attendances->items(),
            'meta' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
            ],
        ]);
    }

    /**
     * Store new attendance entry via API (Links to student_id automatically).
     */
    public function store(Request $request): JsonResponse
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

        // Find or link master student
        $studentId = $validated['student_id'] ?? null;
        if (! $studentId) {
            $matchedStudent = Student::where('nama', $nama)->where('kelas', $kelas)->first();
            if ($matchedStudent) {
                $studentId = $matchedStudent->id;
            } else {
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

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errors' => [
                    'nama' => [$errorMessage],
                ],
            ], 422);
        }

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

        return response()->json([
            'success' => true,
            'message' => "Presensi atas nama {$attendance->nama} ({$attendance->kelas}) berhasil disimpan!",
            'data' => $attendance,
            'today_count' => Attendance::whereDate('tanggal', Carbon::today())->count(),
        ], 201);
    }

    /**
     * Get attendance status breakdown (Sudah Absen vs Belum Absen) for date/class.
     */
    public function status(Request $request): JsonResponse
    {
        $selectedDate = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $selectedKelas = $request->get('kelas', '');
        $search = trim($request->get('search', ''));

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

        $attendancesQuery = Attendance::whereDate('tanggal', $selectedDate);
        if ($selectedKelas !== '') {
            $attendancesQuery->where('kelas', $selectedKelas);
        }
        $attendances = $attendancesQuery->get();

        $attendanceMapByStudentId = $attendances->whereNotNull('student_id')->keyBy('student_id');
        $attendanceMapByNameKey = $attendances->keyBy(fn ($item) => strtolower(trim($item->nama)).'|'.strtolower(trim($item->kelas)));

        $sudahAbsen = [];
        $belumAbsen = [];

        foreach ($allStudents as $student) {
            $nameKey = strtolower(trim($student->nama)).'|'.strtolower(trim($student->kelas));
            $attendanceRecord = $attendanceMapByStudentId->get($student->id) ?? $attendanceMapByNameKey->get($nameKey);

            if ($attendanceRecord) {
                $studentData = $student->toArray();
                $studentData['attendance'] = $attendanceRecord;
                $sudahAbsen[] = $studentData;
            } else {
                $belumAbsen[] = $student;
            }
        }

        return response()->json([
            'success' => true,
            'tanggal' => $selectedDate,
            'kelas' => $selectedKelas,
            'summary' => [
                'total_siswa' => count($allStudents),
                'sudah_absen' => count($sudahAbsen),
                'belum_absen' => count($belumAbsen),
                'persentase_kehadiran' => count($allStudents) > 0 ? round((count($sudahAbsen) / count($allStudents)) * 100, 1) : 0,
            ],
            'sudah_absen' => $sudahAbsen,
            'belum_absen' => $belumAbsen,
        ]);
    }

    /**
     * Get summary analytics & stats.
     */
    public function summary(Request $request): JsonResponse
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
            'total_siswa_master' => $totalMasterStudents,
            'belum_absen' => max(0, $totalMasterStudents - (clone $query)->count()),
        ];

        $stats['kelengkapan_pct'] = $stats['hadir'] > 0 ? round(($stats['lengkap'] / $stats['hadir']) * 100, 1) : 0;

        $classBreakdown = Attendance::selectRaw("kelas, class_sort_order, COUNT(*) as total_siswa, SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as total_hadir, SUM(CASE WHEN status='sakit' THEN 1 ELSE 0 END) as total_sakit, SUM(CASE WHEN status='izin' THEN 1 ELSE 0 END) as total_izin, SUM(CASE WHEN status='alfa' THEN 1 ELSE 0 END) as total_alfa")
            ->whereDate('tanggal', $selectedDate)
            ->groupBy('kelas', 'class_sort_order')
            ->orderBy('class_sort_order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'tanggal' => $selectedDate,
            'stats' => $stats,
            'class_breakdown' => $classBreakdown,
        ]);
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy(Attendance $attendance): JsonResponse
    {
        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data absensi berhasil dihapus.',
        ]);
    }
}
