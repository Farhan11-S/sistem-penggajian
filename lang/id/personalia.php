<?php

return [
    'pengisian' => [
        'columns' => [
            'input_gaji' => 'Input Gaji',
            'reinput_gaji' => 'Input Ulang Gaji',
        ],
        'modal' => [
            'label' => 'Input Gaji',
            'heading' => 'Input Gaji :label',
            'submit' => 'Simpan',
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