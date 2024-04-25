<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CutiController extends Controller
{
    public function index()
    {
        $cuti = DB::table('master_cuti')->orderBy('kode_cutii','asc')->get();
        return view('cuti.index', compact('cuti'));
    }

    public function store(Request $request)
    {
        $kode_cutii = $request->kode_cutii;
        $nama_cuti = $request->nama_cuti;
        $jml_hari = $request->jml_hari;

        $cekcuti = DB::table('master_cuti')->where('kode_cutii', $kode_cutii)->count();
        if ($cekcuti > 0) {
            return Redirect::back()->with(['warning' => 'Data Kode Cuti Sudah Ada']);
        }

        try {
            DB::table('master_cuti')->insert([
                'kode_cutii' => $kode_cutii,
                'nama_cuti' => $nama_cuti,
                'jml_hari' => $jml_hari
            ]);
            return Redirect::back()->with(['success'=>'Data Berhasil Disimpan']);
        } catch (\Throwable $e) {
            return Redirect::back()->with(['warning'=>'Data Gagal Disimpan' . $e->getMessage()]);
        }
    }

    public function edit(Request $request){
        $kode_cutii = $request->kode_cutii;
        $cuti = DB::table('master_cuti')->where('kode_cutii',$kode_cutii)->first();
        return view('cuti.edit', compact('cuti'));
    }

    public function update(Request $request, $kode_cutii)
    {
        $nama_cuti = $request->nama_cuti;
        $jml_hari = $request->jml_hari;

        try {
            DB::table('master_cuti')->where('kode_cutii',$kode_cutii)
            ->update([
                'nama_cuti' => $nama_cuti,
                'jml_hari' => $jml_hari
            ]);
        
            return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
        } catch (\Throwable $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal di Update' .$e->getMessage()]);
        }
    }

    public function delete($kode_cutii)
    {
        try {
            DB::table('master_cuti')->where('kode_cutii',$kode_cutii)
            ->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Throwable $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal di Hapus' .$e->getMessage()]);
        }
    }
}
