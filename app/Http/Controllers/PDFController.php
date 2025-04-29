<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\PotonganGajiKaryawan;
use App\Models\StatusGajiKaryawan;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PDFController extends Controller
{
    public function rincianGaji(string $id)
    {
        $status = StatusGajiKaryawan::findOrFail($id);
        $month = Carbon::parse($status->created_at);
        $karyawan = $status->karyawan;
        $user = $karyawan->user;
        $gaji = $status->gajiKaryawan;
        $potongan = $status->potonganGajiKaryawan;
        $potongan = $status->potonganGajiKaryawan;
        $jumlahPenerimaan = ($gaji->jumlah_penerimaan - $potongan->jumlah_potongan);
        // dd($user->name);
        
        $data = [
            'judul' => 'Rician Gaji Karyawan',
            'identitas' => [
                'bulan' => $month->format('F'),
                'nip' => '385039030',
                'nama' => $user->name,
                'bagian' => 'Karyawan',
            ],
            'penerimaan' => [
                'penerimaan' => 'Rp. 1.500.000',
                'gaji' => $gaji->gaji_pokok,
                'santunan_sosial' => $gaji->santunan_sosial,
                'tunjangan_pemondokan' => $gaji->tunjangan_pemondokan,
                'uang_lembur' => [
                    'biasa' => 'Rp. 20.000',
                    'minggu' => 'Rp. -',
                    'raya' => 'Rp. -',
                    'raya_minggu' => 'Rp. -',
                ],
                'jumlah' => $gaji->jumlah_penerimaan,
                // 'pembulatan_bulan_lalu' => $gaji->pembulatan_bulan_lalu,
                'jumlah_penerimaan' => $jumlahPenerimaan,
            ],
            'potongan' => [
                'aspek' => 'Rp. -dsknjsn',
                'iuran_pekerja' => $potongan->iuran_pekerja,
                'pinjaman_koperasi' => $potongan->pinjaman_koperasi,
                'pinjaman_perusahaan' => $potongan->pinjaman_perusahaan,
                'sakit' => $potongan->sakit,
                'absen' => $potongan->absen,
                'infaq' => $potongan->infaq,
                'pembulatan_bulan_ini' => $potongan->pembulatan_bulan_ini,
                'jumlah_potongan' => $potongan->jumlah_potongan,
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