<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1; // 1 atau januari
        $tahunini = date("Y"); // 2023
        $nip = Auth::guard('pegawai')->user()->nip;
        $presensihariini = DB::table('presensi')->where('nip', $nip)->where('tgl_presensi', $hariini)->first();
        $historibulanini = DB::table('presensi')
            ->select('presensi.*','keterangan', 'jam_kerja.*', 'doc_sid','nama_cuti')
            ->leftJoin('jam_kerja','presensi.kode_jam_kerja','=','jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_cuti','presensi.kode_cuti','=','pengajuan_cuti.kode_cuti')
            ->leftJoin('master_cuti','pengajuan_cuti.kode_cutii','=','master_cuti.kode_cutii')
            ->where('presensi.nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->orderBy('tgl_presensi','desc')
            ->get();

        $rekappresensi = DB::table('presensi')
            ->selectRaw('
            SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="c",1,0)) as jmlcuti,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF(status="d",1,0)) as jmldl,
            SUM(IF(jam_in > "07:30",1,0)) as jmlterlambat
            ')
            ->where('nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->first();

        $leaderboard = DB::table('presensi')
            ->join('pegawai', 'presensi.nip', '=', 'pegawai.nip')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in')
            ->get();

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];


        return view('dashboard.dashboard', compact('presensihariini', 'historibulanini', 'namabulan', 'bulanini', 'tahunini', 'rekappresensi', 'leaderboard'));
    }

    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");
        
        $rekappresensi = DB::table('presensi')
        ->selectRaw('
        SUM(IF(status="h",1,0)) as jmlhadir,
        SUM(IF(status="c",1,0)) as jmlcuti,
        SUM(IF(status="s",1,0)) as jmlsakit,
        SUM(IF(status="d",1,0)) as jmldl,
        SUM(IF(jam_in > "07:30",1,0)) as jmlterlambat
        ')
        ->leftJoin('jam_kerja','presensi.kode_jam_kerja','=','jam_kerja.kode_jam_kerja')
        ->where('tgl_presensi',$hariini)
        ->first();

        return view('dashboard.dashboardadmin', compact('rekappresensi'));
    }
}
