<?php
namespace App\Http\Controllers;

use App\Models\DisposalRecord;
use Barryvdh\DomPDF\Facade\Pdf;

class DisposalReportController extends Controller
{
    public function download(DisposalRecord $record)
    {
        // Load relasi
        $record->load('item.passenger', 'authorizedBy');

        // LOGIKA BARU: Cek metode pemusnahan untuk membedakan template PDF
        if ($record->disposal_method === 'handed_to_police') {
            // Jika diserahkan ke polisi, gunakan template BAST Polisi
            $view = 'reports.police_bast'; 
            $prefix = 'bast-kepolisian-';
        } else {
            // Jika dimusnahkan/lainnya, gunakan template Berita Acara Pemusnahan
            $view = 'reports.disposal';
            $prefix = 'berita-acara-pemusnahan-';
        }

        // Generate PDF
        $pdf = Pdf::loadView($view, ['disposalRecord' => $record]);
        
        // Setup ukuran kertas (opsional, biasanya F4/A4 untuk legal)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($prefix . $record->item->id . '.pdf');
    }
}