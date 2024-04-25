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

        #keterangan{
            height: 5rem !important;
        }
    </style>
    <!------- App Header -------->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Edit Data Dinas Luar</div>
        <div class="right"></div>
    </div>
    <!------- * App Header -------->
@endsection
@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            <form method="POST" action="/dinasluar/{{ $datacuti->kode_cuti }}/update" id="frmCuti" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="text" id="tgl_cuti_dari" value="{{ $datacuti->tgl_cuti_dari}}" name="tgl_cuti_dari" class="form-control datepicker"
                        placeholder="Dari" autocomplete="off">
                </div>
                <div class="form-group">
                    <input type="text" id="tgl_cuti_sampai" value="{{ $datacuti->tgl_cuti_sampai}}" name="tgl_cuti_sampai" class="form-control datepicker"
                        placeholder="Sampai" autocomplete="off">
                </div>
                <div class="form-group">
                    <input type="text" id="jml_hari" name="jml_hari" class="form-control"
                        placeholder="Jumlah Hari" autocomplete="off" readonly>
                </div>
                <div class="custom-file-upload" id="fileUpload1" style="height: 100px !important">
                    <input type="file" name="sid" id="fileuploadInput" accept=".png, .jpg, .jpeg">
                    <label for="fileuploadInput">
                        <span>
                            <strong>
                                <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" 
                    aria-label="cloud upload outline"></ion-icon>
                                <i>Tap untuk Upload Surat Tugas (Optional)</i>
                            </strong>
                        </span>
                    </label>
                </div>
                <div class="form-group">
                    <input type="text" id="keterangan" value="{{ $datacuti->keterangan }}" name="keterangan" class="form-control"
                        placeholder="Keterangan" autocomplete="off">
                </div>
                {{-- <div class="form-group">
                    <textarea name="keterangan" id="keterangan" class="form-control" cols="30" rows="5"
                        placeholder="Alasan/Keterangan"></textarea>
                </div> --}}
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

        function loadjumlahhari() {
            var dari = $("#tgl_cuti_dari").val();
            var sampai = $("#tgl_cuti_sampai").val();
            var date1 = new Date(dari);
            var date2 = new Date(sampai);

            // To calculate the time difference of two dates
            var Difference_In_Time = date2.getTime() - date1.getTime();

            // To Calculate the no. of days between two dates
            var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

            if(dari == "" || sampai == ""){
                var jmlhari = 0;
            } else {
                var jmlhari = Difference_In_Days + 1;
            }
            // To display the final no. of days (result)
            $("#jml_hari").val(jmlhari + " Hari")
        }
        loadjumlahhari();

        $("#tgl_cuti_dari,#tgl_cuti_sampai").change(function(e){
            loadjumlahhari();
        });

            // $('#tgl_cuti').change(function(e) {
            //     var tgl_cuti = $(this).val();
            //     $.ajax({
            //         type: 'POST',
            //         url: '/presensi/cekpengajuancuti',
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             tgl_cuti: tgl_cuti
            //         },
            //         cache: false,
            //         success: function(respond) {
            //             if (respond == 1) {
            //                 Swal.fire({
            //                     title: 'Oops!',
            //                     text: 'Anda sudah melakukan pengajuan pada tanggal tersebut',
            //                     icon: 'warning'
            //                 }).then((result) => {
            //                     $("#tgl_cuti").val("");
            //                 });
            //             }
            //         }

            //     });
            // });

            $("#frmCuti").submit(function() {
                var tgl_cuti_dari = $("#tgl_cuti_dari").val();
                var tgl_cuti_sampai = $("#tgl_cuti_sampai").val();
                var keterangan = $("#keterangan").val();
                if (tgl_cuti_dari == "" || tgl_cuti_sampai == "") {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Tanggal Harus Diisi',
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
