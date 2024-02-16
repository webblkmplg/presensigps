@extends('layouts.presensi')
@section('content')
    <div class="section" id="user-section">
        <div id="user-detail">
            <div class="avatar">
                @if (!@empty(Auth::guard('pegawai')->user()->foto))
                    @php
                        $path = Storage::url('/uploads/pegawai/' . Auth::guard('pegawai')->user()->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="avatar" class="imaged w64" style="height: 60px">
                @else
                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
                @endif

            </div>
            <div id="user-info">
                <h3 id="user-name">{{ Auth::guard('pegawai')->user()->nama_lengkap }}</h3>
                <span id="user-role">{{ Auth::guard('pegawai')->user()->jabatan }}</span>
            </div>
        </div>
    </div>

    <div class="section" id="menu-section">
        <div class="card">
            <div class="card-body text-center">
                <div class="list-menu">
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/editprofile" class="green" style="font-size: 40px;">
                                <ion-icon name="person-sharp"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Profil</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/presensi/cuti" class="danger" style="font-size: 40px;">
                                <ion-icon name="calendar-number"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Cuti/DL</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/presensi/histori" class="warning" style="font-size: 40px;">
                                <ion-icon name="document-text"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            <span class="text-center">Histori</span>
                        </div>
                    </div>
                    <div class="item-menu text-center">
                        <div class="menu-icon">
                            <a href="/proseslogout" class="orange" style="font-size: 40px;">
                                <ion-icon name="log-out-outline"></ion-icon>
                            </a>
                        </div>
                        <div class="menu-name">
                            Log Out
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section mt-2" id="presence-section">
        <div class="todaypresence">
            <div class="row">
                <div class="col-6">
                    <a href="/presensi/create" class="item">
                    <div class="card gradasigreen">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    @if ($presensihariini != null)
                                        @php
                                            $path = Storage::url('/uploads/absensi/' . $presensihariini->foto_in);
                                        @endphp
                                        <img src="{{ url($path) }}" alt="" class="imaged w48">
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Masuk</h4>
                                    <span>{{ $presensihariini != null ? $presensihariini->jam_in : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="/presensi/create" class="item">
                    <div class="card gradasired">
                        <div class="card-body">
                            <div class="presencecontent">
                                <div class="iconpresence">
                                    @if ($presensihariini != null)
                                        @php
                                            $path = Storage::url('/uploads/absensi/' . $presensihariini->foto_out);
                                        @endphp
                                        <img src="{{ url($path) }}" alt="" class="imaged w48">
                                    @else
                                        <ion-icon name="camera"></ion-icon>
                                    @endif
                                </div>
                                <div class="presencedetail">
                                    <h4 class="presencetitle">Pulang</h4>
                                    <span>{{ $presensihariini != null && $presensihariini->jam_out != null ? $presensihariini->jam_out : 'Belum Absen' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
        <h3>Rekap Presensi Bulan {{ $namabulan[$bulanini] }} Tahun {{ $tahunini }}</h3>
        <div id="rekappresensi">
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 16px 12px !important; line-height: 0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top: 3px; right: 10px; font-size: 0.6rem; z-index:999">{{ $rekappresensi->jmlhadir }}</span>
                            <ion-icon name="accessibility-outline" style="font-size: 1.6 rem;" class="text-primary">
                            </ion-icon><br>
                            <span style="font-size: 0.8rem; font-weight: 500">Hadir</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 16px 12px !important; line-height: 0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top: 3px; right: 10px; font-size: 0.6rem; z-index:999">{{ $rekapcuti->jmlcuti }}</span>
                            <ion-icon name="newspaper-outline" style="font-size: 1.6 rem;" class="text-success">
                            </ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight: 500">Cuti</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 16px 12px !important; line-height: 0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top: 3px; right: 10px; font-size: 0.6rem; z-index:999">{{ $rekapcuti->jmlsakit }}</span>
                            <ion-icon name="medkit-outline" style="font-size: 1.6 rem;" class="text-warning">
                            </ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight: 500">Sakit</span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body text-center" style="padding: 16px 12px !important; line-height: 0.8rem">
                            <span class="badge bg-danger"
                                style="position: absolute; top: 3px; right: 10px; font-size: 0.6rem; z-index:999">{{ $rekapcuti->jmldl }}</span>
                            <ion-icon name="earth-outline" style="font-size: 1.6 rem;" class="text-danger">
                            </ion-icon>
                            <br>
                            <span style="font-size: 0.8rem; font-weight: 500">DL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="presencetab mt-2">
            <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                <ul class="nav nav-tabs style1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                            Bulan Ini
                        </a>
                    </li>
                    <!--<li class="nav-item">-->
                    <!--    <a class="nav-link" data-toggle="tab" href="#profile" role="tab">-->
                    <!--        Leaderboard-->
                    <!--    </a>-->
                    <!--</li>-->
                </ul>
            </div>
            <div class="tab-content mt-2" style="margin-bottom:100px;">
                <div class="tab-pane fade show active" id="home" role="tabpanel">
                    <style>
                        .historicontent{
                            display: flex;
                        }

                        .datapresensi{
                            margin-left: 10px;
                        }

                        .keterangan{
                            margin-top: 0px;
                        }
                    </style>
                    @foreach ($historibulanini as $d)
                         @php
                            $path = Storage::url('uploads/absensi/' . $d->foto_in);
                        @endphp
                        <div class="card">
                            <div class="card-body">
                                <div class="historicontent">
                                    <div class="iconpresensi">
                                        {{-- <ion-icon name="finger-print-outline" style="font-size: 48px;" class="text-success"></ion-icon> --}}
                                        <img src="{{ url($path) }}" alt="" class="imaged w48 rounded">
                                    </div>
                                    <div class="datapresensi">
                                        <h3 style="line-height: 3px">{{ $d->nama_jam_kerja}}</h3>
                                        <h4 style="margin: 0px !important">{{ date("d-m-Y",strtotime($d->tgl_presensi))}}</h4>
                                        <span>
                                            {!! $d->jam_in != null ? date("H:i",strtotime($d->jam_in)) : '<span class="text-danger">Belum Scan Pulang</span>' !!}
                                            {!! $d->jam_out != null ? "-". date("H:i",strtotime($d->jam_out)) : '<span class="text-danger">- Belum Scan Pulang</span>' !!}
                                        </span>
                                        <div id="keterangan" class="mt-0">
                                            @php
                                            //jam ketika absen
                                            $jam_in = date("H:i",strtotime($d->jam_in));

                                            //jam jadwal masuk
                                            $jam_masuk = date("H:i",strtotime($d->jam_masuk));

                                            $jadwal_jam_masuk = $d->tgl_presensi." ".$jam_masuk;
                                            $jam_presensi = $d->tgl_presensi." ".$jam_in;
                                            @endphp
                                            @if ($jam_in > $jam_masuk)
                                            @php
                                                $jmlterlambat = hitungjamterlambat($jadwal_jam_masuk,$jam_presensi);
                                            @endphp
                                            <span class="danger">Terlambat {{ $jmlterlambat }}</span>
                                            @else
                                            <span style="color:green">Tepat Waktu</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel">
                    <ul class="listview image-listview">
                        @foreach ($leaderboard as $d)
                            <li>
                                <div class="item">
                                    <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                                    <div class="in">
                                        <div>
                                            <b>{{ $d->nama_lengkap }}</b><br>
                                            <small class="text-muted">{{ $d->jabatan }}</small>
                                        </div>
                                        <span
                                            class="badge {{ $d->jam_in < '07:30' ? 'bg-success' : 'bg-danger' }}">{{ $d->jam_in }}</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>

            </div>
        </div>
    </div>
@endsection
