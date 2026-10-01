<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds with master student dataset (MPK & OSIS).
     */
    public function run(): void
    {
        $students = [
            // --- DAFTAR ANGGOTA LOLOS SELEKSI MPK (35 SISWA) ---
            ['nis' => '1001', 'nama' => 'Dipa Antoni Wijaya', 'kelas' => 'X RPL 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1002', 'nama' => 'Muhammad Rezki Faturohman', 'kelas' => 'X RPL 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1003', 'nama' => 'Riyanti Puspitawati', 'kelas' => 'X RPL 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1004', 'nama' => 'Silpani Oktapia', 'kelas' => 'X RPL 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1005', 'nama' => 'Latifah Nur Maulida', 'kelas' => 'X RPL 3', 'osis_mpk' => 'MPK'],
            ['nis' => '1006', 'nama' => 'Muhammad Guntur Saepuloh', 'kelas' => 'X RPL 3', 'osis_mpk' => 'MPK'],
            ['nis' => '1007', 'nama' => 'Nur Alya Lailatussalma', 'kelas' => 'X RPL 3', 'osis_mpk' => 'MPK'],
            ['nis' => '1008', 'nama' => 'Eva Purwanti', 'kelas' => 'X RPL 4', 'osis_mpk' => 'MPK'],
            ['nis' => '1009', 'nama' => 'Nurlaily Permatasari', 'kelas' => 'X RPL 4', 'osis_mpk' => 'MPK'],
            ['nis' => '1010', 'nama' => 'Eki Fawwaaz Saputra', 'kelas' => 'X RPL 5', 'osis_mpk' => 'MPK'],
            ['nis' => '1011', 'nama' => 'Alinda Syifa Sarah', 'kelas' => 'X RPL 6', 'osis_mpk' => 'MPK'],
            ['nis' => '1012', 'nama' => 'Zalwa Dwi Alifah', 'kelas' => 'X RPL 6', 'osis_mpk' => 'MPK'],
            ['nis' => '1013', 'nama' => 'Adinda Ayudya Pratiwi', 'kelas' => 'X RPL 8', 'osis_mpk' => 'MPK'],
            ['nis' => '1014', 'nama' => 'Risma Lestari', 'kelas' => 'X RPL 9', 'osis_mpk' => 'MPK'],
            ['nis' => '1015', 'nama' => 'Finza Pasha Anugrah', 'kelas' => 'X RPL 10', 'osis_mpk' => 'MPK'],
            ['nis' => '1016', 'nama' => 'Ridwan Kusnawan', 'kelas' => 'X RPL 11', 'osis_mpk' => 'MPK'],
            ['nis' => '1017', 'nama' => 'Adelia Listiasari', 'kelas' => 'X DKV 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1018', 'nama' => 'Natasya Syalwa Zharifah', 'kelas' => 'X DKV 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1019', 'nama' => 'Revan Azhar', 'kelas' => 'X DKV 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1020', 'nama' => 'Siti Maesaroh', 'kelas' => 'X DKV 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1021', 'nama' => 'Zahra Kumaira', 'kelas' => 'X DKV 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1022', 'nama' => 'Fairus Salsabila', 'kelas' => 'X DKV 2', 'osis_mpk' => 'MPK'],
            ['nis' => '1023', 'nama' => 'Nayla Anastasya', 'kelas' => 'X DKV 2', 'osis_mpk' => 'MPK'],
            ['nis' => '1024', 'nama' => 'Nisa Nur Janah', 'kelas' => 'X DKV 3', 'osis_mpk' => 'MPK'],
            ['nis' => '1025', 'nama' => 'Saepudin Hidayat', 'kelas' => 'X DKV 3', 'osis_mpk' => 'MPK'],
            ['nis' => '1026', 'nama' => 'Saskia Putri Nurahmat Dita', 'kelas' => 'X DKV 3', 'osis_mpk' => 'MPK'],
            ['nis' => '1027', 'nama' => 'M Jazuli', 'kelas' => 'XI RPL 1', 'osis_mpk' => 'MPK'],
            ['nis' => '1028', 'nama' => 'Naisya Kardianti Noor', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'MPK'],
            ['nis' => '1029', 'nama' => 'Talitha Yulia', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'MPK'],
            ['nis' => '1030', 'nama' => 'Dika Fauziah', 'kelas' => 'XI RPL 4', 'osis_mpk' => 'MPK'],
            ['nis' => '1031', 'nama' => 'Erliani Juniawanti', 'kelas' => 'XI RPL 5', 'osis_mpk' => 'MPK'],
            ['nis' => '1032', 'nama' => 'Ilham Muzaki', 'kelas' => 'XI RPL 7', 'osis_mpk' => 'MPK'],
            ['nis' => '1033', 'nama' => 'Carissa Septania Fitri', 'kelas' => 'XI DKV 2', 'osis_mpk' => 'MPK'],
            ['nis' => '1034', 'nama' => 'Nidha Zhahra N.A', 'kelas' => 'XI DKV 2', 'osis_mpk' => 'MPK'],
            ['nis' => '1035', 'nama' => 'Wina Puspa S.B', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'MPK'],

            // --- DAFTAR ANGGOTA LOLOS SELEKSI OSIS (45 SISWA) ---
            ['nis' => '1036', 'nama' => 'Muhammad Rivan Ar Rafi', 'kelas' => 'X DKV 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1037', 'nama' => 'Raffa Shakeel Alfarizqi', 'kelas' => 'X DKV 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1038', 'nama' => 'Muhamad Ziad Akbar', 'kelas' => 'X DKV 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1039', 'nama' => 'Noval Sunardi', 'kelas' => 'X DKV 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1040', 'nama' => 'Muhamad Alfarizie Mubarok', 'kelas' => 'X DKV 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1041', 'nama' => 'Assyifa Ayuningtiyas', 'kelas' => 'X DKV 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1042', 'nama' => 'Shafira Khaerunisa', 'kelas' => 'X DKV 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1043', 'nama' => 'Azhar Septa Bahaudin', 'kelas' => 'X DKV 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1044', 'nama' => 'Mutiara Nur Ramadhani', 'kelas' => 'X DKV 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1045', 'nama' => 'Azzylla Aira Putri', 'kelas' => 'X RPL 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1046', 'nama' => 'Keysha Fadillah Dwiyanti', 'kelas' => 'X RPL 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1047', 'nama' => 'Cici Karnia', 'kelas' => 'X RPL 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1048', 'nama' => 'Sri Rahayu Rahmawati', 'kelas' => 'X RPL 1', 'osis_mpk' => 'OSIS'],
            ['nis' => '1049', 'nama' => 'Veronique May Erica Soehaja', 'kelas' => 'X RPL 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1050', 'nama' => 'Mugia Rizki Kurniawan', 'kelas' => 'X RPL 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1051', 'nama' => 'Suci Agustina Pratiwi', 'kelas' => 'X RPL 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1052', 'nama' => 'Dzikri Ilyas Prasetia', 'kelas' => 'X RPL 3', 'osis_mpk' => 'OSIS'],
            ['nis' => '1053', 'nama' => 'Raisa Hayatunnisa', 'kelas' => 'X RPL 3', 'osis_mpk' => 'OSIS'],
            ['nis' => '1054', 'nama' => 'Mohamad Aditia Fratama', 'kelas' => 'X RPL 3', 'osis_mpk' => 'OSIS'],
            ['nis' => '1055', 'nama' => 'Muhammad Tareqi Adityansyah', 'kelas' => 'X RPL 3', 'osis_mpk' => 'OSIS'],
            ['nis' => '1056', 'nama' => 'Papuh Billah', 'kelas' => 'X RPL 3', 'osis_mpk' => 'OSIS'],
            ['nis' => '1057', 'nama' => 'Galang Herdyansyah Putra', 'kelas' => 'X RPL 4', 'osis_mpk' => 'OSIS'],
            ['nis' => '1058', 'nama' => 'Axell Bintang Nur Ali', 'kelas' => 'X RPL 4', 'osis_mpk' => 'OSIS'],
            ['nis' => '1059', 'nama' => 'Delia Rhesylia Putri', 'kelas' => 'X RPL 6', 'osis_mpk' => 'OSIS'],
            ['nis' => '1060', 'nama' => 'Arga Sifha Kurnia', 'kelas' => 'X RPL 7', 'osis_mpk' => 'OSIS'],
            ['nis' => '1061', 'nama' => 'Pipit Restika', 'kelas' => 'X RPL 9', 'osis_mpk' => 'OSIS'],
            ['nis' => '1062', 'nama' => 'Ririt Mauludin', 'kelas' => 'X RPL 9', 'osis_mpk' => 'OSIS'],
            ['nis' => '1063', 'nama' => 'Giya Dwi Gumylar', 'kelas' => 'X RPL 9', 'osis_mpk' => 'OSIS'],
            ['nis' => '1064', 'nama' => 'Andini Khaerunisa', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nis' => '1065', 'nama' => 'Arsyi Dwi Anugrah Hidayat', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nis' => '1066', 'nama' => 'Zaenal Mutaqin', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nis' => '1067', 'nama' => 'Dhalfy', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nis' => '1068', 'nama' => 'Muhamad Qunrat', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nis' => '1069', 'nama' => 'Satria Budiman Prindani', 'kelas' => 'X RPL 10', 'osis_mpk' => 'OSIS'],
            ['nis' => '1070', 'nama' => 'Zuliansyah Akbar', 'kelas' => 'X RPL 11', 'osis_mpk' => 'OSIS'],
            ['nis' => '1071', 'nama' => 'Fiqri Ramadhan Putra', 'kelas' => 'X RPL 11', 'osis_mpk' => 'OSIS'],
            ['nis' => '1072', 'nama' => 'Tieara Septy Puspitasari', 'kelas' => 'X RPL 11', 'osis_mpk' => 'OSIS'],
            ['nis' => '1073', 'nama' => 'Jasmien', 'kelas' => 'XI DKV 3', 'osis_mpk' => 'OSIS'],
            ['nis' => '1074', 'nama' => 'Siti Nidaul', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1075', 'nama' => 'Nayla Aulia Putri', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1076', 'nama' => 'Listianti Siti', 'kelas' => 'XI RPL 2', 'osis_mpk' => 'OSIS'],
            ['nis' => '1077', 'nama' => 'Haikal Janatun', 'kelas' => 'XI RPL 3', 'osis_mpk' => 'OSIS'],
            ['nis' => '1078', 'nama' => 'Almira Alfatunnisa', 'kelas' => 'XI RPL 4', 'osis_mpk' => 'OSIS'],
            ['nis' => '1079', 'nama' => 'Cikal Rizki', 'kelas' => 'XI RPL 4', 'osis_mpk' => 'OSIS'],
            ['nis' => '1080', 'nama' => 'Ihsan Fadillah Alfarizki', 'kelas' => 'XI RPL 6', 'osis_mpk' => 'OSIS'],
        ];

        Student::query()->delete();

        foreach ($students as $s) {
            Student::updateOrCreate(
                [
                    'nis' => $s['nis'],
                ],
                [
                    'nama' => $s['nama'],
                    'kelas' => $s['kelas'],
                    'osis_mpk' => $s['osis_mpk'],
                    'class_sort_order' => Student::getClassSortOrder($s['kelas']),
                    'is_active' => true,
                ]
            );
        }
    }
}
