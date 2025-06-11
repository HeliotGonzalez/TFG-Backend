<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\significado_propuesto;
use App\Models\Significado;
use Illuminate\Support\Facades\DB;
use App\Models\Palabra;



class SignificadoPropuestoController extends Controller
{
    public function getNewMeanings()
    {
        $meanings = significado_propuesto::with('user')->get();

        return response()->json($meanings, 200);
    }

    public function rejectMeaning($id){
        significado_propuesto::where('id', $id)->delete();
        return response()->json(200);
    }

    public function approveMeaning(Request $request)
    {
        $data = $request->validate([
            'id'                  => 'required|integer|exists:significados_propuestos,id',
            'palabra'             => 'required|string',
            'descripcion_antigua' => 'required|string',
            'descripcion_propuesta'=> 'required|string',
        ]);

        DB::transaction(function () use ($data) {
            $palabra = Palabra::where('nombre', $data['palabra'])->firstOrFail();

            $significado = $palabra->significado;

            if ($significado->descripcion !== $data['descripcion_antigua']) {
                return response()->json(['error' => 'El significado actual ya no coincide'], 409);
            }

            $significado->descripcion = $data['descripcion_propuesta'];
            $significado->save();

            significado_propuesto::where('id', $data['id'])->delete();
        });

        return response()->json(
            ['message' => 'Significado aprobado correctamente'],
            200
        );
    }
}
