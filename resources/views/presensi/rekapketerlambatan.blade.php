@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Rekap Akumulasi Keterlambatan
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <div class="row mb-3 px-md-3">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body">
                        <form action="/admin/rekap-keterlambatan" method="GET">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="form-label">Bulan</label>
                                        <select name="bulan" id="bulan" class="form-select">
                                            <option value="">-- Seluruh Bulan (Akumulasi 1 Tahun) --</option>
                                            @foreach($namabulan as $key => $nama_bln)
                                                @if($key != 0)
                                                    <option value="{{ $key }}" {{ $bulan_input == $key ? 'selected' : '' }}>
                                                        {{ $nama_bln }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-3 mt-md-0">
                                    <div class="form-group">
                                        <label class="form-label">Tahun</label>
                                        <select name="tahun" id="tahun" class="form-select" required>
                                            @php
                                                // Tahun awal aplikasi presensi berjalan
                                                $tahun_awal = 2022; 
                                                $tahun_sekarang = date('Y');
                                            @endphp
                                            @for($t = $tahun_sekarang; $t >= $tahun_awal; $t--)
                                                <option value="{{ $t }}" {{ $tahun_input == $t ? 'selected' : '' }}>
                                                    {{ $t }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end mt-3 mt-md-0">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <circle cx="10" cy="10" r="7"></circle>
                                            <line x1="21" y1="21" x2="15" y2="15"></line>
                                        </svg>
                                        Tampilkan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 mb-5 px-md-3">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #edf2f6;">
                        <h3 class="card-title" style="font-weight: 600;">
                            Data Keterlambatan Pegawai - 
                            @if(!empty($bulan_input))
                                Bulan {{ $namabulan[$bulan_input] }} Tahun {{ $tahun_input }}
                            @else
                                Akumulasi Seluruh Bulan Tahun {{ $tahun_input }}
                            @endif
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
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted" style="padding: 20px;">
                                            Belum ada data pegawai atau presensi ditemukan.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('table-keterlambatan');
        if(!table) return;

        const headers = table.querySelectorAll('th.sortable');
        const tbody = table.querySelector('tbody');

        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                const rows = Array.from(tbody.querySelectorAll('tr'));
                if(rows.length === 0 || rows[0].querySelector('td[colspan]')) return;

                const isAscending = header.classList.contains('asc');
                const direction = isAscending ? -1 : 1;

                rows.sort((a, b) => {
                    const aColText = a.querySelectorAll('td')[index].getAttribute('data-sort');
                    const bColText = b.querySelectorAll('td')[index].getAttribute('data-sort');

                    const aColValue = isNaN(aColText) ? aColText.toLowerCase() : parseFloat(aColText);
                    const bColValue = isNaN(bColText) ? bColText.toLowerCase() : parseFloat(bColText);

                    if (aColValue > bColValue) return 1 * direction;
                    if (aColValue < bColValue) return -1 * direction;
                    return 0;
                });

                headers.forEach(h => h.classList.remove('asc', 'desc'));
                header.classList.add(isAscending ? 'desc' : 'asc');

                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });
</script>
@endsection