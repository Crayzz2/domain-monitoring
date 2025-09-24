<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Domain;
use App\Models\Hosting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrintSummaryController extends Controller
{
    public function print()
    {
        $logo = Configuration::first()?->company_logo;
        $logoPath = $logo ? 'file://' . public_path('storage/' . $logo) : null;

        $pdf = Pdf::loadView('print-summary', ['logo' => $logoPath])
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->stream('invoice.pdf'); // To view in browser
//        return $pdf->download('relatório.pdf'); // To force download
    }
}
