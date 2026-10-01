<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_can_list_master_students(): void
    {
        Student::factory()->count(3)->create();

        $response = $this->getJson('/api/students');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_api_can_create_master_student(): void
    {
        $payload = [
            'nis' => '12345',
            'nama' => 'API Test Student',
            'kelas' => 'X RPL 1',
            'osis_mpk' => 'OSIS',
        ];

        $response = $this->postJson('/api/students', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'API Test Student');

        $this->assertDatabaseHas('students', ['nama' => 'API Test Student', 'nis' => '12345']);
    }

    public function test_api_can_list_attendances(): void
    {
        $student = Student::factory()->create();
        Attendance::create([
            'student_id' => $student->id,
            'tanggal' => date('Y-m-d'),
            'nama' => $student->nama,
            'kelas' => $student->kelas,
            'status' => 'hadir',
            'kelengkapan' => 'lengkap',
        ]);

        $response = $this->getJson('/api/attendances');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    public function test_api_can_store_attendance(): void
    {
        $student = Student::factory()->create(['nama' => 'Siswa API', 'kelas' => 'X DKV 1']);

        $payload = [
            'student_id' => $student->id,
            'nama' => 'Siswa API',
            'kelas' => 'X DKV 1',
            'status' => 'hadir',
            'kelengkapan' => 'lengkap',
            'tanggal' => date('Y-m-d'),
        ];

        $response = $this->postJson('/api/attendances', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('attendances', ['nama' => 'Siswa API', 'student_id' => $student->id]);
    }

    public function test_api_can_get_attendance_status_sudah_and_belum_absen(): void
    {
        $student1 = Student::factory()->create(['nama' => 'Hadir API', 'kelas' => 'X RPL 1']);
        $student2 = Student::factory()->create(['nama' => 'Belum API', 'kelas' => 'X RPL 1']);

        Attendance::create([
            'student_id' => $student1->id,
            'tanggal' => date('Y-m-d'),
            'nama' => $student1->nama,
            'kelas' => $student1->kelas,
            'status' => 'hadir',
            'kelengkapan' => 'lengkap',
        ]);

        $response = $this->getJson('/api/attendances/status?kelas=X+RPL+1');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('summary.sudah_absen', 1)
            ->assertJsonPath('summary.belum_absen', 1);
    }
}
