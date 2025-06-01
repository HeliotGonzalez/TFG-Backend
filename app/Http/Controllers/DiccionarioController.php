<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\Diccionario;
use App\Models\Palabra;
use App\Models\DailyChallenge;



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
        $data = $request->input('data');

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

    function getPersonalDictionary($userID)
    {
        // Consulta desde el modelo Video, de forma similar a la función getVideos,
        // pero filtrando los videos que estén asociados al diccionario del usuario.
        $videos = Video::with('significado', 'user')
            ->withCount([
                // Contar likes y dislikes (desde la relación userVideos)
                'userVideos as likes' => function ($query) {
                    $query->where('action', 'like');
                },
                'userVideos as dislikes' => function ($query) {
                    $query->where('action', 'dislike');
                }
            ])
            ->with(['userVideos' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])
            ->with('significado.etiquetas')
            // Filtra únicamente los videos que tengan una entrada en el diccionario del usuario
            ->whereHas('diccionario', function ($query) use ($userID) {
                $query->where('user_id', $userID);
            })
            ->orderBy('likes', 'desc')
            ->get();
    
        // Procesa cada video para agregar propiedades adicionales y limpiar la respuesta
        $videos->map(function ($video) {
            // Indica si el video está presente en el diccionario
            $video->inDictionary = $video->diccionario->isNotEmpty();
            
            // Determina la reacción realizada por el usuario (si existe)
            $reaction = $video->userVideos->first();
            $video->myReaction = $reaction ? $reaction->action : null;
            // Obtener la palabra asociada al significado
            $palabra = Palabra::where('significado_id', $video->significado_id)->first();
            // Asignar el nombre de la palabra a la propiedad "palabra"
            $video->palabra = $palabra ? $palabra->nombre : 'Desconocido';
    
            // Elimina relaciones no necesarias en la respuesta final
            unset($video->diccionario);
            unset($video->userVideos);
            return $video;
        });
    
        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron videos'], 404);
        }
        
        return response()->json($videos);
    }

    function testYourself($userID){
        $videos = Video::with('significado', 'user')
            ->with(['userVideos' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])
            // Filtra únicamente los videos que tengan una entrada en el diccionario del usuario
            ->whereHas('diccionario', function ($query) use ($userID) {
                $query->where('user_id', $userID);
            })
            ->get();
    
        // Procesa cada video para agregar propiedades adicionales y limpiar la respuesta
        $videos->map(function ($video) {
            // Obtener la palabra asociada al significado
            $palabra = Palabra::where('significado_id', $video->significado_id)->first();
            // Asignar el nombre de la palabra a la propiedad "palabra"
            $video->palabra = $palabra ? $palabra->nombre : 'Desconocido';

            return $video;
        });
    
        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron videos'], 404);
        }
        
        return response()->json($videos);
    }

    public function getDailyChallenge(){
        $rows = DailyChallenge::with(['palabra', 'video'])->whereDate('created_at', Carbon::today())->get();

        if ($rows->isEmpty()) {
            return response()->json(['message' => 'No hay reto diario disponible'], 404);
        }

        $payload = $rows->map(fn ($row) => [
            'palabra'   => $row->palabra->nombre,
            'video_url' => $row->video->url,
            'video_id'  => $row->video_id 
        ]);

        return response()->json($payload);
    }
}
