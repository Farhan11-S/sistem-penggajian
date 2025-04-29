<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }}</title>
    <style>
    li {
        list-style-type: none;
    }

    table,
    th,
    td {
        border: 0px;
    }
    </style>
</head>

<body>
    <h1 style="text-align: center; font-size: x-large;">RINCIAN GAJI KARYAWAN</h1>
    <table style="margin-left:auto; margin-right:auto; font-size: small;">
        <tr>
            <td>BULAN</td>
            <td>:</td>
            <td>{{ $identitas['bulan'] }}</td>
        </tr>
        <!-- <tr>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $identitas['nip'] }}</td>
        </tr> -->
        <tr>
            <td>NAMA</td>
            <td>:</td>
            <td>{{ $identitas['nama'] }}</td>
        </tr>
        <tr>
            <td>BAGIAN</td>
            <td>:</td>
            <td>{{ $identitas['bagian'] }}</td>
        </tr>
    </table>
    <hr style="height: 1px; background-color: black; margin: 30px 0;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">
                <h3 style="margin-top: 0; text-transform: uppercase;">Penerimaan</h3>
                <table style="font-size: small;">
                    <!-- <tr>
                        <td>PENERIMAAN</td>
                        <td>:</td>
                        <td>{{ $penerimaan['penerimaan'] }}</td>
                    </tr> -->
                    <tr>
                        <td>GAJI</td>
                        <td>:</td>
                        <td>{{ $penerimaan['gaji'] }}</td>
                    </tr>
                    <tr>
                        <td>SANTUNAN SOSIAL</td>
                        <td>:</td>
                        <td>{{ $penerimaan['santunan_sosial'] }}</td>
                    </tr>
                    <tr>
                        <td>TUNJANGAN PEMONDOKAN</td>
                        <td>:</td>
                        <td>{{ $penerimaan['tunjangan_pemondokan'] }}</td>
                    </tr>
                    <tr>
                        <td>UANG LEMBUR</td>
                    </tr>
                    <tr>
                        <td>
                            <ul style="margin: 0;">
                                <li style="list-style-type: circle;">BIASA</li>
                                <li style="list-style-type: circle;">MINGGU</li>
                                <li style="list-style-type: circle;">RAYA</li>
                                <li style="list-style-type: circle;">RAYA/MINGGU</li>
                            </ul>
                        </td>
                        <td>
                            <ul style="padding: 0; margin: 0;">
                                <li>:</li>
                                <li>:</li>
                                <li>:</li>
                                <li>:</li>
                            </ul>
                        </td>
                        <td>
                            <ul style="padding: 0; margin: 0;">
                                <li>{{ $penerimaan['uang_lembur']['biasa'] }}</li>
                                <li>{{ $penerimaan['uang_lembur']['minggu'] }}</li>
                                <li>{{ $penerimaan['uang_lembur']['raya'] }}</li>
                                <li>{{ $penerimaan['uang_lembur']['raya_minggu'] }}</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>JUMLAH</td>
                        <td>:</td>
                        <td>{{ $penerimaan['jumlah'] }}</td>
                    </tr>
                    <!-- <tr>
                        <td>JUMLAH PENERIMAAN</td>
                        <td>:</td>
                        <td>{{ $penerimaan['jumlah_penerimaan'] }}</td>
                    </tr> -->
                </table>
            </td>
            <!-- <td style="height: max-content;">
                <div style="height: full; border: 2px dash black;"></div>
            </td> -->
            <td style="width: 50%;">
                <h3 style="margin-top: 0; text-transform: uppercase;">Potongan</h3>
                <table style="font-size: small;">
                    <!-- <tr>
                        <td>ASTEK</td>
                        <td>:</td>
                        <td>{{ $potongan['aspek'] }}</td>
                    </tr> -->
                    <tr>
                        <td>IURAN PEKERJA</td>
                        <td>:</td>
                        <td>{{ $potongan['iuran_pekerja'] }}</td>
                    </tr>
                    <tr>
                        <td>PINJAMAN KOPERASI</td>
                        <td>:</td>
                        <td>{{ $potongan['pinjaman_koperasi'] }}</td>
                    </tr>
                    <tr>
                        <td>PINJAMAN PERUSAHAAN</td>
                        <td>:</td>
                        <td>{{ $potongan['pinjaman_perusahaan'] }}</td>
                    </tr>
                    <tr>
                        <td>SAKIT</td>
                        <td>:</td>
                        <td>{{ $potongan['sakit'] }}</td>
                    </tr>
                    <tr>
                        <td>ABSEN</td>
                        <td>:</td>
                        <td>{{ $potongan['absen'] }}</td>
                    </tr>
                    <tr>
                        <td>INFAQ</td>
                        <td>:</td>
                        <td>{{ $potongan['infaq'] }}</td>
                    </tr>
                    <tr>
                        <td>PEMBULATAN BULAN INI</td>
                        <td>:</td>
                        <td>{{ $potongan['pembulatan_bulan_ini'] }}</td>
                    </tr>
                    <tr>
                        <td>JUMLAH POTONGAN</td>
                        <td>:</td>
                        <td>{{ $potongan['jumlah_potongan'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <span style="text-transform: uppercase; margin-top: 5px;">Jumlah Penerimaan : {{ $penerimaan['jumlah'] }} - Rp.
        {{ $potongan['jumlah_potongan'] }} =
        {{ $penerimaan['jumlah_penerimaan'] }}</span>
    <hr style="border: 0.5px dashed black; float: none; margin: 10px 0;">
</body>

</html>