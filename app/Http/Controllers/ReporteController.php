<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;

class ReporteController extends Controller
{
    public function getAllReports(){
        $reportes = Reporte::with(['video', 'user'])->get();
        return response()->json($reportes);
    }
}
