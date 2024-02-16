<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>A4</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            font-weight: bold
        }

        .tabeldatapegawai {
            margin-top: 20px;
        }

        .tabeldatapegawai td {
            padding: 1px;
        }

        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresensi tr th {
            border: 1px solid #131212;
            padding: 8px;
            background-color: #dbdbdb;
        }

        .tabelpresensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
        }

        .foto {
            width: 40px;
            height: 30px;
        }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->

<body class="A4">
    @php
        function selisih($jam_masuk, $jam_keluar)
        {
            [$h, $m, $s] = explode(':', $jam_masuk);
            $dtAwal = mktime($h, $m, $s, '1', '1', '1');
            [$h, $m, $s] = explode(':', $jam_keluar);
            $dtAkhir = mktime($h, $m, $s, '1', '1', '1');
            $dtSelisih = $dtAkhir - $dtAwal;
            $totalmenit = $dtSelisih / 60;
            $jam = explode('.', $totalmenit / 60);
            $sisamenit = $totalmenit / 60 - $jam[0];
            $sisamenit2 = $sisamenit * 60;
            $jml_jam = $jam[0];
            return $jml_jam . ':' . round($sisamenit2);
        }
    @endphp
    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">

        <!-- Write HTML just like a web page -->
        <table style="width: 100%">
            <tr>
                <td style="width: 35px">
                    <img src="{{ asset('assets/img/icon1.png') }}" width="70" height="70" alt="">
                </td>
                <td>
                    <span id="title">
                        Laporan Presensi Pegawai Periode {{ $namabulan[$bulan] }} {{ $tahun }} <br>
                        BTKLPP Kelas I Palembang
                    </span><br>
                    <span><i>Jl. SMB II No. 55, KM. 11 Alang-alang Lebar, Palembang 30154</i></span>
                </td>
            </tr>
        </table>
        <table class="tabeldatapegawai">
            {{-- <tr>
                <td rowspan="6">
                    @php
                        $path = Storage::url('uploads/pegawai/' . $pegawai->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="" width="110" height="150">
                </td>
            </tr> --}}
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $pegawai->nip }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $pegawai->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $pegawai->jabatan }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>:</td>
                <td>{{ $pegawai->nama_dept }}</td>
            </tr>
            {{-- <tr>
                <td>No Handphone</td>
                <td>:</td>
                <td>{{ $pegawai->no_hp }}</td>
            </tr> --}}
        </table>
        <table class="tabelpresensi">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Keterangan</th>
                <th>Jml Jam</th>
            </tr>
            <tr>
                @foreach ($presensi as $d)
                    @php
                        $jamterlambat = selisih('07:30:00', $d->jam_in);
                    @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</td>
                <td>{{ $d->jam_in }}</td>
                <td>{{ $d->jam_out != null ? $d->jam_out : 'PSW4' }}</td>
                <td>
                    @if ($d->jam_in > '07:30')
                        Terlambat {{ $jamterlambat }}
                    @else
                        Tepat Waktu
                    @endif
                </td>
                <td>
                    @if ($d->jam_out != null)
                        @php
                            $jmljamkerja = selisih($d->jam_in, $d->jam_out);
                        @endphp
                    @else
                        @php
                            $jmljamkerja = 0;
                        @endphp
                    @endif
                    {{ $jmljamkerja }}
                </td>
            </tr>
            @endforeach
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td style="text-align: right;" height: "30px">
                    <u>Tanggal Cetak : {{ date('d-m-Y') }}</u><br>
                    <i><b>Cheked by Administrator</b></i><br>

                </td>
            </tr>
        </table>
    </section>

</body>

</html>
