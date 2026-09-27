<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['hadir', 'hadir', 'hadir', 'sakit', 'izin', 'alfa']);
        $kelengkapan = null;
        $keterangan = null;

        if ($status === 'hadir') {
            $kelengkapan = $this->faker->randomElement(['lengkap', 'lengkap', 'lengkap', 'tidak_lengkap']);
            if ($kelengkapan === 'tidak_lengkap') {
                $keterangan = $this->faker->randomElement([
                    'Tidak membawa topi dan sabuk',
                    'Buku catatan tertinggal',
                    'Seragam tidak sesuai jadwal',
                    'Pin OSIS & dasi tidak lengkap',
                    'Sepatu tidak berwarna hitam polos',
                ]);
            }
        } else {
            $reasons = [
                'sakit' => ['Demam tinggi', 'Flu dan batuk', 'Sakit perut', 'Rawat inap RS', 'Migrain'],
                'izin' => ['Acara keluarga di luar kota', 'Mengurus dokumen SIM', 'Lomba robotik tingkat provinsi', 'Ada urusan keluarga mendadak'],
                'alfa' => ['Tanpa keterangan', 'Tidak ada kabar dari orang tua', 'Bolos sekolah'],
            ];
            $keterangan = $this->faker->randomElement($reasons[$status]);
        }

        $kelas = $this->faker->randomElement(Attendance::AVAILABLE_CLASSES);

        return [
            'tanggal' => $this->faker->dateTimeBetween('-5 days', 'now')->format('Y-m-d'),
            'nama' => $this->faker->name(),
            'kelas' => $kelas,
            'class_sort_order' => Attendance::getClassSortOrder($kelas),
            'status' => $status,
            'kelengkapan' => $kelengkapan,
            'keterangan' => $keterangan,
        ];
    }
}
