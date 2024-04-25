<?php

namespace App\Http\Controllers;

use App\Models\Pengajuancuti;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
      public function gethari()
    {
        $hari = date("D");

        switch ($hari) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

            case 'Mon':
                $hari_ini = "Senin";
                break;

            case 'Tue':
                $hari_ini = "Selasa";
                break;

            case 'Wed':
                $hari_ini = "Rabu";
                break;

            case 'Thu':
                $hari_ini = "Kamis";
                break;

            case 'Fri':
                $hari_ini = "Jumat";
                break;

            case 'Sat':
                $hari_ini = "Sabtu";
                break;

            default:
                $hari_ini = "Tidak di Ketahui";
                break;
        }
        return $hari_ini;
    }

    public function create()
    {
        // $hariini = date("Y-m-d");
        echo $hariini = date("Y-m-d");
        $namahari = $this->gethari();
        $nip = Auth::guard('pegawai')->user()->nip;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nip', $nip)->count();
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        $jamkerja = DB::table('konfigurasi_jamkerja')
            ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nip', $nip)->where('hari', $namahari)->first();
        return view('presensi.create', compact('cek', 'lok_kantor', 'jamkerja'));
    }

    public function store(Request $request)
    {
        $nip = Auth::guard('pegawai')->user()->nip;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        $lok_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        $lok = explode(",", $lok_kantor->lokasi_kantor);
        $latitudekantor = $lok[0];
        $longitudekantor = $lok[1];
        // $latitudekantor = -2.9137672569993844;
        // $longitudekantor = 104.6950448322047;
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);
        $namahari = $this->gethari();
        $jamkerja = DB::table('konfigurasi_jamkerja')
            ->join('jam_kerja', 'konfigurasi_jamkerja.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nip', $nip)->where('hari', $namahari)->first();

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nip', $nip)->count();

        if ($cek > 0) {
            $ket = "out";
        } else {
            $ket = "in";
        }

        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nip . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($radius > $lok_kantor->radius) {
            echo "error|Maaf Anda Berada Di Luar Radius, Jarak Anda " . $radius . " Meter dari Kantor|radius";
        } else {
            if ($cek > 0) {
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $fileName,
                    'lokasi_out' => $lokasi
                ];
                $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nip', $nip)->update($data_pulang);
                if ($update) {
                    echo "success|Terimakasih, Hati-hati di Jalan|out";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Gagal Absen, Hubungi IT|out";
                }
            } else {
                if ($jam < $jamkerja->awal_jam_masuk) {
                    echo "error|Maaf Presensi dimulai pukul 07:00|in";
                } else {
                    $data = [
                        'nip' => $nip,
                        'tgl_presensi' => $tgl_presensi,
                        'jam_in' => $jam,
                        'foto_in' => $fileName,
                        'lokasi_in' => $lokasi,
                        'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                        'status' => 'h'
                    ];

                    $simpan = DB::table('presensi')->insert($data);
                    if ($simpan) {
                        echo "success|Terimakasih, Selamat Bekerja|in";
                        Storage::put($file, $image_base64);
                    } else {
                        echo "error|Maaf Gagal Absen, Hubungi IT|in";
                    }
                }
            }
        }
    }
    //Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile()
    {
        $nip = Auth::guard('pegawai')->user()->nip;
        $pegawai = DB::table('pegawai')->where('nip', $nip)->first();

        return view('presensi.editprofile', compact('pegawai'));
    }

    public function updateprofile(Request $request)
    {
        $nip = Auth::guard('pegawai')->user()->nip;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $pegawai = DB::table('pegawai')->where('nip', $nip)->first();
        if ($request->hasFile('foto')) {
            $foto = $nip . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $pegawai->foto;
        }
        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('pegawai')->where('nip', $nip)->update($data);
        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/pegawai/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['error' => 'Data Gagal di Update']);
        }
    }

    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nip = Auth::guard('pegawai')->user()->nip;

        $histori = DB::table('presensi')
            ->select('presensi.*','keterangan', 'jam_kerja.*', 'doc_sid','nama_cuti')
            ->leftJoin('jam_kerja','presensi.kode_jam_kerja','=','jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_cuti','presensi.kode_cuti','=','pengajuan_cuti.kode_cuti')
            ->leftJoin('master_cuti','pengajuan_cuti.kode_cutii','=','master_cuti.kode_cutii')
            ->where('presensi.nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi','desc')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }

    public function cuti(Request $request)
    {
        $nip = Auth::guard('pegawai')->user()->nip;

        if(!empty($request->bulan) && !empty($request->tahun))
        {
        $datacuti = DB::table('pengajuan_cuti')
        ->leftJoin('master_cuti','pengajuan_cuti.kode_cutii','=', 'master_cuti.kode_cutii')
        ->orderBy('tgl_cuti_dari', 'desc')
        ->where('nip', $nip)
        ->whereRaw('MONTH(tgl_cuti_dari)="'.$request->bulan.'"')
        ->whereRaw('YEAR(tgl_cuti_dari)="'.$request->tahun.'"')
        ->get();
        } else {
            $datacuti = DB::table('pengajuan_cuti')
            ->leftJoin('master_cuti','pengajuan_cuti.kode_cutii','=', 'master_cuti.kode_cutii')
            ->orderBy('tgl_cuti_dari', 'desc')
            ->where('nip', $nip)->limit(5)
            ->orderBy('tgl_cuti_dari', 'desc')
            ->get();
        }


        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.cuti', compact('datacuti','namabulan'));
    }

    public function buatcuti()
    {

        return view('presensi.buatcuti');
    }

    public function storecuti(Request $request)
    {
        $nip = Auth::guard('pegawai')->user()->nip;
        $tgl_cuti = $request->tgl_cuti;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nip' => $nip,
            'tgl_cuti' => $tgl_cuti,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_cuti')->insert($data);

        if ($simpan) {
            return redirect('/presensi/cuti')->with(['success' => 'Informasi Berhasil Dikirim']);
        } else {
            return redirect('/presensi/cuti')->with(['error' => 'Informasi Gagal Dikirim']);
        }
    }

    public function monitoring()
    {
        return view('presensi.monitoring');
    }

    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $presensi = DB::table('presensi')
            ->select('presensi.*', 'nama_lengkap', 'nama_dept','keterangan')
            ->leftjoin('pengajuan_cuti','presensi.kode_cuti','=','pengajuan_cuti.kode_cuti')
            ->join('pegawai', 'presensi.nip', '=', 'pegawai.nip')
            ->join('departemen', 'pegawai.kode_dept', '=', 'departemen.kode_dept')
            ->where('tgl_presensi', $tanggal)
            ->orderBy('jam_in')
            ->get();

        return view('presensi.getpresensi', compact('presensi'));
    }

    public function tampilkanpeta(Request $request)
    {
        $id = $request->id;
        $presensi = DB::table('presensi')->where('id', $id)
            ->join('pegawai', 'presensi.nip', '=', 'pegawai.nip')
            ->first();
        return view('presensi.showmap', compact('presensi'));
    }

    public function laporan()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $pegawai = DB::table('pegawai')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan', 'pegawai'));
    }

    public function cetaklaporan(Request $request)
    {
        $nip = $request->nip;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $pegawai = DB::table('pegawai')->where('nip', $nip)
            ->join('departemen', 'pegawai.kode_dept', '=', 'departemen.kode_dept')
            ->first();

        $presensi = DB::table('presensi')
            ->select('presensi.*','keterangan','jam_kerja.*')
            ->leftJoin('jam_kerja','presensi.kode_jam_kerja','=','jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_cuti','presensi.kode_cuti','=','pengajuan_cuti.kode_cuti')
            ->where('presensi.nip', $nip)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();
            
            if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");

            header("Content-type : application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Presensi Pegawai $time.xls");
            return view('presensi.cetaklaporanexcel', compact('bulan', 'tahun', 'namabulan', 'pegawai', 'presensi'));
        }
        
        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'pegawai', 'presensi'));
    }

    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.rekap', compact('namabulan'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
       
        $select_date = "";
        $field_date = "";
        $i = 1;
        while(strtotime($dari) <= strtotime($sampai)){
            $rangetanggal[] = $dari;

            $select_date .= "MAX(IF(tgl_presensi='$dari', CONCAT(
                IFNULL(jam_in, 'NA'),'|',
                IFNULL(jam_out, 'NA'),'|',
                IFNULL(presensi.status, 'NA'),'|',
                IFNULL(nama_jam_kerja, 'NA'),'|',
                IFNULL(jam_masuk, 'NA'),'|',
                IFNULL(jam_pulang, 'NA'),'|',
                IFNULL(presensi.kode_cuti, 'NA'),'|',
                IFNULL(keterangan, 'NA'),'|'
                ),NULL)) AS tgl_" . $i.",";

                $field_date .= "tgl_" . $i . ","; 
                $i++;
                $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
       
        }
        // dd($select_date);

        $jmlhari = count($rangetanggal);
        $lastrange = $jmlhari - 1;
        $sampai = $rangetanggal[$lastrange];
        if ($jmlhari == 30) {
            array_push($rangetanggal, NULL);
        }else if($jmlhari == 29) {
            array_push($rangetanggal, NULL, NULL);
        }else if($jmlhari == 28) {
            array_push($rangetanggal, NULL, NULL, NULL);
        }
        
        $query = Pegawai::query();
        $query->selectRaw("$field_date pegawai.nip, nama_lengkap, jabatan"
    );
        
        $query->leftJoin(
            DB::raw("(
                SELECT 
                $select_date
                presensi.nip
                FROM presensi
                    LEFT JOIN jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
                    LEFT JOIN pengajuan_cuti ON presensi.kode_cuti = pengajuan_cuti.kode_cuti
                    WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND '$sampai'
                    GROUP BY nip

            ) presensi"),
            function($join){
                $join->on('pegawai.nip','=','presensi.nip');
            }
        );

        $query->orderBy('nama_lengkap');
        $rekap = $query->get();

        // dd($rekap);
    
        if (isset($_POST['exportexcel'])) {
        $time = date("d-M-Y H:i:s");

        header("Content-type : application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Rekap Presensi Pegawai $time.xls");
        }

        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap','rangetanggal','jmlhari'));
    }

    public function sakitcuti(Request $request)
    {
        $query = Pengajuancuti::query();
        $query->select('kode_cuti', 'tgl_cuti_dari', 'tgl_cuti_sampai', 'pengajuan_cuti.nip', 'nama_lengkap', 'jabatan', 'status', 'status_approved', 'keterangan');
        $query->join('pegawai', 'pengajuan_cuti.nip', '=', 'pegawai.nip');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_cuti_dari', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nip)) {
            $query->where('pengajuan_cuti.nip', $request->nip);
        }
        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }

        if ($request->status_approved === '0' || $request->status_approved === '1' || $request->status_approved === '2') {
            $query->where('status_approved', $request->status_approved);
        }
        $query->orderBy('tgl_cuti_dari', 'desc');
        $sakitcuti = $query->paginate(10);
        $sakitcuti->appends($request->all());
        return view('presensi.sakitcuti', compact('sakitcuti'));
    }

    public function approvecuti(Request $request)
    {
        $status_approved = $request->status_approved;
        $kode_cuti = $request->kode_cuti_form;
        $datacuti = DB::table('pengajuan_cuti')->where('kode_cuti',$kode_cuti)->first();
        $nip = $datacuti->nip;
        $tgl_dari = $datacuti->tgl_cuti_dari;
        $tgl_sampai = $datacuti->tgl_cuti_sampai;
        $status = $datacuti->status;
        DB::beginTransaction();

        try {
            if($status_approved == 1)
            {
                while(strtotime($tgl_dari)<= strtotime($tgl_sampai))
                {
                    DB::table('presensi')->insert([
                        'nip' => $nip,
                        'tgl_presensi' => $tgl_dari,
                        'status' => $status,
                        'kode_cuti' => $kode_cuti
                    ]);
                    $tgl_dari = date("Y-m-d",strtotime("+1 days", strtotime($tgl_dari)));
                }
            }
        
            DB::table('pengajuan_cuti')->where('kode_cuti', $kode_cuti)->update(['status_approved' => $status_approved]);
           
            DB::commit();
           
            return Redirect::back()->with(['success'=>'Data Berhasil Diproses']);

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            return Redirect::back()->with(['warning'=>'Data Gagal Diproses']);
        }
    }

    public function batalkansakitcuti($kode_cuti)
    {
        DB::beginTransaction();
        try {
            $update = DB::table('pengajuan_cuti')->where('kode_cuti', $kode_cuti)->update([
                'status_approved' => 0
            ]);
            DB::table('presensi')->where('kode_cuti',$kode_cuti)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil di Batalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal di Batalkan']);
        }
        
    }

    public function cekpengajuancuti(Request $request)
    {
        $tgl_cuti = $request->tgl_cuti;
        $nip = Auth::guard('pegawai')->user()->nip;

        $cek = DB::table('pengajuan_cuti')->where('nip', $nip)->where('tgl_cuti', $tgl_cuti)->count();
        return $cek;
    }

    public function showact($kode_cuti)
    {
        $datacuti = DB::table('pengajuan_cuti')->where('kode_cuti',$kode_cuti)->first();
        return view('presensi.showact', compact('datacuti'));
    }

    public function deletecuti($kode_cuti)
    {
        $cekdatacuti = DB::table('pengajuan_cuti')->where('kode_cuti',$kode_cuti)->first();
        $doc_sid = $cekdatacuti->doc_sid;

        try {
            DB::table('pengajuan_cuti')->where('kode_cuti',$kode_cuti)->delete();
            // dd($doc_sid);
            if ($doc_sid != null)
            {
                Storage::delete('/public/uploads/sid/' . $doc_sid);
            }
            return Redirect('presensi/cuti')->with(['success' => 'Data Berhasil di Hapus']);
        } catch (\Exception $e) {
            return Redirect('presensi/cuti')->with(['error' => 'Data Gagal di Hapus']);
        }
    }
}
