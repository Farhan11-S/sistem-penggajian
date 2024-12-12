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
    <h3 style="text-align: center; font-size: x-large;">LAPORAN LEMBUR AKTUAL</h3>
    <table style="margin-left:auto; margin-right:auto; font-size: small;">
        <tr>
            <td>BULAN</td>
            <td>:</td>
            <td>{{ $identitas['bulan'] }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $identitas['nip'] }}</td>
        </tr>
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
    <table style="font-size: small;">
        <tr>
            <td>PENERIMAAN</td>
            <td>:</td>
            <td>{{ $detail['penerimaan'] }}</td>
        </tr>
        <tr>
            <td>GAJI</td>
            <td>:</td>
            <td>{{ $detail['gaji'] }}</td>
        </tr>
        <tr>
            <td>SANTUNAN SOSIAL</td>
            <td>:</td>
            <td>{{ $detail['santunan_sosial'] }}</td>
        </tr>
        <tr>
            <td>TUNJANGAN PEMONDOKAN</td>
            <td>:</td>
            <td>{{ $detail['tunjangan_pemondokan'] }}</td>
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
                    <li>{{ $detail['uang_lembur']['biasa'] }}</li>
                    <li>{{ $detail['uang_lembur']['minggu'] }}</li>
                    <li>{{ $detail['uang_lembur']['raya'] }}</li>
                    <li>{{ $detail['uang_lembur']['raya_minggu'] }}</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>JUMLAH</td>
            <td>:</td>
            <td>{{ $detail['jumlah'] }}</td>
        </tr>
        <tr>
            <td>PEMBULATAN BULAN LALU</td>
            <td>:</td>
            <td>{{ $detail['pembulatan_bulan_lalu'] }}</td>
        </tr>
        <tr>
            <td>JUMLAH PENERIMAAN</td>
            <td>:</td>
            <td>{{ $detail['jumlah_penerimaan'] }}</td>
        </tr>
    </table>
</body>

</html>