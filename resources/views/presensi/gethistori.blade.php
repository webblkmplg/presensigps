<style>
    .historicontent{
        display: flex;
        margin-top: 10px;
    }

    .datapresensi{
        margin-left: 10px;
    }

    .card{
            border: 1px solid rgb(135, 135, 207)
        }
</style>

@if ($histori->isEmpty())
    <div class="alert alert-outline-warning">Data Belum Ada</div>
@endif
@foreach ($histori as $d)
@if ($d->status=="h")
<div class="card mb-1">
    <div class="card-body">
        <div class="historicontent">
            <div class="iconpresensi">
                <ion-icon name="finger-print-outline" style="font-size: 48px;" class="text-success"></ion-icon>
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
@elseif($d->status=="d")
<div class="card mb-1">
    <div class="card-body">
        <div class="historicontent">
            <div class="iconpresensi">
                <ion-icon name="earth-outline" style="font-size: 48px;" class="text-primary"></ion-icon>
                {{-- <img src="{{ url($path) }}" alt="" class="imaged w48 rounded"> --}}
            </div>
            <div class="datapresensi">
                <h3 style="line-height: 3px">Dinas Luar - {{ $d->kode_cuti }}</h3>
                <h4 style="margin: 0px !important">{{ date("d-m-Y",strtotime($d->tgl_presensi))}}</h4>
                <span>{{ $d->keterangan }}</span>
                <br>
                <span style="color: blue"><ion-icon name="document-attach-outline"></ion-icon>Dokumen</span>
            </div>
        </div>
    </div>
</div>

@elseif($d->status=="s")
<div class="card mb-1">
    <div class="card-body">
        <div class="historicontent">
            <div class="iconpresensi">
                <ion-icon name="medkit-outline" style="font-size: 48px;" class="text-danger"></ion-icon>
                {{-- <img src="{{ url($path) }}" alt="" class="imaged w48 rounded"> --}}
            </div>
            <div class="datapresensi">
                <h3 style="line-height: 3px">Sakit - {{ $d->kode_cuti }}</h3>
                <h4 style="margin: 0px !important">{{ date("d-m-Y",strtotime($d->tgl_presensi))}}</h4>
                <span>{{ $d->keterangan }}</span>
                <br>
                <span style="color: blue"><ion-icon name="document-attach-outline"></ion-icon>Dokumen</span>
            </div>
        </div>
    </div>
</div>

@elseif($d->status=="c")
<div class="card mb-1">
    <div class="card-body">
        <div class="historicontent">
            <div class="iconpresensi">
                <ion-icon name="document-outline" style="font-size: 48px;" class="text-warning"></ion-icon>
                {{-- <img src="{{ url($path) }}" alt="" class="imaged w48 rounded"> --}}
            </div>
            <div class="datapresensi">
                <h3 style="line-height: 3px">Cuti - {{ $d->kode_cuti }}</h3>
                <h4 style="margin: 0px !important">{{ date("d-m-Y",strtotime($d->tgl_presensi))}}</h4>
                <span class="text-info">{{ $d->nama_cuti }}</span>
                <br>
                <span>{{ $d->keterangan }}</span>
            </div>
        </div>
    </div>
</div>

@endif
@endforeach
