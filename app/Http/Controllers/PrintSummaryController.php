<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrintSummaryController extends Controller
{
    public function print()
    {

        $pdf = Pdf::loadView('print-summary')
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true);

//        return $pdf->stream('invoice.pdf'); // To view in browser
        return $pdf->download('relatório.pdf'); // To force download
    }
}
