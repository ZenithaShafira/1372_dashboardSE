<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PML;
use App\Models\PPL;
use App\Models\Monitoring;
use Illuminate\Support\Carbon;

class MonitoringController extends Controller
{
    public function index()
    {
        $all_pml = pml::all();
        
        return view('dashboard', compact('all_pml'));
    }

    public function getDefaultData(Request $request, int $id_pml)
    {   
        // GET DEFAULT TABEL PML
        $getLatestUpload = Carbon::parse(
            Monitoring::max('waktu_upload')
        );

        // dd($getLatestUpload);

        $uploadTerakhir = $getLatestUpload->toDateString();
        // dd($uploadTerakhir);

        // $defaultTanggal = Carbon::parse($uploadTerakhir)
        //     ->subDay()
        //     ->toDateString();

        $tanggalFlatpickr = $getLatestUpload->copy()->subDay()->toDateString();

        $uploadSebelumnyaGet = Monitoring::whereDate('waktu_upload', '<', $uploadTerakhir)
            ->max('waktu_upload');

        $uploadSebelumnya = $uploadSebelumnyaGet
            ? Carbon::parse($uploadSebelumnyaGet)->toDateString()
            : null;

        $ketTanggalTerakhir = $getLatestUpload->translatedFormat('d F Y') 
            . ' pukul ' . $getLatestUpload->format('H:i');
        
        // dd($tanggalFlatpickr);
        
        $ketTanggalSebelumnya = $uploadSebelumnyaGet
            ? Carbon::parse($uploadSebelumnyaGet)->translatedFormat('d F Y')
                . ' pukul '
                . Carbon::parse($uploadSebelumnyaGet)->format('H:i')
            : null;

        $chart = [
            'labels' => [],
            'progress' => [],
            'total_progress' => [],
            'sisa_target' => [],
        ];

        $all_pml = PML::all();
        $pengawas = PML::findOrFail($id_pml);
        $pencacah = $pengawas->pencacah()
            ->orderBy('nama')
            ->get();

        // dd($pencacah);
        foreach ($pencacah as $p) {

            $snapshotAwal = Monitoring::where('id_ppl', $p->id)
                ->whereDate('waktu_upload', $uploadSebelumnya)
                ->latest('waktu_upload')
                ->first();

            $snapshotBaru = Monitoring::where('id_ppl', $p->id)
                ->whereDate('waktu_upload', $uploadTerakhir)
                ->latest('waktu_upload')
                ->first();
            
            $progress = ($snapshotBaru->total_progress ?? 0) - ($snapshotAwal->total_progress ?? 0);
            $total_progress = $snapshotBaru->total_progress ?? 0;
            // $target = $p->target;
            // $sisa_target = $target - $total_progress;

            $chart['labels'][] = $p->nama;
            $chart['progress'][] = $progress;
            $chart['total_progress'][] = $total_progress;
            // $chart['sisa_target'][] = $sisa_target;
        }

        // dd($chart);

        //GET DEFAULT TABEL MINGGUAN
        $pencacahSelected = $pencacah->first();

        $allTanggal = Monitoring::selectRaw('DATE(waktu_upload) as tanggal')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('tanggal')
            ->values();

        $periode = [];

        for ($i = 0; $i < count($allTanggal) - 1; $i++) {
            $periode[] = [
                'awal' => $allTanggal[$i],
                'akhir' => $allTanggal[$i + 1],
            ];
        }

        // Ambil maksimal 7 periode terakhir
        $periode = collect($periode)
            ->take(-7)
            ->values();
        
        $snapshots = Monitoring::where('id_ppl', $pencacahSelected->id)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->waktu_upload)->toDateString();
            });
        
        $chartMingguan = [
            'labels' => [],
            'progress' => [],
        ];

        foreach ($periode as $p) {

            $snapshotAwal = $snapshots[$p['awal']] ?? null;
            $snapshotAkhir = $snapshots[$p['akhir']] ?? null;

            $tanggalAwal = Carbon::parse($p['awal']);
            $tanggalAkhir = Carbon::parse($p['akhir']);

            // $chartMingguan['labels'][] =
            //     $tanggalAwal->translatedFormat('d M')
            //     . ' - ' .
            //     $tanggalAkhir->translatedFormat('d M');
            $chartMingguan['labels'][] = $tanggalAwal->translatedFormat('d M');

            $chartMingguan['progress'][] =
                ($snapshotAkhir->total_progress ?? 0) -
                ($snapshotAwal->total_progress ?? 0);
        }


        return view('monitoring_perPML', compact(
            'all_pml',
            'pengawas',
            'pencacah',
            'uploadTerakhir',
            'uploadSebelumnya',
            'ketTanggalTerakhir',
            'ketTanggalSebelumnya',
            'tanggalFlatpickr',
            'chart',
            'chartMingguan'
        ));
    }

    public function filterTanggalPML(Request $request, int $id_pml)
    {   
        $request->validate([
            'tanggal' => 'required|date|date_format:Y-m-d',
        ]);

        $tanggalInput = $request->query('tanggal');
            
        // Upload terakhir sebelum/sampai tanggal yang dipilih
        $tanggalAwal = Monitoring::whereDate('waktu_upload', '<=', $tanggalInput)
            ->selectRaw('DATE(waktu_upload) as tanggal')
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->value('tanggal');
        
        // Upload berikutnya setelah upload awal
        $tanggalSetelah = Monitoring::whereDate('waktu_upload', '>', $tanggalAwal)
            ->selectRaw('DATE(waktu_upload) as tanggal')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->value('tanggal');

        $uploadTerakhir = Monitoring::whereDate('waktu_upload', $tanggalSetelah)
            ->max('waktu_upload');
        
        $uploadSebelumnya = Monitoring::whereDate('waktu_upload', $tanggalAwal)
            ->max('waktu_upload');  
        
        $ketTanggalTerakhir = $uploadTerakhir
            ? Carbon::parse($uploadTerakhir)->translatedFormat('d F Y') .
                ' pukul ' .
                Carbon::parse($uploadTerakhir)->format('H:i')
            : null;

        $ketTanggalSebelumnya = $uploadSebelumnya
            ? Carbon::parse($uploadSebelumnya)->translatedFormat('d F Y') .
                ' pukul ' .
                Carbon::parse($uploadSebelumnya)->format('H:i')
            : null;
            
        $chart = [
            'labels' => [],
            'progress' => [],
            'total_progress' => [],
            'sisa_target' => [],
        ];

        // $all_pml = PML::all();
        $pengawas = PML::findOrFail($id_pml);
        $pencacah = $pengawas->pencacah()
            ->orderBy('nama')
            ->get();

        foreach ($pencacah as $p) {

            $snapshotAwal = Monitoring::where('id_ppl', $p->id)
                ->whereDate('waktu_upload', $tanggalAwal)
                ->latest('waktu_upload')
                ->first();

            $snapshotBaru = Monitoring::where('id_ppl', $p->id)
                ->whereDate('waktu_upload', $tanggalSetelah)
                ->latest('waktu_upload')
                ->first();
            
            $progress = ($snapshotBaru->total_progress ?? 0) - ($snapshotAwal->total_progress ?? 0);
            $total_progress = $snapshotBaru->total_progress;
            // $target = $p->target;
            // $sisa_target = $target - $total_progress;

            $chart['labels'][] = $p->nama;
            $chart['progress'][] = $progress;
            $chart['total_progress'][] = $total_progress;
            // $chart['sisa_target'][] = $sisa_target;
        }

        return response()->json([
            'labels' => $chart['labels'],
            'progress' => $chart['progress'],
            'total_progress' => $chart['total_progress'],
            // 'sisa_target' => $chart['sisa_target'],
            'keterangan' => 'Progress harian diperoleh dari data '
                . $ketTanggalTerakhir
                . ' dikurangi '
                . $ketTanggalSebelumnya,
            'tanggalTerakhir' => $tanggalSetelah, //ini buat tanggal total
        ]);
    }

    public function filterMingguan(Request $request){
        $request->validate([
            'id_ppl' => 'int',
        ]);

        $id_ppl = $request->query('id_ppl');

        $allTanggal = Monitoring::selectRaw('DATE(waktu_upload) as tanggal')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('tanggal')
            ->values();

        $periode = [];

        for ($i = 0; $i < count($allTanggal) - 1; $i++) {
            $periode[] = [
                'awal' => $allTanggal[$i],
                'akhir' => $allTanggal[$i + 1],
            ];
        }

        // Ambil maksimal 7 periode terakhir
        $periode = collect($periode)
            ->take(-7)
            ->values();
        
        $snapshots = Monitoring::where('id_ppl', $id_ppl)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->waktu_upload)->toDateString();
            });
        
        $chartMingguan = [
            'labels' => [],
            'progress' => [],
        ];

        foreach ($periode as $p) {

            $snapshotAwal = $snapshots[$p['awal']] ?? null;
            $snapshotAkhir = $snapshots[$p['akhir']] ?? null;

            $tanggalAwal = Carbon::parse($p['awal']);
            $tanggalAkhir = Carbon::parse($p['akhir']);

            // $chartMingguan['labels'][] =
            //     $tanggalAwal->translatedFormat('d M')
            //     . ' - ' .
            //     $tanggalAkhir->translatedFormat('d M');

            $chartMingguan['labels'][] = $tanggalAwal->translatedFormat('d M');

            $chartMingguan['progress'][] =
                ($snapshotAkhir->total_progress ?? 0) -
                ($snapshotAwal->total_progress ?? 0);
        }

        return response()->json([
            'labels' => $chartMingguan['labels'],
            'progress' => $chartMingguan['progress'],
        ]);

    }
}
