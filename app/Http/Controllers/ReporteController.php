<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;

class ReporteController extends Controller
{
    public const ESTADO_VISIBLE    = 0;
    public const ESTADO_NO_VISIBLE = 1;

    public function getAllReports(){
        $reportes = Reporte::with(['video', 'user'])->where('banned', false)->get()->groupBy('estado');

        return response()->json([
            'reportesVisibles'    => $reportes->get(self::ESTADO_VISIBLE)    ?? [],
            'reportesNoVisibles'  => $reportes->get(self::ESTADO_NO_VISIBLE) ?? [],
        ], 200);
    }

    public function hideReport(Request $request){
        $data = $request->all();
        Reporte::where('id', $data['reportID'])->update(['estado' => true]);

        return response()->json(['message' => 'Reporte ocultado correctamente']);
    }
}
