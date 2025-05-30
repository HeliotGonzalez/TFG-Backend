<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Significado;
use App\Models\Video;
use App\Models\Amigo;
use App\Models\Palabra;
use App\Models\Reporte;
use App\Models\UserVideo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    function getVideos($descripcion, $userID)
    {
        $descripcion = urldecode($descripcion);
        
        $videos = Video::with('significado', 'user')
            ->with(['diccionario' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])
            // Contar los totales de likes y dislikes de la tabla de reacciones en la relación "userVideos"
            ->withCount([
                'userVideos as likes' => function ($query) {
                    $query->where('action', 'like');
                },
                'userVideos as dislikes' => function ($query) {
                    $query->where('action', 'dislike');
                }
            ])
            // Cargar la reacción del usuario actual
            ->with(['userVideos' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])
            ->whereHas('significado', function ($query) use ($descripcion) {
                $query->where('descripcion', $descripcion);
            })->with('significado.etiquetas')
            ->orderBy('likes', 'desc')
            ->whereNotIn('corregido', [1, 3, 5])
            ->get();

    
        // Procesar cada video para agregar campos adicionales antes de enviarlo al front
        $videos->map(function ($video) {
            // Indicar si el video está en el diccionario del usuario
            $video->inDictionary = $video->diccionario->isNotEmpty();

            // Determinar la reacción que hizo el usuario (si existe)
            $reaction = $video->userVideos->first();
            $video->myReaction = $reaction ? $reaction->action : null;

    
            // Eliminar relaciones innecesarias
            unset($video->diccionario);
            unset($video->userVideos);
            return $video;
        });
    
        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron videos'], 404);
        }
        
        return response()->json($videos);
    }

    function store(Request $request) {
        $data = $request->all();
        
        // Buscar el significado en base a la descripción recibida
        $significado = Significado::where('descripcion', $data['significado'])->first();

        $video = new Video();
        $video->significado_id = $significado->id;
        $video->user_id = $data['userID'];
        $video->url = $data['videoUrl'];
        $video->corregido = ($data['corregido'] == 'true') ? 1 : 0;
        $video->save();
    
        return response()->json(['message' => 'Video registrado correctamente'], 200);
    }    
        
    function videoLikes(Request $request) {
        $data = $request->input('data');
        $video = Video::where('id', $data['id'])->first();

        if ($video) {
            if ($this->hasAlreadyVoted($data, $video) == 1){
                return response()->json(['message' => 'Ya has dado like a este video'], 400);
            } else if ($this->hasAlreadyVoted($data, $video) == 2 && $data['action'] == 'like'){
                $video->likes = $data['likes'];
                $video->dislikes = $video->dislikes -1;
                $video->save();
                return response()->json(['message' => 'Like registrado'], 200);

            } else if ($this->hasAlreadyVoted($data, $video) == 2 && $data['action'] == 'dislike'){
                $video->likes =  $video->likes - 1;
                $video->dislikes = $data['dislikes'];
                $video->save();
                return response()->json(['message' => 'Like registrado'], 200);
            }

            $video->likes = $data['likes'];
            $video->dislikes = $data['dislikes'];
            $video->save();
            return response()->json(['message' => 'Like registrado'], 200);
        }

        return response()->json(['message' => 'Video no encontrado'], 404);
    }

    function hasAlreadyVoted($data, $video){
        $has_already_liked = UserVideo::where('user_id', $data['userID'])->where('video_id', $video->id)->first();
        if ($has_already_liked) {
            if ($has_already_liked->action == $data['action']) {
                return 1;
            } else {
                $has_already_liked->action = $data['action'];
                $has_already_liked->save();
                return 2;
            }
        } else {
            $userVideo = new UserVideo();
            $userVideo->user_id = $data['userID'];
            $userVideo->video_id = $video->id;
            $userVideo->action = $data['action'];
            $userVideo->save();
        }
    }

    function cancelMyAction(Request $request) {
        $data = $request->input('data');

        $deleted = UserVideo::where('user_id', $data['userID'])
            ->where('video_id', $data['videoID'])
            ->where('action', $data['action'])
            ->delete();
    
        if ($deleted > 0) {
            return response()->json(['message' => 'Acción cancelada exitosamente'], 200);
        } else {
            return response()->json(['message' => 'Video no encontrado'], 404);
        }
    }
    
    function reportVideo(Request $request) {
        $data = $request->all();
    
        $reporte = new Reporte();
        $reporte->video_id = $data['videoID'];
        $reporte->user_id = $data['userID'];
        $reporte->contenido = $data['reason'];
        $reporte->estado = false;
        $reporte->save();
    
        return response()->json(['message' => 'Reporte registrado correctamente'], 200);
    }

    function getRecentlyUploadedVideos($userID){
        $videos = Video::with('user')->latest('created_at')->limit(50)            
            ->with(['diccionario' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])->withCount([
                'userVideos as likes' => function ($query) {
                    $query->where('action', 'like');
                },
                'userVideos as dislikes' => function ($query) {
                    $query->where('action', 'dislike');
                }
            ])->with(['userVideos' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])->with('significado.etiquetas')->whereNotIn('corregido', [1, 3, 5])
            ->get();

        $videos->map(function ($video) {
            // Indicar si el video está en el diccionario del usuario
            $video->inDictionary = $video->diccionario->isNotEmpty();

            // Determinar la reacción que hizo el usuario (si existe)
            $reaction = $video->userVideos->first();
            $video->myReaction = $reaction ? $reaction->action : null;

    
            // Eliminar relaciones innecesarias
            unset($video->diccionario);
            unset($video->userVideos);
            return $video;
        });

        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron videos'], 404);
        }
        
        return response()->json($videos);
    }

    public function getVideosByThemes($userID, $tags)
    {
        // Aceptar tags como string o array y normalizar a array de strings
        if (is_string($tags)) {
            $tagsArray = array_filter(array_map('trim', explode(',', $tags)));
        } elseif (is_array($tags)) {
            $tagsArray = array_filter(array_map('trim', $tags));
        } else {
            $tagsArray = [];
        }

        // Normalizar etiquetas a minúsculas para comparación insensible
        $lowerTags = array_map('strtolower', $tagsArray);

        $videos = Video::with('user')
            ->withCount([
                'userVideos as likes' => function ($query) {
                    $query->where('action', 'like');
                },
                'userVideos as dislikes' => function ($query) {
                    $query->where('action', 'dislike');
                },
            ])
            ->with(['userVideos' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])
            ->with(['diccionario' => function ($query) use ($userID) {
                $query->where('user_id', $userID);
            }])->with('significado.etiquetas')
            ->whereHas('significado.etiquetas', function ($query) use ($lowerTags) {
                $query->whereIn(DB::raw('LOWER(nombre)'), $lowerTags);
            })->whereNotIn('corregido', [1, 3, 5])
            ->latest('created_at')
            ->limit(50)
            ->get();

        // Mapear datos adicionales y limpiar relaciones
        $videos->map(function ($video) {
            $video->inDictionary = $video->diccionario->isNotEmpty();

            $reaction = $video->userVideos->first();
            $video->myReaction = $reaction ? $reaction->action : null;

            unset($video->diccionario, $video->userVideos);
            return $video;
        });

        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron videos'], 404);
        }

        return response()->json($videos);
    }

    public function getVideosUncorrected($userID = null){
        $videos = Video::with('significado', 'user')
        ->withCount([
            'userVideos as likes' => function ($query) {
                $query->where('action', 'like');
            },
            'userVideos as dislikes' => function ($query) {
                $query->where('action', 'dislike');
            }
        ])->with('significado.etiquetas')->where('user_id', '!=', $userID)
        ->orderBy('likes', 'desc')
        ->where('corregido', 1)
        ->get();
    
        return $videos;    
    }

    public function getVideosCorrected($userID)
    {
        $videos = Video::where('user_id', $userID)->whereIn('corregido', [2, 3, 4, 5])->get();

        foreach ($videos as $video) {
            if ($video->corregido == 2) $video->corregido = 4;
            if ($video->corregido == 3) $video->corregido = 5;
            
            if ($video->isDirty('corregido')) {
                $video->save();
            }
        }

        return $videos;
    }

    public function correctVideo(Request $request){
        $data = $request->all();
        $video = Video::where('id', $data['videoId'])->first();

        if ($video) {
            $video->corregido = ($data['action'] == 'accept') ? 2 : 3;
            $video->comentario = $data['comment'];
            $video->save();

            Redis::publish('video-corrected', json_encode([
                'from' => $video->user_id,
                'to' => $video->user_id,
                'video_id' => $video->id,
                'user_id' => $video->user_id,
                'action' => $data['action'],
                'comment' => $data['comment']
            ]));

            return response()->json(['message' => 'Video corregido correctamente'], 200);
        }

        return response()->json(['message' => 'Video no encontrado'], 404);
    }

    public function getMyFriendsVideos(int $userID)
    {
        /** 1) IDs de amigos aceptados (en ambos sentidos) */
        $friendIds = Amigo::where('status', 'accepted')
            ->where(function ($q) use ($userID) {
                $q->where('user_id', $userID)
                ->orWhere('amigo_id', $userID);
            })
            // Elegimos “el otro” ID: si yo soy user_id, devuélveme amigo_id, y viceversa
            ->selectRaw("CASE WHEN user_id = ? THEN amigo_id ELSE user_id END AS amigo_id", [$userID])
            ->pluck('amigo_id')
            ->unique()
            ->values();

        if ($friendIds->isEmpty()) {
            return response()->json(['message' => 'No tienes amigos aceptados'], 404);
        }

        /** 2) Vídeos de esos amigos */
        $videos = Video::with('user', 'significado.etiquetas')
            // totales de likes / dislikes
            ->withCount([
                'userVideos as likes' => fn($q) => $q->where('action', 'like'),
                'userVideos as dislikes' => fn($q) => $q->where('action', 'dislike'),
            ])
            // mi reacción
            ->with(['userVideos' => fn($q) => $q->where('user_id', $userID)])
            // si está en mi diccionario
            ->with(['diccionario' => fn($q) => $q->where('user_id', $userID)])
            ->whereIn('user_id', $friendIds)
            ->whereNotIn('corregido', [1, 3, 5])
            ->latest('created_at')
            ->get();

        /** 3) Post-procesado para el front */
        $videos->map(function ($video) {
            $video->inDictionary = $video->diccionario->isNotEmpty();

            $reaction = $video->userVideos->first();
            $video->myReaction = $reaction ? $reaction->action : null;

            unset($video->diccionario, $video->userVideos);
            return $video;
        });

        if ($videos->isEmpty()) {
            return response()->json(['message' => 'Tus amigos aún no han subido vídeos'], 404);
        }

        return response()->json($videos);
    }

    public function getExpertStatData(){
        $videosPerUser = Video::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->with('user:id,name')
            ->get();

        $totalVideos = Video::count();

        $totalVideosLastMonth = $this->videosOfLastMonth();
        $totalWords = Palabra::count();

        $videosUncorrected = $this->getVideosUncorrected();
        $totalUncorrected  = $videosUncorrected->count();

        return [$videosPerUser, $totalVideos, $totalVideosLastMonth, $totalWords, $totalUncorrected];
    }

    // Sección de funciones privadas
    private function videosOfLastMonth(){
        $lastMonth       = Carbon::now()->subMonth();
        $yearOfLastMonth = $lastMonth->year;
        $monthOfLastMonth = $lastMonth->month;

        $totalVideosLastMonth = Video::whereYear('created_at', $yearOfLastMonth)->whereMonth('created_at', $monthOfLastMonth)->count();
        return $totalVideosLastMonth;
    }

    public function getUnseenVideosCorrected($userID){
        $videos = Video::whereIn('corregido', [2, 3])->where('user_id', $userID)->get();
        return $videos;
    }

}
