<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CutisakitController extends Controller
{
    public function create()
    {
        return view('sakit.create');
    }

    public function store(Request $request)
    {
        $nip = Auth::guard('pegawai')->user()->nip;
        $tgl_cuti_dari = $request->tgl_cuti_dari;
        $tgl_cuti_sampai = $request->tgl_cuti_sampai;
        $status = "s";
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
        $format = "SA".$bulan.$thn;
        $kode_cuti = buatkode($lastkodecuti,$format,3);

        if ($request->hasFile('sid')) {
            $sid = $kode_cuti . "." . $request->file('sid')->getClientOriginalExtension();
        }else {
            $sid = null;
        }
        $data = [
            'kode_cuti' => $kode_cuti,
            'nip' => $nip,
            'tgl_cuti_dari' => $tgl_cuti_dari,
            'tgl_cuti_sampai' => $tgl_cuti_sampai,
            'status' => $status,
            'keterangan' => $keterangan,
            'doc_sid' => $sid
        ];

        $cekpresensi = DB::table('presensi')
        ->whereBetween('tgl_presensi',[$tgl_cuti_dari,$tgl_cuti_sampai])
        ->where('nip',$nip);

        $datapresensi = $cekpresensi->get();
        
        if($cekpresensi->count() > 0) {
            $blacklistdate = "";
            foreach($datapresensi as $d){
                $blacklistdate .= date('d-m-Y', strtotime($d->tgl_presensi)).",";
            }

            return redirect('/presensi/cuti')->with(['error' => 'Gagal! Tanggal ' . $blacklistdate .' sudah digunakan/ sudah dilakukan presensi. Silakan Ganti Tanggal Periode Pengajuan/Hubungi IT']);
        } else {

                $simpan = DB::table('pengajuan_cuti')->insert($data);

                if ($simpan) {

                    if ($request->hasFile('sid')) {
                        $sid = $kode_cuti . "." . $request->file('sid')->getClientOriginalExtension();
                        $folderPath = "public/uploads/sid/";
                        $request->file('sid')->storeAs($folderPath, $sid);
                    }   
                    return redirect('/presensi/cuti')->with(['success' => 'Informasi Berhasil Dikirim']);
                } else {
                    return redirect('/presensi/cuti')->with(['error' => 'Informasi Gagal Dikirim']);
                }
            }
    }

    public function edit($kode_cuti)
    {
        $datacuti = DB::table('pengajuan_cuti')->where('kode_cuti',$kode_cuti)->first();
        return view('sakit.edit', compact('datacuti'));
    }

    public function update($kode_cuti, Request $request)
    {
        $tgl_cuti_dari = $request->tgl_cuti_dari;
        $tgl_cuti_sampai = $request->tgl_cuti_sampai;
        $keterangan = $request->keterangan;

        if ($request->hasFile('sid')) {
            $sid = $kode_cuti . "." . $request->file('sid')->getClientOriginalExtension();
        }else {
            $sid = null;
        }
        $data = [
            'tgl_cuti_dari' => $tgl_cuti_dari,
            'tgl_cuti_sampai' => $tgl_cuti_sampai,
            'keterangan' => $keterangan,
            'doc_sid' => $sid
        ];

        try {
            DB::table('pengajuan_cuti')
            ->where('kode_cuti', $kode_cuti)
            ->update($data);
            if ($request->hasFile('sid')) {
                $sid = $kode_cuti . "." . $request->file('sid')->getClientOriginalExtension();
                $folderPath = "public/uploads/sid/";
                $request->file('sid')->storeAs($folderPath, $sid);
              }   
              return redirect('/presensi/cuti')->with(['success' => 'Informasi Berhasil Diupdate']);
        } catch (\Exception $e) {
            return redirect('/presensi/cuti')->with(['error' => 'Informasi Gagal Diupdate']);
        }
    }
}
