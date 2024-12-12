<?php

return [
    'pengisian' => [
        'columns' => [
            'input_gaji' => 'Input Gaji',
        ],
        'modal' => [
            'label' => 'Input Gaji',
            'heading' => 'Input Gaji :label',
            'submit' => 'Simpan',
        ],
    ],
    'verifikasi' => [
        'modal' => [
            'label' => 'Detail Verifikasi',
            'heading' => 'Detail Verifikasi Gaji :label',
            'verify' => 'Verifikasi',
            'reject' => 'Tolak',
        ],
    ],
    'penggajian' => [
        'columns' => [
            'user.name' => 'Nama Karyawan',
            'alamat' => 'Alamat',
            'jumlah_absensi' => 'Absensi',
            'total_jam_lembur' => 'Total Jam Lembur',
            'placeholder_jam_lembur' => 'Jam Lembur Kosong',
        ],
        'actions' => [
            'view' => [
                'form' => [
                    'name' => 'Nama Karyawan',
                    'alamat' => 'Alamat',
                    'jumlah_absensi' => 'Absensi',
                ],
            ],
        ],
        'modal' => [
            'label' => 'Detail Karyawan',
            'heading' => 'Detail Karyawan :label',
            'submit' => 'Mulai Gaji',
        ],
    ],
];