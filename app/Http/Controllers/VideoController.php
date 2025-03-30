<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Significado;
use App\Models\Video;
use App\Models\Palabra;
use App\Models\UserVideo;

class VideoController extends Controller
{
    function getVideos($descripcion) {
        $videos = Video::with('significado')
            ->whereHas('significado', function($query) use ($descripcion) {
                $query->where('descripcion', $descripcion);
            })->with('user')->orderBy('likes', 'desc')->get();
    
        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron videos'], 404);
        }
        
        return response()->json($videos);
    }
    
    function videoLikes(Request $request) {
        $data = $request->input('data');
        $video = Video::where('id', $data['id'])->first();

        if ($video) {
            if ($this->hasAlreadyVoted($data, $video) == 1){
                return response()->json(['message' => 'Ya has dado like a este video'], 400);
            } else if ($this->hasAlreadyVoted($data, $video) == 2 && $data['action'] == 'like'){
                $video->likes = $data['likes'];
                $video->dislikes -= 1;
                $video->save();
                return response()->json(['message' => 'Like registrado'], 200);

            } else if ($this->hasAlreadyVoted($data, $video) == 2 && $data['action'] == 'dislike'){
                $video->likes -= 1;
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
    
}
