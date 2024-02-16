<?php

namespace App\Http\Controllers;

use App\Models\Pengajuancuti;
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
                        'kode_jam_kerja' => $jamkerja->kode_jam_kerja
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
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->where('nip', $nip)
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }

    public function cuti()
    {
        $nip = Auth::guard('pegawai')->user()->nip;
        $datacuti = DB::table('pengajuan_cuti')->where('nip', $nip)->orderBy('tgl_cuti', 'desc')->get();
        return view('presensi.cuti', compact('datacuti'));
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
            ->select('presensi.*', 'nama_lengkap', 'nama_dept')
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
            ->where('nip', $nip)
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
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $rekap = DB::table('presensi')
            ->selectRaw('presensi.nip,nama_lengkap,
        MAX(IF(DAY(tgl_presensi) = 1,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_1,
        MAX(IF(DAY(tgl_presensi) = 2,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_2,
        MAX(IF(DAY(tgl_presensi) = 3,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_3,
        MAX(IF(DAY(tgl_presensi) = 4,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_4,
        MAX(IF(DAY(tgl_presensi) = 5,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_5,
        MAX(IF(DAY(tgl_presensi) = 6,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_6,
        MAX(IF(DAY(tgl_presensi) = 7,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_7,
        MAX(IF(DAY(tgl_presensi) = 8,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_8,
        MAX(IF(DAY(tgl_presensi) = 9,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_9,
        MAX(IF(DAY(tgl_presensi) = 10,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_10,
        MAX(IF(DAY(tgl_presensi) = 11,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_11,
        MAX(IF(DAY(tgl_presensi) = 12,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_12,
        MAX(IF(DAY(tgl_presensi) = 13,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_13,
        MAX(IF(DAY(tgl_presensi) = 14,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_14,
        MAX(IF(DAY(tgl_presensi) = 15,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_15,
        MAX(IF(DAY(tgl_presensi) = 16,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_16,
        MAX(IF(DAY(tgl_presensi) = 17,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_17,
        MAX(IF(DAY(tgl_presensi) = 18,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_18,
        MAX(IF(DAY(tgl_presensi) = 19,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_19,
        MAX(IF(DAY(tgl_presensi) = 20,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_20,
        MAX(IF(DAY(tgl_presensi) = 21,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_21,
        MAX(IF(DAY(tgl_presensi) = 22,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_22,
        MAX(IF(DAY(tgl_presensi) = 23,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_23,
        MAX(IF(DAY(tgl_presensi) = 24,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_24,
        MAX(IF(DAY(tgl_presensi) = 25,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_25,
        MAX(IF(DAY(tgl_presensi) = 26,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_26,
        MAX(IF(DAY(tgl_presensi) = 2,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_2,
        MAX(IF(DAY(tgl_presensi) = 27,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_27,
        MAX(IF(DAY(tgl_presensi) = 28,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_28,
        MAX(IF(DAY(tgl_presensi) = 29,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_29,
        MAX(IF(DAY(tgl_presensi) = 30,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_30,
        MAX(IF(DAY(tgl_presensi) = 31,CONCAT(jam_in,"-", IFNULL(jam_out,"00:00:00")),"")) AS tgl_31')
            ->join('pegawai', 'presensi.nip', '=', 'pegawai.nip')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->groupByRaw('presensi.nip,nama_lengkap')
            ->get();

        if (isset($_POST['exportexcel'])) {
        $time = date("d-M-Y H:i:s");

        header("Content-type : application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Rekap Presensi Pegawai $time.xls");
        }

        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap'));
    }

    public function sakitcuti(Request $request)
    {
        $query = Pengajuancuti::query();
        $query->select('id', 'tgl_cuti', 'pengajuan_cuti.nip', 'nama_lengkap', 'jabatan', 'status', 'status_approved', 'keterangan');
        $query->join('pegawai', 'pengajuan_cuti.nip', '=', 'pegawai.nip');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_cuti', [$request->dari, $request->sampai]);
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
        $query->orderBy('tgl_cuti', 'desc');
        $sakitcuti = $query->paginate(10);
        $sakitcuti->appends($request->all());
        return view('presensi.sakitcuti', compact('sakitcuti'));
    }

    public function approvecuti(Request $request)
    {
        $status_approved = $request->status_approved;
        $id_sakitcuti_form = $request->id_sakitcuti_form;
        $update = DB::table('pengajuan_cuti')->where('id', $id_sakitcuti_form)->update([
            'status_approved' => $status_approved
        ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
        }
    }

    public function batalkansakitcuti($id)
    {
        $update = DB::table('pengajuan_cuti')->where('id', $id)->update([
            'status_approved' => 0
        ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
        }
    }

    public function cekpengajuancuti(Request $request)
    {
        $tgl_cuti = $request->tgl_cuti;
        $nip = Auth::guard('pegawai')->user()->nip;

        $cek = DB::table('pengajuan_cuti')->where('nip', $nip)->where('tgl_cuti', $tgl_cuti)->count();
        return $cek;
    }
}
