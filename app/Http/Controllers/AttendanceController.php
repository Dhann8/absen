<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Mobile form for continuous attendance entry.
     */
    public function form()
    {
        $classes = Attendance::AVAILABLE_CLASSES;
        $todayCount = Attendance::whereDate('tanggal', Carbon::today())->count();
        $recentEntries = Attendance::orderBy('id', 'desc')->take(5)->get();

        return view('attendance.form', compact('classes', 'todayCount', 'recentEntries'));
    }

    /**
     * Store new attendance entry (Supports AJAX continuous submission).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kelas' => ['required', 'string', Rule::in(Attendance::AVAILABLE_CLASSES)],
            'osis_mpk' => 'nullable|string|in:OSIS,MPK,Bukan',
            'status' => 'required|in:hadir,sakit,izin,alfa',
            'kelengkapan' => 'nullable|required_if:status,hadir|in:lengkap,tidak_lengkap',
            'keterangan' => 'nullable|required_if:status,sakit,izin,alfa|required_if:kelengkapan,tidak_lengkap|string|max:1000',
            'tanggal' => 'nullable|date|date_format:Y-m-d',
        ]);

        $tanggal = $validated['tanggal'] ?? Carbon::today()->format('Y-m-d');

        // Clean conditional fields
        $kelengkapan = ($validated['status'] === 'hadir') ? ($validated['kelengkapan'] ?? 'lengkap') : null;
        $keterangan = $validated['keterangan'] ?? null;

        $attendance = Attendance::create([
            'tanggal' => $tanggal,
            'nama' => trim($validated['nama']),
            'kelas' => $validated['kelas'],
            'osis_mpk' => $validated['osis_mpk'] ?? 'Bukan',
            'class_sort_order' => Attendance::getClassSortOrder($validated['kelas']),
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

        $stats = [
            'total' => (clone $query)->count(),
            'hadir' => (clone $query)->where('status', 'hadir')->count(),
            'sakit' => (clone $query)->where('status', 'sakit')->count(),
            'izin' => (clone $query)->where('status', 'izin')->count(),
            'alfa' => (clone $query)->where('status', 'alfa')->count(),
            'lengkap' => (clone $query)->where('status', 'hadir')->where('kelengkapan', 'lengkap')->count(),
            'tidak_lengkap' => (clone $query)->where('status', 'hadir')->where('kelengkapan', 'tidak_lengkap')->count(),
        ];

        // Completeness percentage
        $stats['kelengkapan_pct'] = $stats['hadir'] > 0
            ? round(($stats['lengkap'] / $stats['hadir']) * 100, 1)
            : 0;

        // Class breakdown (using standard single quotes for SQL portability)
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

        // Ordered by Date (descending or ascending) and strictly ordered by class order (X RPL 1 - X DKV 3, XI RPL 1 - XI DKV 1)
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

        // Strictly sorted by Date and Class Hierarchy (X RPL 1 -> X DKV 3, XI RPL 1 -> XI DKV 1)
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

            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
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
     * Get autocomplete name suggestions based on user input query.
     */
    public function suggestions(Request $request)
    {
        $query = trim($request->query('q') ?? $request->input('q') ?? '');

        if ($query === '') {
            return response()->json([]);
        }

        $escaped = addcslashes($query, '%_\\');

        $names = Attendance::where('nama', 'like', $escaped.'%')
            ->select('nama', 'kelas', 'osis_mpk')
            ->distinct()
            ->limit(8)
            ->get();

        return response()->json($names);
    }
}
