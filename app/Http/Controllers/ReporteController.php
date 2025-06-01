<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;

class ReporteController extends Controller
{
    public function getAllReports(){
        $reportes = Reporte::with(['video', 'user'])->where('estado', 0)->get();
        return response()->json($reportes);
    }

    public function hideReport(Request $request){
        $data = $request->all();
        Reporte::where('id', $data['reportID'])->update(['estado' => true]);

        return response()->json(['message' => 'Reporte ocultado correctamente']);
    }
}
