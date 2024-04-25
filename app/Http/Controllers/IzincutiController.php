<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IzincutiController extends Controller
{
    public function create()
    {
        $mastercuti = DB::table('master_cuti')->orderBy('kode_cutii')->get();
        return view('izincuti.create', compact('mastercuti'));
    }

    public function store(Request $request)
    {
        $nip = Auth::guard('pegawai')->user()->nip;
        $tgl_cuti_dari = $request->tgl_cuti_dari;
        $tgl_cuti_sampai = $request->tgl_cuti_sampai;
        $kode_cutii = $request->kode_cutii;
        $status = "c";
        $keterangan = $request->keterangan;

        $bulan = date("m",strtotime($tgl_cuti_dari));
        $tahun = date("Y",strtotime($tgl_cuti_dari));
        $thn = substr($tahun,2,2);

        $lastcuti = DB::table('pengajuan_cuti')
        ->whereRaw('MONTH(tgl_cuti_dari)="'.$bulan.'"')
        ->whereRaw('YEAR(tgl_cuti_dari)="'.$tahun.'"')
        ->orderBy('kode_cuti','desc')
        ->first();

        $lastkodecuti = $lastcuti!= null ? $lastcuti->kode_cuti : "";
        $format = "CU".$bulan.$thn;
        $kode_cuti = buatkode($lastkodecuti,$format,3);

        //Hitung Jumlah Hari yang Diajukan
        $jmlhari = hitunghari($tgl_cuti_dari,$tgl_cuti_sampai);

        //Cek jumlah maksimal cuti
        $cuti = DB::table('master_cuti')->where('kode_cutii', $kode_cutii)->first();

        $jmlmaxcuti = $cuti->jml_hari;

        //Cek Jumlah Cuti yang Sudah Digunakan pada Tahun Aktif
        $cutidigunakan = DB::table('presensi')
        ->whereRaw('YEAR(tgl_presensi)="'.$tahun.'"')
        ->where('status','c')
        ->where('nip', $nip)
        ->count();

        //sisa cuti
        $sisacuti = $jmlmaxcuti - $cutidigunakan;

        $data = [
            'kode_cuti' => $kode_cuti,
            'nip' => $nip,
            'tgl_cuti_dari' => $tgl_cuti_dari,
            'tgl_cuti_sampai' => $tgl_cuti_sampai,
            'kode_cutii' => $kode_cutii,
            'status' => $status,
            'keterangan' => $keterangan,
        ];

        $cekpresensi = DB::table('presensi')
        ->whereBetween('tgl_presensi',[$tgl_cuti_dari,$tgl_cuti_sampai])
        ->where('nip',$nip);

        $datapresensi = $cekpresensi->get();
        
        if($jmlhari> $sisacuti){
            return redirect('/presensi/cuti')->with(['error' => 'Jumlah Hari Melebihi Batas Maksimal Cuti dalam 1 Tahun, Sisa Cuti Anda Adalah '.$sisacuti.' Hari']);
        } else if($cekpresensi->count() > 0) {
            $blacklistdate = "";
            foreach($datapresensi as $d){
                $blacklistdate .= date('d-m-Y', strtotime($d->tgl_presensi)).",";
            }

            return redirect('/presensi/cuti')->with(['error' => 'Gagal! Tanggal ' . $blacklistdate .' sudah digunakan/ sudah dilakukan presensi. Silakan Ganti Tanggal Periode Pengajuan/Hubungi IT']);
        } else {

                $simpan = DB::table('pengajuan_cuti')->insert($data);

                if ($simpan) {
                    return redirect('/presensi/cuti')->with(['success' => 'Informasi Berhasil Dikirim']);
                } else {
                    return redirect('/presensi/cuti')->with(['error' => 'Informasi Gagal Dikirim']);
                }
            }
    }

    public function edit($kode_cuti)
    {
        $datacuti = DB::table('pengajuan_cuti')->where('kode_cuti',$kode_cuti)->first();
        $mastercuti = DB::table('master_cuti')->orderBy('kode_cutii')->get();
        return view('izincuti.edit', compact('mastercuti','datacuti'));
    }

    public function update($kode_cuti, Request $request)
    {
        $tgl_cuti_dari = $request->tgl_cuti_dari;
        $tgl_cuti_sampai = $request->tgl_cuti_sampai;
        $keterangan = $request->keterangan;
        $kode_cutii = $request->kode_cutii;

        try {
            $data = [
                'tgl_cuti_dari' => $tgl_cuti_dari,
                'tgl_cuti_sampai' => $tgl_cuti_sampai,
                'kode_cutii' => $kode_cutii,
                'keterangan' => $keterangan
            ];

            DB::table('pengajuan_cuti')->where('kode_cuti', $kode_cuti)->update($data);
            return redirect('/presensi/cuti')->with(['success' => 'Informasi Berhasil Diupdate']);
        } catch (\Exception $e) {
            return redirect('/presensi/cuti')->with(['error' => 'Informasi Gagal Diupdate']);
        }
    }
}
