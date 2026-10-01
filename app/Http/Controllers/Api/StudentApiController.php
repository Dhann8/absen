<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentApiController extends Controller
{
    /**
     * Display a listing of master students.
     */
    public function index(Request $request): JsonResponse
    {
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

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $perPage = (int) $request->get('per_page', 25);
        $students = $query->orderBy('class_sort_order', 'asc')
            ->orderBy('nama', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar master siswa berhasil diambil.',
            'data' => $students->items(),
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ],
            'available_classes' => Student::AVAILABLE_CLASSES,
        ]);
    }

    /**
     * Store a newly created master student.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nis' => 'nullable|string|max:50|unique:students,nis',
            'nama' => 'required|string|max:255',
            'kelas' => ['required', 'string', Rule::in(Student::AVAILABLE_CLASSES)],
            'osis_mpk' => 'nullable|string|in:Bukan,OSIS,MPK',
            'is_active' => 'nullable|boolean',
        ]);

        $student = Student::create([
            'nis' => $validated['nis'] ?? null,
            'nama' => trim($validated['nama']),
            'kelas' => $validated['kelas'],
            'osis_mpk' => $validated['osis_mpk'] ?? 'Bukan',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Master siswa {$student->nama} ({$student->kelas}) berhasil ditambahkan!",
            'data' => $student,
        ], 201);
    }

    /**
     * Display the specified master student with attendance history.
     */
    public function show(Student $student): JsonResponse
    {
        $student->load(['attendances' => function ($q) {
            $q->orderBy('tanggal', 'desc')->limit(30);
        }]);

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    /**
     * Update the specified master student.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'nis' => ['nullable', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($student->id)],
            'nama' => 'sometimes|required|string|max:255',
            'kelas' => ['sometimes', 'required', 'string', Rule::in(Student::AVAILABLE_CLASSES)],
            'osis_mpk' => 'nullable|string|in:Bukan,OSIS,MPK',
            'is_active' => 'nullable|boolean',
        ]);

        $student->update([
            'nis' => array_key_exists('nis', $validated) ? $validated['nis'] : $student->nis,
            'nama' => isset($validated['nama']) ? trim($validated['nama']) : $student->nama,
            'kelas' => $validated['kelas'] ?? $student->kelas,
            'osis_mpk' => $validated['osis_mpk'] ?? $student->osis_mpk,
            'is_active' => isset($validated['is_active']) ? $validated['is_active'] : $student->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Data master siswa {$student->nama} berhasil diperbarui!",
            'data' => $student,
        ]);
    }

    /**
     * Remove the specified master student.
     */
    public function destroy(Student $student): JsonResponse
    {
        $nama = $student->nama;
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => "Master siswa {$nama} berhasil dihapus.",
        ]);
    }
}
