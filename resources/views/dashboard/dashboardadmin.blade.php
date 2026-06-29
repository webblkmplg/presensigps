@extends('layouts.admin.tabler')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Overview
                    </div>
                    <h2 class="page-title">
                        Rekap Presensi {{ date('d-m-Y', strtotime(date('Y-m-d')))}}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            
            <div class="row">

                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-success text-white avatar">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-fingerprint" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3"></path>
                                            <path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6"></path>
                                            <path d="M12 11v2a14 14 0 0 0 2.5 8"></path>
                                            <path d="M8 15a18 18 0 0 0 1.8 6"></path>
                                            <path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmlhadir }}
                                    </div>
                                    <div class="text-muted">
                                        Pegawai Hadir
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-info text-white avatar">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-file-text" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                            </path>
                                            <path d="M9 9l1 0"></path>
                                            <path d="M9 13l6 0"></path>
                                            <path d="M9 17l6 0"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmlcuti }}
                                    </div>
                                    <div class="text-muted">
                                        Pegawai Cuti
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-warning text-white avatar">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-mood-sick" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 21a9 9 0 1 1 0 -18a9 9 0 0 1 0 18z"></path>
                                            <path d="M9 10h-.01"></path>
                                            <path d="M15 10h-.01"></path>
                                            <path d="M8 16l1 -1l1.5 1l1.5 -1l1.5 1l1.5 -1l1 1"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmlsakit }}
                                    </div>
                                    <div class="text-muted">
                                        Pegawai Sakit
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-danger text-white avatar">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-clock-bolt" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M20.984 12.53a9 9 0 1 0 -7.552 8.355"></path>
                                            <path d="M12 7v5l3 3"></path>
                                            <path d="M19 16l-2 3h4l-2 3"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmlterlambat }}
                                    </div>
                                    <div class="text-muted">
                                        Pegawai Terlambat
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-info text-white avatar">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trekking"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 4m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M7 21l2 -4" /><path d="M13 21v-4l-3 -3l1 -6l3 4l3 2" /><path d="M10 14l-1.827 -1.218a2 2 0 0 1 -.831 -2.15l.28 -1.117a2 2 0 0 1 1.939 -1.515h1.439l4 1l3 -2" /><path d="M17 12v9" /><path d="M16 20h2" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $rekappresensi->jmldl }}
                                    </div>
                                    <div class="text-muted">
                                        Pegawai Dinas Luar
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        <!-- UPDATE 24 juni 2026 -->
        <div class="row mt-5 mb-5 px-md-3"> 
                <div class="col-12">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;"> 
                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #edf2f6;">
                            <h3 class="card-title" style="font-weight: 600;">
                                Akumulasi Keterlambatan Pegawai - Bulan {{ $namabulan[$bulanini] }} {{ $tahunini }}
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <style>
                                th.sortable {
                                    cursor: pointer;
                                    user-select: none;
                                    transition: background-color 0.2s ease;
                                }
                                th.sortable:hover {
                                    background-color: #e6eef7 !important;
                                    color: #206bc4;
                                }
                                th.sortable i {
                                    font-size: 0.8rem;
                                    margin-left: 5px;
                                    opacity: 0.4;
                                }
                            </style>
                            <table class="table table-vcenter card-table table-striped table-hover" id="table-keterlambatan">
                                <thead>
                                    <tr>
                                        <th class="w-1 sortable text-center">No <i class="fa fa-sort"></i></th>
                                        <th class="sortable">NIP <i class="fa fa-sort"></i></th>
                                        <th class="sortable">Nama Pegawai <i class="fa fa-sort"></i></th>
                                        <th class="sortable">Total Waktu Keterlambatan <i class="fa fa-sort"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($leaderboard_keterlambatan) > 0)
                                        @foreach($leaderboard_keterlambatan as $index => $peg)
                                            @php
                                                $jam = floor($peg['total_menit'] / 60);
                                                $menit = $peg['total_menit'] % 60;
                                                
                                                $teks_waktu = '';
                                                if($jam > 0) {
                                                    $teks_waktu .= $jam . ' Jam ';
                                                }
                                                if($menit > 0) {
                                                    $teks_waktu .= $menit . ' Menit';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-center" data-sort="{{ $index + 1 }}">{{ $index + 1 }}</td>
                                                <td class="text-muted" data-sort="{{ $peg['nip'] }}">{{ $peg['nip'] }}</td>
                                                <td class="font-weight-medium" data-sort="{{ $peg['nama_lengkap'] }}">{{ $peg['nama_lengkap'] }}</td>
                                                <td data-sort="{{ $peg['total_menit'] }}">
                                                    @if($peg['total_menit'] > 0)
                                                        <span class="text-danger font-weight-bold">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px;">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                                                <path d="M12 7v5l3 3"></path>
                                                            </svg>
                                                            {{ $teks_waktu }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success text-white" style="padding: 6px 10px; font-weight: 500;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 3px;">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M5 12l5 5l10 -10"></path>
                                                            </svg>
                                                            Tepat Waktu
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            </div> </div>
        <!-- END of UPDATE 24 Juni 2026 -->

    </div>
@endsection
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('table-keterlambatan');
            const headers = table.querySelectorAll('th.sortable');
            const tbody = table.querySelector('tbody');

            headers.forEach((header, index) => {
                header.addEventListener('click', () => {
                    // Ambil semua baris data di dalam tbody
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    
                    // Cek arah sorting saat ini (ascending atau descending)
                    const isAscending = header.classList.contains('asc');
                    const direction = isAscending ? -1 : 1;

                    rows.sort((a, b) => {
                        // Ambil nilai dari atribut data-sort yang sudah kita siapkan
                        const aColText = a.querySelectorAll('td')[index].getAttribute('data-sort');
                        const bColText = b.querySelectorAll('td')[index].getAttribute('data-sort');

                        // Konversi ke angka jika memungkinkan, jika tidak jadikan huruf kecil untuk sorting alfabet
                        const aColValue = isNaN(aColText) ? aColText.toLowerCase() : parseFloat(aColText);
                        const bColValue = isNaN(bColText) ? bColText.toLowerCase() : parseFloat(bColText);

                        if (aColValue > bColValue) return 1 * direction;
                        if (aColValue < bColValue) return -1 * direction;
                        return 0;
                    });

                    // Bersihkan class asc/desc dari semua header
                    headers.forEach(h => h.classList.remove('asc', 'desc'));

                    // Tambahkan class asc/desc ke header yang baru saja diklik
                    header.classList.add(isAscending ? 'desc' : 'asc');

                    // Masukkan kembali baris yang sudah diurutkan ke dalam tabel
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
    </script>