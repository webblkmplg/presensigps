@extends('layouts.presensi')
@section('header')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <style>
        .datepicker-modal {
            max-height: 430px !important;
        }

        .datepicker-date-display {
            background-color: #0f3a7e !important;
        }
    </style>
    <!------- App Header -------->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Form Cuti/ Sakit/ Dinas Luar</div>
        <div class="right"></div>
    </div>
    <!------- * App Header -------->
@endsection
@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            <form method="POST" action="/presensi/storecuti" id="frmCuti">
                @csrf
                <div class="form-group">
                    <input type="text" id="tgl_cuti" name="tgl_cuti" class="form-control datepicker"
                        placeholder="Tanggal" autocomplete="off">
                </div>
                <div class="form-group">
                    <select name="status" id="status" class="form-control">
                        <option value="">Cuti/Sakit/DL</option>
                        <option value="c">Cuti Tahunan</option>
                        <option value="s">Sakit</option>
                        <option value="d">Dinas Luar</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="keterangan" id="keterangan" class="form-control" cols="30" rows="5"
                        placeholder="Alasan/Keterangan"></textarea>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary w-100">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        var currYear = (new Date()).getFullYear();

        $(document).ready(function() {
            $(".datepicker").datepicker({

                format: "yyyy-mm-dd"
            });

            $('#tgl_cuti').change(function(e) {
                var tgl_cuti = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: '/presensi/cekpengajuancuti',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tgl_cuti: tgl_cuti
                    },
                    cache: false,
                    success: function(respond) {
                        if (respond == 1) {
                            Swal.fire({
                                title: 'Oops!',
                                text: 'Anda sudah melakukan pengajuan pada tanggal tersebut',
                                icon: 'warning'
                            }).then((result) => {
                                $("#tgl_cuti").val("");
                            });
                        }
                    }

                });
            });

            $("frmCuti").submit(function() {
                var tgl_cuti = $("#tgl_cuti").val();
                var status = $("#status").val();
                var keterangan = $("#keterangan").val();
                if (tgl_cuti == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Tanggal Harus Diisi',
                        icon: 'warning'
                    });
                    return false;
                } else if (status == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Status Cuti/Sakit/DL Harus Diisi',
                        icon: 'warning'
                    });
                    return false;
                } else if (keterangan == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Alasan/Keterangan Harus Diisi',
                        icon: 'warning'
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
