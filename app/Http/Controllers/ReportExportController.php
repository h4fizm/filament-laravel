<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PerformanceReview;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    public function exportPdf($id)
    {
        $review = PerformanceReview::with(['employee.contract'])->findOrFail($id);

        // Pastikan review_date dalam format tanggal
        $review->review_date = Carbon::parse($review->review_date)->format('d M Y');

        $pdf = Pdf::loadView('reports.export-pdf', compact('review'));

        return $pdf->download("report-{$review->employee->name}.pdf");
    }

}
