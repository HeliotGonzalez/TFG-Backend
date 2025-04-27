<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Etiqueta;

class EtiquetaController extends Controller
{
    function get(){
        $etiquetas = Etiqueta::pluck('nombre');
        return $etiquetas;
    }
}
