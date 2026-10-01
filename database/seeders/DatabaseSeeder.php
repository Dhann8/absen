<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with official student dataset.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'name' => 'Admin Presensi',
                'password' => bcrypt('password'),
            ]
        );

        $this->call(StudentSeeder::class);

        $allStudents = Student::all();

        $statuses = ['hadir', 'hadir', 'hadir', 'hadir', 'sakit', 'izin', 'alfa'];
        $notes = [
            'sakit' => 'Demam dan flu tinggi',
            'izin' => 'Mengikuti rapat koordinasi OSIS/MPK tingkat wilayah',
            'alfa' => 'Tanpa keterangan',
            'tidak_lengkap' => 'Tidak memakai atribut dasi dan sabuk',
        ];

        // Seed attendance for ~60% of students today so there are both "Sudah Absen" and "Belum Absen" students
        foreach ($allStudents as $index => $student) {
            if ($index % 3 === 0) {
                // Leave this student as "Belum Absen" for demo purpose
                continue;
            }

            $status = $statuses[$index % count($statuses)];
            $kelengkapan = null;
            $keterangan = null;

            if ($status === 'hadir') {
                $kelengkapan = ($index % 5 === 0) ? 'tidak_lengkap' : 'lengkap';
                if ($kelengkapan === 'tidak_lengkap') {
                    $keterangan = $notes['tidak_lengkap'];
                }
            } else {
                $keterangan = $notes[$status];
            }

            Attendance::create([
                'student_id' => $student->id,
                'tanggal' => Carbon::today()->format('Y-m-d'),
                'nama' => $student->nama,
                'kelas' => $student->kelas,
                'osis_mpk' => $student->osis_mpk,
                'class_sort_order' => $student->class_sort_order,
                'status' => $status,
                'kelengkapan' => $kelengkapan,
                'keterangan' => $keterangan,
            ]);
        }
    }
}
