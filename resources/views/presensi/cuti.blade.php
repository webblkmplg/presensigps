@extends('layouts.presensi')
@section('header')
    <!------- App Header -------->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Data Cuti/Sakit/Dinas Luar</div>
        <div class="right"></div>
    </div>
    <style>
        .historicontent{
            display: flex;
            gap: 1px;
            margin-top:10px;
            
        }

        .datapresensi{
            margin-left: 10px;
        }

        .status {
            position: absolute;
            right: 5px;
        }

        .card{
            border: 1px solid rgb(135, 135, 207)
        }
    </style>
    <!------- * App Header -------->
@endsection
@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            @php
                $messagesuccess = Session::get('success');
                $messageerror = Session::get('error');
            @endphp
            @if (Session::get('success'))
                <div class="alert alert-success">
                    {{ $messagesuccess }}
                </div>
            @endif
            @if (Session::get('error'))
                <div class="alert alert-danger">
                    {{ $messageerror }}
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form method="GET" action="/presensi/cuti">
                <div class="row">
                    <div class="col-8">
                        <div class="form-group">
                            <select name="bulan" id="bulan" class="form-control selectmaterialize">
                                <option value="">Bulan</option>
                                @for($i= 1; $i <= 12; $i++)
                                <option {{Request('bulan') == $i ? 'selected' : '' }} value="{{ $i }}">{{ $namabulan[$i] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                
                    <div class="col-4">
                        <div class="form-group">
                            <select name="tahun" id="tahun" class="form-control selectmaterialize">
                                <option value="">Tahun</option>
                                @php
                                    $tahun_awal = 2023;
                                    $tahun_sekarang = date("Y");
                                    for ($t=$tahun_awal; $t<=$tahun_sekarang; $t++)
                                    {
                                        if(Request('tahun')==$t)
                                        {
                                            $selected='selected'; 
                                            
                                        } else 
                                        {
                                            $selected='';
                                        }
                                        echo "<option $selected value='$t'>$t</option>";
                                    }                            
                                @endphp
                            </select>
                        </div>
                    </div>
                </div>
               
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary w-100">Cari Data</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <div class="row" style="position: fixed; width:100%; margin:auto; overflow-y:scroll; height:430px">
        <div class="col">
            @foreach ($datacuti as $d)
            @php
             if ($d->status=="c") {
                $status = "Cuti";
             } else if ($d->status=="s") {
                $status = "Sakit";
             }  else if ($d->status=="d") {
                $status = "Dinas Luar";
             }else{
                $status = "Not Found";
             }
            @endphp
            <div class="card mt-1 card_izin" kode_cuti="{{$d->kode_cuti}}" status_approved="{{ $d->status_approved }}" data-toggle="modal" data-target="#actionSheetIconed">
                <div class="card-body">
                    <div class="historicontent">
                        <div class="iconpresensi">
                            @if ($d->status=="c")
                            <ion-icon name="document-outline" style="font-size: 48px; color:#11796e"></ion-icon>
                            @elseif ($d->status=="s")
                            <ion-icon name="medkit-outline" style="font-size: 48px; color:#d42929"></ion-icon>
                            @elseif ($d->status=="d")
                            <ion-icon name="globe-outline" style="font-size: 48px; color:#21c021"></ion-icon>
                            @endif
                        </div>
                        <div class="datapresensi">
                            <h3 style="line-height: 3px">{{ date("d-m-Y", strtotime($d->tgl_cuti_dari)) }} ({{ $status }})</h3>
                            <small>{{ date("d-m-Y", strtotime($d->tgl_cuti_dari)) }} s/d {{ date("d-m-Y", strtotime($d->tgl_cuti_sampai)) }}</small>
                            <p>
                                @if ($d->status=="c")
                                <span class="badge bg-warning">{{ $d->nama_cuti }}</span>
                                <br>
                                @endif
                                {{ $d->keterangan }}
                            <br>
                            @if (!@empty($d->doc_sid))
                            <span style="color: blue"><ion-icon name="document-attach-outline"></ion-icon>Lihat Dokumen</span>
                            @endif
                            </p>
                        </div>
                        <div class="status">
                            @if ($d->status_approved==0)
                                <span class="badge bg-warning">Pending</span>
                            @elseif ($d->status_approved==1)
                                <span class="badge bg-success">Disetujui</span>
                            @elseif ($d->status_approved==2)
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                            <p style="margin-top: 5px; font-weight:bold">{{ hitunghari($d->tgl_cuti_dari,$d->tgl_cuti_sampai)}} Hari</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    {{-- <div class="row">
        <div class="col">&nbsp; <br> &nbsp; <br></div>
    </div> --}}
    <div class="fab-button animate bottom-right dropdown" style="margin-bottom:70px">
        <a href="#" class="fab bg-primary" data-toggle="dropdown">
            <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add-outline"></ion-icon>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item bg-primary" href="/dinasluar">
                <ion-icon name="document-outline" role="img" class="md hydrated" aria-label="image-outline"></ion-icon>
                <p>Dinas Luar</p>
            </a>
            <a class="dropdown-item bg-primary" href="/cutisakit">
                <ion-icon name="document-outline" role="img" class="md hydrated" aria-label="image-outline"></ion-icon>
                <p>Sakit</p>
            </a>
            <a class="dropdown-item bg-primary" href="/izincuti">
                <ion-icon name="document-outline" role="img" class="md hydrated" aria-label="image-outline"></ion-icon>
                <p>Cuti</p>
            </a>
        </div>
    </div>

    {{-- Modal Pop UP Action  --}}
 
<div class="modal fade action-sheet" id="actionSheetIconed" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aksi</h5>
            </div>
            <div class="modal-body" id="showact">
 
            </div>
        </div>
    </div>
</div>
<div class="modal fade dialogbox" id="deleteConfirm" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yakin Dihapus ?</h5>
            </div>
            <div class="modal-body">
                Informasi Akan dihapus
            </div>
            <div class="modal-footer">
                <div class="btn-inline">
                    <a href="#" class="btn btn-text-secondary" data-dismiss="modal">Batalkan</a>
                    <a href="" class="btn btn-text-primary" id="hapuspengajuan">Hapus</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function(){
        $(".card_izin").click(function(e){
            var kode_cuti = $(this).attr("kode_cuti");
            var status_approved = $(this).attr("status_approved");
            
            if(status_approved==1){
                Swal.fire({
                            title: 'OOps!',
                            text: 'Data Sudah Diverifikasi, Tidak Dapat Diubah',
                            icon: 'warning',
                        })
            } else {
                $("#showact").load('/cuti/' + kode_cuti + '/showact');
            }
           
        });
    });
</script>
@endpush