@extends('layouts.admin.tabler')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Konfigurasi
                    </div>
                    <h2 class="page-title">
                        Jam Kerja
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    @if (Session::get('success'))
                                        <div class="alert alert-success">
                                            {{ Session::get('success') }}
                                        </div>
                                    @endif

                                    @if (Session::get('warning'))
                                        <div class="alert alert-warning">
                                            {{ Session::get('warning') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <a href="#" class="btn btn-primary" id="btnTambahJK"><svg
                                            xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 5l0 14"></path>
                                            <path d="M5 12l14 0"></path>
                                        </svg> Tambah Data</a>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode JK</th>
                                                <th>Nama JK</th>
                                                <th>Awal Jam Masuk</th>
                                                <th>Jam Masuk</th>
                                                <th>Akhir Jam Masuk</th>
                                                <th>Jam Pulang</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jam_kerja as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->kode_jam_kerja }}</td>
                                                    <td>{{ $d->nama_jam_kerja }}</td>
                                                    <td>{{ $d->awal_jam_masuk }}</td>
                                                    <td>{{ $d->jam_masuk }}</td>
                                                    <td>{{ $d->akhir_jam_masuk }}</td>
                                                    <td>{{ $d->jam_pulang }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="#" class="edit btn btn-info btn-sm"
                                                                kode_jam_kerja="{{ $d->kode_jam_kerja }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="icon icon-tabler icon-tabler-edit" width="24"
                                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                    stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                    </path>
                                                                    <path
                                                                        d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1">
                                                                    </path>
                                                                    <path
                                                                        d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z">
                                                                    </path>
                                                                    <path d="M16 5l3 3"></path>
                                                                </svg>
                                                            </a>
                                                            <form action="/konfigurasi/{{ $d->kode_jam_kerja }}/delete"
                                                                method="POST" style="margin-left:5px">
                                                                @csrf
                                                                <a class="btn btn-danger btn-sm delete-confirm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="icon icon-tabler icon-tabler-trash"
                                                                        width="24" height="24" viewBox="0 0 24 24"
                                                                        stroke-width="2" stroke="currentColor"
                                                                        fill="none" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none"></path>
                                                                        <path d="M4 7l16 0"></path>
                                                                        <path d="M10 11l0 6"></path>
                                                                        <path d="M14 11l0 6"></path>
                                                                        <path
                                                                            d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                        </path>
                                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                        </path>
                                                                    </svg>
                                                                </a>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Input Jam Kerja --}}
    <div class="modal modal-blur fade" id="modal-inputjk" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Jam Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/konfigurasi/storejamkerja" method="POST" id="frmJK">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-barcode" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 7v-1a2 2 0 0 1 2 -2h2"></path>
                                            <path d="M4 17v1a2 2 0 0 0 2 2h2"></path>
                                            <path d="M16 4h2a2 2 0 0 1 2 2v1"></path>
                                            <path d="M16 20h2a2 2 0 0 0 2 -2v-1"></path>
                                            <path d="M5 11h1v2h-1z"></path>
                                            <path d="M10 11l0 2"></path>
                                            <path d="M14 11h1v2h-1z"></path>
                                            <path d="M19 11l0 2"></path>
                                        </svg>
                                    </span>
                                    <input type="text" value="" class="form-control"
                                        placeholder="Kode Jam Kerja" name="kode_jam_kerja" id="kode_jam_kerja">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-clock-edit" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M21 12a9 9 0 1 0 -9.972 8.948c.32 .034 .644 .052 .972 .052"></path>
                                            <path d="M12 7v5l2 2"></path>
                                            <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z">
                                            </path>
                                        </svg>
                                    </span>
                                    <input type="text" value="" class="form-control"
                                        placeholder="Nama Jam Kerja" name="nama_jam_kerja" id="nama_jam_kerja">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alarm"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 13m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                            <path d="M12 10l0 3l2 0"></path>
                                            <path d="M7 4l-2.75 2"></path>
                                            <path d="M17 4l2.75 2"></path>
                                        </svg>
                                    </span>
                                    <input type="text" value="" class="form-control"
                                        placeholder="Awal Jam Masuk" name="awal_jam_masuk" id="awal_jam_masuk">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alarm"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 13m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                            <path d="M12 10l0 3l2 0"></path>
                                            <path d="M7 4l-2.75 2"></path>
                                            <path d="M17 4l2.75 2"></path>
                                        </svg>
                                    </span>
                                    <input type="text" value="" class="form-control" placeholder="Jam Masuk"
                                        name="jam_masuk" id="jam_masuk">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alarm"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 13m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                            <path d="M12 10l0 3l2 0"></path>
                                            <path d="M7 4l-2.75 2"></path>
                                            <path d="M17 4l2.75 2"></path>
                                        </svg>
                                    </span>
                                    <input type="text" value="" class="form-control"
                                        placeholder="Akhir Jam Masuk" name="akhir_jam_masuk" id="akhir_jam_masuk">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alarm"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 13m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                            <path d="M12 10l0 3l2 0"></path>
                                            <path d="M7 4l-2.75 2"></path>
                                            <path d="M17 4l2.75 2"></path>
                                        </svg>
                                    </span>
                                    <input type="text" value="" class="form-control" placeholder="Jam Pulang"
                                        name="jam_pulang" id="jam_pulang">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M10 14l11 -11"></path>
                                            <path
                                                d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5">
                                            </path>
                                        </svg>
                                        Simpan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Edit Jam Kerja --}}
    <div class="modal modal-blur fade" id="modal-editjk" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Jam Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadeditform">

                </div>

            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $("#btnTambahJK").click(function() {
                $("#modal-inputjk").modal("show");
            });


            $("#frmJK").submit(function() {
                var kode_jam_kerja = $("#kode_jam_kerja").val();
                var nama_jam_kerja = $("#nama_jam_kerja").val();
                var awal_jam_masuk = $("#awal_jam_masuk").val();
                var jam_masuk = $("#jam_masuk").val();
                var akhir_jam_masuk = $("#akhir_jam_masuk").val();
                var jam_pulang = $("#jam_pulang").val();

                if (kode_jam_kerja == "") {
                    // alert('NIP Harus Diisi');
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Kode Jam Kerja Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#kode_jam_kerja").focus();
                    });
                    return false;
                } else if (nama_jam_kerja == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Nama Jam Kerja Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#nama_jam_kerja").focus();
                    });
                    return false;
                } else if (awal_jam_masuk == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Awal Jam Masuk Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#awal_jam_masuk").focus();
                    });
                    return false;
                } else if (jam_masuk == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Jam Masuk Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#jam_masuk").focus();
                    });
                    return false;
                } else if (akhir_jam_masuk == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Akhir Jam Masuk Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#akhir_jam_masuk").focus();
                    });
                    return false;
                } else if (jam_pulang == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Jam Pulang Harus Diisi',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#jam_pulang").focus();
                    });
                    return false;
                }
            });

            $(".edit").click(function() {
                var kode_jam_kerja = $(this).attr('kode_jam_kerja');
                $.ajax({
                    type: 'POST',
                    url: '/konfigurasi/editjamkerja',
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_jam_kerja: kode_jam_kerja
                    },
                    success: function(respond) {
                        $("#loadeditform").html(respond);
                    }
                });
                $("#modal-editjk").modal("show");
            });

            $(".delete-confirm").click(function(e) {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: 'Are Anda Yakin Menghapus Data?',
                    text: "Data yang sudah terhapus tidak bisa dikembalikan",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                        Swal.fire(
                            'Deleted!',
                            'Data Berhasil Di Hapus.',
                            'success'
                        )
                    }
                })
            });

        });
    </script>
@endpush
