<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::middleware(['guest:pegawai'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});

Route::middleware(['guest:user'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

Route::middleware(['auth:pegawai'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    //presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);

    //edit profile
    Route::get('/editprofile', [PresensiController::class, 'editprofile']);
    Route::post('presensi/{nip}/updateprofile', [PresensiController::class, 'updateprofile']);

    //histori
    Route::get('/presensi/histori', [PresensiController::class, 'histori']);
    Route::post('/gethistori', [PresensiController::class, 'gethistori']);

    //cuti
    Route::get('/presensi/cuti', [PresensiController::class, 'cuti']);
    Route::get('/presensi/buatcuti', [PresensiController::class, 'buatcuti']);
    Route::post('/presensi/storecuti', [PresensiController::class, 'storecuti']);
    Route::post('/presensi/cekpengajuancuti', [PresensiController::class, 'cekpengajuancuti']);

    //Dinas Luar
    Route::get('/dinasluar', 'App\Http\Controllers\DinasluarController@create')->name('dinasluar');
    Route::post('/dinasluar/store', 'App\Http\Controllers\DinasluarController@store')->name('dinasluarstore');
    Route::get('/dinasluar/{kode_cuti}/edit', 'App\Http\Controllers\DinasluarController@edit')->name('dinasluaredit');
    Route::post('/dinasluar/{kode_cuti}/update', 'App\Http\Controllers\DinasluarController@update')->name('dinasluarupdate');

    //Cuti Sakit
    Route::get('/cutisakit', 'App\Http\Controllers\CutisakitController@create')->name('cutisakit');
    Route::post('/cutisakit/store', 'App\Http\Controllers\CutisakitController@store')->name('cutisakitstore');
    Route::get('/cutisakit/{kode_cuti}/edit', 'App\Http\Controllers\CutisakitController@edit')->name('cutisakitedit');
    Route::post('/cutisakit/{kode_cuti}/update', 'App\Http\Controllers\CutisakitController@update')->name('cutisakitupdate');

     //Izin Cuti
     Route::get('/izincuti', 'App\Http\Controllers\IzincutiController@create')->name('izincuti');
     Route::post('/izincuti/store', 'App\Http\Controllers\IzincutiController@store')->name('izincutistore');
     Route::get('/izincuti/{kode_cuti}/edit', 'App\Http\Controllers\IzincutiController@edit')->name('izincutiedit');
     Route::post('/izincuti/{kode_cuti}/update', 'App\Http\Controllers\IzincutiController@update')->name('izincutiupdate'); 
     
     Route::get('/cuti/{kode_cuti}/showact', 'App\Http\Controllers\PresensiController@showact')->name('presensishowact');
     Route::get('/cuti/{kode_cuti}/delete', 'App\Http\Controllers\PresensiController@deletecuti')->name('presensideletecuti');
});

Route::middleware(['auth:user'])->group(function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin']);

    //Pegawai
    Route::get('/pegawai', [PegawaiController::class, 'index']);
    Route::post('/pegawai/store', [PegawaiController::class, 'store']);
    Route::post('/pegawai/edit', [PegawaiController::class, 'edit']);
    Route::post('/pegawai/{nip}/update', [PegawaiController::class, 'update']);
    Route::post('/pegawai/{nip}/delete', [PegawaiController::class, 'delete']);
    Route::get('/pegawai/{nip}/resetpassword', [PegawaiController::class, 'resetpassword']);

    //Unit Kerja
    Route::get('/departemen', [DepartemenController::class, 'index']);
    Route::post('/departemen/store', [DepartemenController::class, 'store']);
    Route::post('/departemen/edit', [DepartemenController::class, 'edit']);
    Route::post('/departemen/{kode_dept}/update', [DepartemenController::class, 'update']);
    Route::post('/departemen/{kode_dept}/delete', [DepartemenController::class, 'delete']);

    //presensi
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);
    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);
    Route::post('/tampilkanpeta', [PresensiController::class, 'tampilkanpeta']);
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);
    Route::get('/presensi/rekap', [PresensiController::class, 'rekap']);
    Route::post('/presensi/cetakrekap', [PresensiController::class, 'cetakrekap']);
    Route::get('/presensi/sakitcuti', [PresensiController::class, 'sakitcuti']);
    Route::post('/presensi/approvecuti', [PresensiController::class, 'approvecuti']);
    Route::get('/presensi/{kode_cuti}/batalkansakitcuti', [PresensiController::class, 'batalkansakitcuti']);

    //konfigurasi
    Route::get('/konfigurasi/lokasikantor', [KonfigurasiController::class, 'lokasikantor']);
    Route::post('/konfigurasi/updatelokasikantor', [KonfigurasiController::class, 'updatelokasikantor']);
    
    Route::get('/konfigurasi/jamkerja', [KonfigurasiController::class, 'jamkerja']);
    Route::post('/konfigurasi/storejamkerja', [KonfigurasiController::class, 'storejamkerja']);
    Route::post('/konfigurasi/editjamkerja', [KonfigurasiController::class, 'editjamkerja']);
    Route::post('/konfigurasi/updatejamkerja', [KonfigurasiController::class, 'updatejamkerja']);
    Route::post('/konfigurasi/{kode_jam_kerja}/delete', [KonfigurasiController::class, 'deletejamkerja']);
    
    Route::get('/konfigurasi/{nip}/setjamkerja', [KonfigurasiController::class, 'setjamkerja']);
    Route::post('/konfigurasi/storesetjamkerja', [KonfigurasiController::class, 'storesetjamkerja']);
    Route::post('/konfigurasi/updatesetjamkerja', [KonfigurasiController::class, 'updatesetjamkerja']);

    //cuti
    // Route::get('/cuti',[CutiController::class,'index']);
    Route::get('/cuti', 'App\Http\Controllers\CutiController@index')->name('cuti');
    Route::post('/cuti/store', 'App\Http\Controllers\CutiController@store')->name('cutistore');
    Route::post('/cuti/edit', 'App\Http\Controllers\CutiController@edit')->name('cutiedit');
    Route::post('/cuti/{kode_cutii}/update', 'App\Http\Controllers\CutiController@update')->name('cutiupdate');
    Route::post('/cuti/{kode_cutii}/delete', 'App\Http\Controllers\CutiController@delete')->name('cutidelete');
});
