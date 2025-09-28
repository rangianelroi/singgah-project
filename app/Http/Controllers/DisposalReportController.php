<?php
namespace App\Http\Controllers;

use App\Models\DisposalRecord;
use Barryvdh\DomPDF\Facade\Pdf;

class DisposalReportController extends Controller
{
    public function download(DisposalRecord $record)
    {
        // Load relasi agar bisa diakses di view
        $record->load('item.passenger', 'authorizedBy');

        // Buat PDF dari view Blade
        $pdf = Pdf::loadView('reports.disposal', ['disposalRecord' => $record]);

        // Beri nama file dan unduh
        $fileName = 'berita-acara-pemusnahan-' . $record->item->id . '.pdf';
        return $pdf->download($fileName);
    }
}