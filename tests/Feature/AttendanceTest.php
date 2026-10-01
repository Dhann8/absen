<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_attendance_form(): void
    {
        $response = $this->get(route('absen.form'));
        $response->assertStatus(200);
        $response->assertSee('Input Presensi Siswa');
    }

    public function test_can_store_attendance_entry_via_ajax(): void
    {
        $payload = [
            'nama' => 'Budi Santoso',
            'kelas' => 'X RPL 1',
            'status' => 'hadir',
            'kelengkapan' => 'lengkap',
            'tanggal' => date('Y-m-d'),
        ];

        $response = $this->postJson(route('absen.store'), $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('attendances', [
            'nama' => 'Budi Santoso',
            'kelas' => 'X RPL 1',
            'status' => 'hadir',
            'kelengkapan' => 'lengkap',
        ]);
    }

    public function test_can_store_sakit_attendance_with_keterangan(): void
    {
        $payload = [
            'nama' => 'Siti Aminah',
            'kelas' => 'XI DKV 1',
            'status' => 'sakit',
            'keterangan' => 'Demam dan flu berat',
            'tanggal' => date('Y-m-d'),
        ];

        $response = $this->postJson(route('absen.store'), $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendances', [
            'nama' => 'Siti Aminah',
            'kelas' => 'XI DKV 1',
            'status' => 'sakit',
            'keterangan' => 'Demam dan flu berat',
        ]);
    }

    public function test_can_view_dashboard(): void
    {
        $response = $this->get(route('absen.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Presensi');
    }

    public function test_can_view_data_absen_records(): void
    {
        $response = $this->get(route('absen.records'));
        $response->assertStatus(200);
        $response->assertSee('Data Absensi Siswa');
    }

    public function test_can_download_csv_export(): void
    {
        $response = $this->get(route('absen.download'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_can_fetch_name_suggestions(): void
    {
        Student::factory()->create(['nama' => 'Ahmad Rizky', 'kelas' => 'X RPL 1']);

        $response = $this->get('/absen/suggestions?q=Ah');
        $response->assertStatus(200);
        $response->assertJsonFragment(['nama' => 'Ahmad Rizky']);
    }

    public function test_can_view_status_page_showing_sudah_and_belum_absen(): void
    {
        $student1 = Student::factory()->create(['nama' => 'Student One', 'kelas' => 'X RPL 1']);
        $student2 = Student::factory()->create(['nama' => 'Student Two', 'kelas' => 'X RPL 1']);

        Attendance::create([
            'student_id' => $student1->id,
            'tanggal' => date('Y-m-d'),
            'nama' => $student1->nama,
            'kelas' => $student1->kelas,
            'status' => 'hadir',
            'kelengkapan' => 'lengkap',
        ]);

        $response = $this->get(route('absen.status'));
        $response->assertStatus(200);
        $response->assertSee('Student One');
        $response->assertSee('Student Two');
        $response->assertSee('Sudah Absen');
        $response->assertSee('Belum Absen');
    }

    public function test_can_manage_master_students_crud(): void
    {
        $response = $this->get(route('students.index'));
        $response->assertStatus(200);

        // Store
        $storeResponse = $this->post(route('students.store'), [
            'nis' => '9999',
            'nama' => 'New Student Master',
            'kelas' => 'X RPL 2',
            'osis_mpk' => 'OSIS',
        ]);
        $storeResponse->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', ['nama' => 'New Student Master', 'nis' => '9999']);

        $student = Student::where('nama', 'New Student Master')->first();

        // Update
        $updateResponse = $this->put(route('students.update', $student), [
            'nis' => '9999',
            'nama' => 'Updated Student Master',
            'kelas' => 'X RPL 2',
            'osis_mpk' => 'MPK',
        ]);
        $updateResponse->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', ['nama' => 'Updated Student Master', 'osis_mpk' => 'MPK']);

        // Destroy
        $destroyResponse = $this->delete(route('students.destroy', $student));
        $destroyResponse->assertRedirect(route('students.index'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_fails_validation_for_invalid_class(): void
    {
        $payload = [
            'nama' => 'User Hack',
            'kelas' => 'INVALID_CLASS_NAME',
            'status' => 'hadir',
            'kelengkapan' => 'lengkap',
        ];

        $response = $this->postJson(route('absen.store'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['kelas']);
    }

    public function test_fails_validation_when_keterangan_missing_for_sakit(): void
    {
        $payload = [
            'nama' => 'Siti Aminah',
            'kelas' => 'XI DKV 1',
            'status' => 'sakit',
        ];

        $response = $this->postJson(route('absen.store'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['keterangan']);
    }

    public function test_prevents_duplicate_attendance_on_same_date(): void
    {
        $payload = [
            'nama' => 'Budi Duplicate',
            'kelas' => 'X RPL 1',
            'status' => 'hadir',
            'kelengkapan' => 'lengkap',
            'tanggal' => date('Y-m-d'),
        ];

        // First attempt succeeds
        $firstResponse = $this->postJson(route('absen.store'), $payload);
        $firstResponse->assertStatus(200);

        // Second attempt on the same date fails validation
        $secondResponse = $this->postJson(route('absen.store'), $payload);
        $secondResponse->assertStatus(422);
        $secondResponse->assertJsonValidationErrors(['nama']);
    }
}
