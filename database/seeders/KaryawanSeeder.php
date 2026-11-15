<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Simpanan;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        Karyawan::truncate();
        Simpanan::truncate();

        $data = [
            ['nik'=>'KAR100','nama'=>'Budi Santoso','jabatan'=>'Staff','no_hp'=>'0812340001','no_wa'=>'0812340001','alamat'=>'Jl. Merdeka 1','jenis_kelamin'=>'L','tanggal_masuk'=>'2020-01-15','is_active'=>1],
            ['nik'=>'KAR101','nama'=>'Siti Aminah','jabatan'=>'Manager','no_hp'=>'0812340002','no_wa'=>'0812340002','alamat'=>'Jl. Melati 2','jenis_kelamin'=>'P','tanggal_masuk'=>'2018-06-10','is_active'=>1],
            ['nik'=>'KAR102','nama'=>'Anton Wijaya','jabatan'=>'Staff','no_hp'=>'0812340003','no_wa'=>'0812340003','alamat'=>'Jl. Kenanga 3','jenis_kelamin'=>'L','tanggal_masuk'=>'2021-09-01','is_active'=>1],
        ];

        foreach ($data as $d) {
            $k = Karyawan::create($d);
            Simpanan::create([
                'karyawan_id' => $k->id,
                'simpanan_pokok' => 0,
                'simpanan_wajib' => 0,
                'simpanan_sukarela' => 0,
                'total_simpanan' => 0,
            ]);
        }
    }
}
