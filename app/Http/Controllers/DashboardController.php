<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        
        // --- MULAI PENAMBAHAN KODE ---
        // $total_menit_terlambat = 0;

        // foreach ($historibulanini as $d) {
        //     // Cek apakah status hadir, dan data jam in & jam masuk tersedia
        //     if ($d->status == 'h' && !empty($d->jam_in) && !empty($d->jam_masuk)) {
        //         // Jika jam absen masuk lebih besar dari jam jadwal masuk
        //         if ($d->jam_in > $d->jam_masuk) {
        //             $waktu_masuk = Carbon::parse($d->jam_masuk);
        //             $waktu_absen = Carbon::parse($d->jam_in);
                    
        //             // Tambahkan selisih menitnya
        //             $total_menit_terlambat += $waktu_masuk->diffInMinutes($waktu_absen);
        //         }
        //     }
        // }

        $total_menit_terlambat = 0;

        foreach ($historibulanini as $d) {
            // TAMBAHAN: Cek kode_jam_kerja agar JK03 tidak dihitung
            if ($d->status == 'h' && !empty($d->jam_in) && !empty($d->jam_masuk) && $d->kode_jam_kerja != 'JK03') {
                
                if ($d->jam_in > $d->jam_masuk) {
                    // PERBAIKAN DI SINI: Cukup gunakan \Carbon\Carbon::parse atau Carbon::parse
                    $waktu_masuk = \Carbon\Carbon::parse($d->jam_masuk);
                    $waktu_absen = \Carbon\Carbon::parse($d->jam_in);
                    
                    $total_menit_terlambat += $waktu_masuk->diffInMinutes($waktu_absen);
                }
            }
        }
        
        // Konversi ke format Jam dan Menit yang lebih estetik dibaca
        $jam = floor($total_menit_terlambat / 60);
        $menit = $total_menit_terlambat % 60;

        if ($jam > 0) {
            $total_keterlambatan = $jam . ' Jam ' . $menit . ' Menit';
        } elseif ($menit > 0) {
            $total_keterlambatan = $menit . ' Menit';
        } else {
            $total_keterlambatan = '0 Menit (Tepat Waktu)';
        }
        // --- AKHIR PENAMBAHAN KODE ---
        
        // --- MULAI TAMBAHAN: HITUNG KETERLAMBATAN TAHUN BERJALAN ---
        $history_tahun_ini = \Illuminate\Support\Facades\DB::table('presensi')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('presensi.nip', $nip)
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"') // Filter 1 tahun penuh
            ->where('status', 'h')
            ->whereNotNull('jam_in')
            ->whereNotNull('jam_kerja.jam_masuk')
            ->where('presensi.kode_jam_kerja', '!=', 'JK03') // Abaikan lembur/akhir pekan
            ->get();

        $total_menit_terlambat_tahun = 0;

        foreach ($history_tahun_ini as $d) {
            if ($d->jam_in > $d->jam_masuk) {
                $waktu_masuk = \Carbon\Carbon::parse($d->jam_masuk);
                $waktu_absen = \Carbon\Carbon::parse($d->jam_in);
                
                $total_menit_terlambat_tahun += $waktu_masuk->diffInMinutes($waktu_absen);
            }
        }

        $jam_tahun = floor($total_menit_terlambat_tahun / 60);
        $menit_tahun = $total_menit_terlambat_tahun % 60;

        if ($jam_tahun > 0) {
            $total_keterlambatan_tahun = $jam_tahun . ' Jam ' . $menit_tahun . ' Menit';
        } elseif ($menit_tahun > 0) {
            $total_keterlambatan_tahun = $menit_tahun . ' Menit';
        } else {
            $total_keterlambatan_tahun = '0 Menit (Tepat Waktu)';
        }
        // --- AKHIR TAMBAHAN TAHUN BERJALAN ---

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


        return view('dashboard.dashboard', compact('presensihariini', 'historibulanini', 'namabulan', 'bulanini', 'tahunini', 'rekappresensi', 'leaderboard', 'total_keterlambatan', 'total_keterlambatan_tahun'));
    }

    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1; // Pastikan format bulan 1-12
        $tahunini = date("Y");
        
        // Rekap untuk indikator card hari ini
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
        
        // --- MULAI AKUMULASI KETERLAMBATAN BULAN INI ---
        // $history_sebulan = DB::table('presensi')
        //     ->join('pegawai', 'presensi.nip', '=', 'pegawai.nip')
        //     ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
        //     ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
        //     ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
        //     ->where('status', 'h') // Hanya yang hadir
        //     ->whereNotNull('jam_in')
        //     ->whereNotNull('jam_kerja.jam_masuk')
        //     ->whereRaw('jam_in > jam_kerja.jam_masuk') // Filter hanya yang masuknya lewat dari jadwal
        //     ->get();
        
        // --- MULAI AKUMULASI KETERLAMBATAN BULAN INI (SEMUA PEGAWAI) ---
        
        // 1. Ambil data master semua pegawai terlebih dahulu
        $semua_pegawai = DB::table('pegawai')->get();
        $rekap_keterlambatan = [];
        
        // 2. Buat array default untuk SEMUA pegawai dengan total_menit = 0
        foreach ($semua_pegawai as $peg) {
            $rekap_keterlambatan[$peg->nip] = [
                'nip' => $peg->nip,
                'nama_lengkap' => $peg->nama_lengkap,
                'total_menit' => 0
            ];
        }

        // 3. Ambil data presensi bulan ini (hadir, punya jam masuk, dan BUKAN JK03)
        $history_sebulan = DB::table('presensi')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->where('status', 'h')
            ->whereNotNull('jam_in')
            ->whereNotNull('jam_kerja.jam_masuk')
            ->where('presensi.kode_jam_kerja', '!=', 'JK03') // Abaikan lembur & akhir pekan
            ->get();

        // 4. Hitung dan tambahkan menit keterlambatan ke array pegawai
        foreach($history_sebulan as $d) {
            // Hanya hitung jika jam absen lebih besar dari jadwal jam masuk
            if ($d->jam_in > $d->jam_masuk) {
                $waktu_masuk = \Carbon\Carbon::parse($d->jam_masuk);
                $waktu_absen = \Carbon\Carbon::parse($d->jam_in);
                $menit_terlambat = $waktu_masuk->diffInMinutes($waktu_absen);

                // Pastikan NIP ada di master data sebelum ditambahkan
                if(isset($rekap_keterlambatan[$d->nip])) {
                    $rekap_keterlambatan[$d->nip]['total_menit'] += $menit_terlambat;
                }
            }
        }

        // 5. Ubah ke Laravel Collection dan urutkan descending (terlambat paling lama di atas)
        $leaderboard_keterlambatan = collect($rekap_keterlambatan)
            ->sortByDesc('total_menit')
            ->values()
            ->all();
            
        // --- AKHIR AKUMULASI ---

        

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('dashboard.dashboardadmin', compact('rekappresensi', 'leaderboard_keterlambatan', 'namabulan', 'bulanini', 'tahunini'));
    }
}
