<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PDFController extends Controller
{
    public function rincianGaji()
    {
        $data = [
            'judul' => 'Rician Gaji Karyawan',
            'identitas' => [
                'bulan' => 'November',
                'nip' => '385039030',
                'nama' => 'Joko Surondo',
                'bagian' => 'Peternak',
            ],
            'penerimaan' => [
                'penerimaan' => 'Rp. 1.500.000',
                'gaji' => 'Rp. 3.000.000',
                'santunan_sosial' => 'Joko Surondo',
                'tunjangan_pemondokan' => 'Peternak',
                'uang_lembur' => [
                    'biasa' => 'Rp. 20.000',
                    'minggu' => 'Rp. -',
                    'raya' => 'Rp. -',
                    'raya_minggu' => 'Rp. -',
                ],
                'jumlah' => 'Rp. 3.020.000',
                'pembulatan_bulan_lalu' => 'Rp. -',
                'jumlah_penerimaan' => 'Rp. 3.020.000',
            ],
            'potongan' => [
                'aspek' => 'Rp. -',
                'iuran_pekerja' => 'Rp. 10.000',
                'pinjaman_koperasi' => 'Rp. -',
                'pinjaman_perusahaan' => 'Rp. -',
                'sakit' => 'Rp. -',
                'absen' => 'Rp. -',
                'infaq' => 'Rp. -',
                'pembulatan_bulan_ini' => 'Rp. 10.000',
                'jumlah_potongan' => 'Rp. 10.000',
            ]
        ];
        $pdf = App::make('dompdf.wrapper')->setPaper('a4', 'landscape');
        $pdf->loadView('pdf.rincian-gaji', $data);
        return $pdf->stream();
    }

    // public function laporanLembur()
    // {
    //     $data = [
    //         'judul' => 'Laporan Lembur Aktual',
    //         'identitas' => [
    //             'bulan' => 'November',
    //             'nip' => '385039030',
    //             'nama' => 'Joko Surondo',
    //             'bagian' => 'Peternak',
    //         ],
    //         'detail' => [
    //             'penerimaan' => 'Rp. 1.500.000',
    //             'gaji' => 'Rp. 3.000.000',
    //             'santunan_sosial' => 'Joko Surondo',
    //             'tunjangan_pemondokan' => 'Peternak',
    //             'uang_lembur' => [
    //                 'biasa' => 'Rp. 20.000',
    //                 'minggu' => 'Rp. -',
    //                 'raya' => 'Rp. -',
    //                 'raya_minggu' => 'Rp. -',
    //             ],
    //             'jumlah' => 'Rp. 3.020.000',
    //             'pembulatan_bulan_lalu' => 'Rp. -',
    //             'jumlah_penerimaan' => 'Rp. 3.020.000',
    //         ]
    //     ];
    //     $pdf = App::make('dompdf.wrapper');
    //     $pdf->loadView('pdf.laporan-lembur', $data);
    //     return $pdf->stream();
    // }
}