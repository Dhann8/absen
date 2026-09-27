<?php

namespace Tests\Feature;

use App\Models\Attendance;
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
        Attendance::factory()->create(['nama' => 'Ahmad Rizky', 'kelas' => 'X RPL 1']);

        $response = $this->get('/absen/suggestions?q=Ah');
        $response->assertStatus(200);
        $response->assertJsonFragment(['nama' => 'Ahmad Rizky']);
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
}
