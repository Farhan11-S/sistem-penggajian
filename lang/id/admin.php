<?php

return [
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