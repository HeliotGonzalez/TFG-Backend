<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\Diccionario;


class DiccionarioController extends Controller
{
    function storeVideoInDictionary(Request $request) {
        $data = $request->all();

        $video = Video::where('id', $data['videoID'])->first();
        $user = User::where('id', $data['userID'])->first();

        if ($video && $user) {
            $diccionario = new Diccionario();
            $diccionario->user_id = $user->id;
            $diccionario->video_id = $video->id;
            $diccionario->save();

            return response()->json(['message' => 'Video añadido correctamente'], 200);
        }

        return response()->json(['message' => 'Video o palabra no encontrado'], 404);
    }

    function deleteVideoFromDictionary(Request $request) {
        $data = $request->all();

        $video = Video::where('id', $data['videoID'])->first();
        $user = User::where('id', $data['userID'])->first();

        if ($video && $user) {
            Diccionario::where('user_id', $user->id)
                ->where('video_id', $video->id)
                ->delete();

            return response()->json(['message' => 'Video eliminado correctamente'], 200);
        }

        return response()->json(['message' => 'Video o palabra no encontrado'], 404);
    }
}
