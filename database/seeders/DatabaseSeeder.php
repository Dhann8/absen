<?php

namespace Database\Seeders;

use App\Models\Attendance;
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

        $students = [
            ['nama' => 'Muhammad Rivan Ar Rafi', 'kelas' => 'X DKV 1', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Raffa Shakeel Alfarizqi', 'kelas' => 'X DKV 1', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Muhamad Ziad Akbar', 'kelas' => 'X DKV 1', 'osis_mpk' => 'MPK'],
            ['nama' => 'Noval Sunardi', 'kelas' => 'X DKV 1', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Muhamad Alfarizie Mubarok', 'kelas' => 'X DKV 1', 'osis_mpk' => 'MPK'],
            ['nama' => 'Assyifa Ayuningtiyas', 'kelas' => 'X DKV 2', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Shafira Khaerunisa', 'kelas' => 'X DKV 2', 'osis_mpk' => 'MPK'],
            ['nama' => 'Azhar Septa Bahaudin', 'kelas' => 'X DKV 2', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Mutiara Nur Ramadhani', 'kelas' => 'X DKV 2', 'osis_mpk' => 'MPK'],
            ['nama' => 'Azzylla Aira Putri', 'kelas' => 'X RPL 1', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Keysha Fadillah Dwiyanti', 'kelas' => 'X RPL 1', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Cici Karnia', 'kelas' => 'X RPL 1', 'osis_mpk' => 'MPK'],
            ['nama' => 'Sri Rahayu Rahmawati', 'kelas' => 'X RPL 1', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Veronique May Erica Soehaja', 'kelas' => 'X RPL 2', 'osis_mpk' => 'MPK'],
            ['nama' => 'Mugia Rizki Kurniawan', 'kelas' => 'X RPL 2', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Suci Agustina Pratiwi', 'kelas' => 'X RPL 2', 'osis_mpk' => 'MPK'],
            ['nama' => 'Dzikri Ilyas Prasetia', 'kelas' => 'X RPL 3', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Raisa Hayatunnisa', 'kelas' => 'X RPL 3', 'osis_mpk' => 'MPK'],
            ['nama' => 'Mohamad Aditia Fratama', 'kelas' => 'X RPL 3', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Muhammad Tareqi Adityansyah', 'kelas' => 'X RPL 3', 'osis_mpk' => 'MPK'],
            ['nama' => 'Papuh Billah', 'kelas' => 'X RPL 3', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Galang Herdyansyah Putra', 'kelas' => 'X RPL 4', 'osis_mpk' => 'MPK'],
            ['nama' => 'Axell Bintang Nur Ali', 'kelas' => 'X RPL 4', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Delia Rhesylia Putri', 'kelas' => 'X RPL 6', 'osis_mpk' => 'MPK'],
            ['nama' => 'Arga Sifha Kurnia', 'kelas' => 'X RPL 7', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Pipit Restika', 'kelas' => 'X RPL 9', 'osis_mpk' => 'MPK'],
            ['nama' => 'Ririt Mauludin', 'kelas' => 'X RPL 9', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Giya Dwi Gumylar', 'kelas' => 'X RPL 9', 'osis_mpk' => 'MPK'],
            ['nama' => 'Andini Khaerunisa', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Arsyi Dwi Anugrah Hidayat', 'kelas' => 'X RPL 10', 'osis_mpk' => 'MPK'],
            ['nama' => 'Zaenal Mutaqin', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Dhalfy', 'kelas' => 'X RPL 10', 'osis_mpk' => 'MPK'],
            ['nama' => 'Muhamad Qunrat', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Satria Budiman Prindani', 'kelas' => 'X RPL 10', 'osis_mpk' => 'MPK'],
            ['nama' => 'Zuliansyah Akbar', 'kelas' => 'X RPL 11', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Fiqri Ramadhan Putra', 'kelas' => 'X RPL 11', 'osis_mpk' => 'MPK'],
            ['nama' => 'Tieara Septy Puspitasari', 'kelas' => 'X RPL 11', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Jasmien', 'kelas' => 'XI DKV 3', 'osis_mpk' => 'MPK'],
            ['nama' => 'Siti Nidaul', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Nayla Aulia Putri', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'MPK'],
            ['nama' => 'Listianti Siti', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Haikal Janatun', 'kelas' => 'XI RPL 3', 'osis_mpk' => 'MPK'],
            ['nama' => 'Almira Alfatunnisa', 'kelas' => 'XI RPL 4', 'osis_mpk' => 'OSIS'],
            ['nama' => 'Cikal Rizki', 'kelas' => 'XI RPL 4', 'osis_mpk' => 'MPK'],
            ['nama' => 'Ihsan Fadillah Alfarizki', 'kelas' => 'XI RPL 6', 'osis_mpk' => 'OSIS'],
        ];

        $statuses = ['hadir', 'hadir', 'hadir', 'hadir', 'sakit', 'izin', 'alfa'];
        $notes = [
            'sakit' => 'Demam dan flu tinggi',
            'izin' => 'Mengikuti rapat koordinasi OSIS/MPK tingkat wilayah',
            'alfa' => 'Tanpa keterangan',
            'tidak_lengkap' => 'Tidak memakai atribut dasi dan sabuk',
        ];

        foreach ($students as $index => $s) {
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
                'tanggal' => Carbon::today()->format('Y-m-d'),
                'nama' => $s['nama'],
                'kelas' => $s['kelas'],
                'osis_mpk' => $s['osis_mpk'],
                'class_sort_order' => Attendance::getClassSortOrder($s['kelas']),
                'status' => $status,
                'kelengkapan' => $kelengkapan,
                'keterangan' => $keterangan,
            ]);
        }
    }
}
