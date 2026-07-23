<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PML;
use App\Models\Monitoring;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index() 
    {
        $all_pml = pml::all();

        $waktuUploadTerakhir = Carbon::parse(Monitoring::max('waktu_upload'));

        $ketUploadTerakhir = $waktuUploadTerakhir->translatedFormat('d F Y')
            . ' pukul ' .
            $waktuUploadTerakhir->format('H:i');

        $uploadTerakhir = Monitoring::select('id_ppl', DB::raw('MAX(waktu_upload) as waktu_upload'))
            ->groupBy('id_ppl');

        // dd($ketUploadTerakhir);

        $data = Monitoring::joinSub($uploadTerakhir, 'upload_terakhir', function ($join) {
            $join->on('monitoring.id_ppl', '=', 'upload_terakhir.id_ppl')
                ->on('monitoring.waktu_upload', '=', 'upload_terakhir.waktu_upload');
        })
        ->join('ppl', 'monitoring.id_ppl', '=', 'ppl.id')
        ->select(
            'ppl.id',
            'ppl.nama',
            // 'ppl.target',
            'monitoring.total_progress',
            // DB::raw('(monitoring.total_progress / ppl.target * 100) as persen')
        );

        $top10tinggi = (clone $data)
            ->orderByDesc('total_progress')
            ->take(10)
            ->get();
        
        $top10rendah = (clone $data)
            ->orderBy('total_progress') //ASC
            ->take(10)
            ->get();
        // dd($top10rendah);
        
        $allProgress = (clone $data)->sum('monitoring.total_progress');
        
        // dd($top10);

        
        // $bottom10 = (clone $data)
        //     ->orderBy('total_progress')
        //     ->take(10)
        //     ->get(); 

        // foreach ($pencacah as $p) {

        // }

        return view('dashboard', compact(
            'all_pml',
            'top10tinggi',
            'top10rendah',
            'allProgress',
            'ketUploadTerakhir'
        ));
    }
}
     