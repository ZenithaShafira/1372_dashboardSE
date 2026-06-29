<?php

namespace App\Http\Controllers;

use App\Models\PPL;
use App\Models\Monitoring;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UploadMonitoringController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        $header = fgetcsv($file);

        $data = [];

        while (($row = fgetcsv($file)) !== false) {

            $row = array_combine($header, $row);
            // dd($header);
            // dd($row);

            $email = trim($row['nama']);
            $role = strtoupper(trim($row['role']));
            $status = strtoupper(trim($row['status']));
            // dd($data[$email]);            

            $isPencacah = $role === 'PENCACAH';
            $isProgress = !in_array($status, ['OPEN','DRAFT']);
 
            if ($isPencacah && $isProgress) {
                if (!isset($data[$email])) {
                    $data[$email] = [
                        'total_progress' => 0,
                        'waktu_upload' => Carbon::createFromFormat(
                            'j/n/Y, H.i.s',
                            trim($row['tanggal_upload'])
                        ),
                    ];
                }
                $data[$email]['total_progress'] += (int) $row['jumlah'];
            }
        }

        fclose($file);

        // dd($data);

        foreach ($data as $email => $hasil) {

            $pencacah = PPL::where('email', $email)->first();
            // dd($pencacah);

            if (!$pencacah) {
                continue;
            }

            // dd($email, $hasil, $pencacah);

            Monitoring::create([
                'id_ppl' => $pencacah->id,
                'total_progress' => $hasil['total_progress'],
                'waktu_upload' => $hasil['waktu_upload'],
            ]);
        }

        return redirect()->back()->with(
            'success',
            'Data monitoring berhasil diimport'
        );
    }
}