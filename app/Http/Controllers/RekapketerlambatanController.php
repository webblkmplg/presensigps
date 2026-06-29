<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapketerlambatanController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap request dari form filter, default ke tahun saat ini jika kosong
        $bulan_input = $request->bulan; // Bisa null jika tidak dipilih
        $tahun_input = $request->tahun ?? date('Y');

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // 1. Ambil data master semua pegawai (diurutkan berdasarkan abjad)
        $semua_pegawai = DB::table('pegawai')->orderBy('nama_lengkap', 'asc')->get();
        $rekap_keterlambatan = [];
        
        foreach ($semua_pegawai as $peg) {
            $rekap_keterlambatan[$peg->nip] = [
                'nip' => $peg->nip,
                'nama_lengkap' => $peg->nama_lengkap,
                'total_menit' => 0
            ];
        }

        // 2. Siapkan query presensi (Abaikan JK03 / Lembur / Sabtu-Minggu)
        // Optimasi: Hanya select kolom yang benar-benar dipakai untuk menghitung keterlambatan
        $query = DB::table('presensi')
            ->select('presensi.nip', 'presensi.jam_in', 'jam_kerja.jam_masuk')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('presensi.status', 'h')
            ->whereNotNull('presensi.jam_in')
            ->whereNotNull('jam_kerja.jam_masuk')
            ->where('presensi.kode_jam_kerja', '!=', 'JK03')
            ->whereRaw('YEAR(presensi.tgl_presensi)="' . $tahun_input . '"');

        // Jika bulan dipilih, tambahkan filter bulan ke dalam query
        if (!empty($bulan_input)) {
            $query->whereRaw('MONTH(presensi.tgl_presensi)="' . $bulan_input . '"');
        }

        $history_presensi = $query->get();

        // 3. Kalkulasi Menit Keterlambatan
        foreach($history_presensi as $d) {
            if ($d->jam_in > $d->jam_masuk) {
                $waktu_masuk = Carbon::parse($d->jam_masuk);
                $waktu_absen = Carbon::parse($d->jam_in);
                
                $menit_terlambat = $waktu_masuk->diffInMinutes($waktu_absen);

                if(isset($rekap_keterlambatan[$d->nip])) {
                    $rekap_keterlambatan[$d->nip]['total_menit'] += $menit_terlambat;
                }
            }
        }

        // 4. Ubah ke Collection dan urutkan descending (yang paling sering telat di nomor 1)
        $leaderboard_keterlambatan = collect($rekap_keterlambatan)
            ->sortByDesc('total_menit')
            ->values()
            ->all();
            
        // Mengarahkan ke file resources/views/presensi/rekapketerlambatan.blade.php
        return view('presensi.rekapketerlambatan', compact('leaderboard_keterlambatan', 'namabulan', 'bulan_input', 'tahun_input'));
    }
}