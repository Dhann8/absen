<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Database\Seeders\StudentSeeder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of master students.
     */
    public function index(Request $request)
    {
        $classes = Student::AVAILABLE_CLASSES;

        $query = Student::query();

        if ($request->filled('search')) {
            $search = addcslashes($request->search, '%_\\');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('osis_mpk')) {
            $query->where('osis_mpk', $request->osis_mpk);
        }

        $students = $query->orderBy('class_sort_order', 'asc')
            ->orderBy('nama', 'asc')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => Student::count(),
            'active' => Student::where('is_active', true)->count(),
            'osis' => Student::where('osis_mpk', 'OSIS')->count(),
            'mpk' => Student::where('osis_mpk', 'MPK')->count(),
        ];

        return view('students.index', compact('students', 'classes', 'stats'));
    }

    /**
     * Store a newly created student in master data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'nullable|string|max:50|unique:students,nis',
            'nama' => 'required|string|max:255',
            'kelas' => ['required', 'string', Rule::in(Student::AVAILABLE_CLASSES)],
            'osis_mpk' => 'nullable|string|in:Bukan,OSIS,MPK',
        ]);

        $student = Student::create([
            'nis' => $validated['nis'] ?? null,
            'nama' => trim($validated['nama']),
            'kelas' => $validated['kelas'],
            'osis_mpk' => $validated['osis_mpk'] ?? 'Bukan',
            'is_active' => true,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Siswa {$student->nama} ({$student->kelas}) berhasil ditambahkan!",
                'data' => $student,
            ]);
        }

        return redirect()->route('students.index')->with('success', "Siswa {$student->nama} ({$student->kelas}) berhasil ditambahkan ke Master Data!");
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => ['nullable', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($student->id)],
            'nama' => 'required|string|max:255',
            'kelas' => ['required', 'string', Rule::in(Student::AVAILABLE_CLASSES)],
            'osis_mpk' => 'nullable|string|in:Bukan,OSIS,MPK',
            'is_active' => 'boolean',
        ]);

        $student->update([
            'nis' => $validated['nis'] ?? null,
            'nama' => trim($validated['nama']),
            'kelas' => $validated['kelas'],
            'osis_mpk' => $validated['osis_mpk'] ?? 'Bukan',
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $student->is_active,
        ]);

        return redirect()->route('students.index')->with('success', "Data siswa {$student->nama} berhasil diperbarui!");
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        $nama = $student->nama;
        $student->delete();

        return redirect()->route('students.index')->with('success', "Siswa {$nama} berhasil dihapus dari Master Data.");
    }

    /**
     * Seed or reset default student list.
     */
    public function seedDefault()
    {
        $seeder = new StudentSeeder;
        $seeder->run();

        return redirect()->route('students.index')->with('success', 'Daftar master siswa resmi berhasil di-load!');
    }
}
