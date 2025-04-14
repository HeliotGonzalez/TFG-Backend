<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Palabra;
use App\Models\Significado;


class PalabraController extends Controller
{
    public function store(Request $request) {
        $nombre = $request->input('nombre');
        $descripcion = $request->input('descripcion');
        $etiquetas = $request->input('etiquetas');
    
        try {
            $significado = new Significado();
            $significado->descripcion = $descripcion;
            $significado->etiquetas = json_encode($etiquetas);
            $significado->save();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al registrar el significado'], 500);
        }

        try {
            $palabra = new Palabra();
            $palabra->significado_id = $significado->id;
            $palabra->nombre = $nombre;
            $palabra->estado = 0;
            $palabra->save();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al registrar la palabra'], 500);
        }
        
        return response()->json(['message' => 'Nueva palabra registrada'], 200);
    }

    public function getWords($letter){
        $words = Palabra::with('significado.highestVotedVideo')->where('nombre', 'like', $letter.'%')->get();
        return response()->json($words, 200);
    }
    
    function  getVideosByWord($word){
        $words = Palabra::with('significado.highestVotedVideo')->where('nombre', $word)->get();
        return response()->json($words, 200);
    }
}
