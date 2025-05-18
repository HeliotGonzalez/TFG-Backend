<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Amigo;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AmigoController extends Controller
{

    public function getPendingFriendRequest($to){
        $amigos = Amigo::with('user')->where('amigo_id', $to)->where('status', 'pending')->get();
        return response()->json(['amigos' => $amigos], 200);
    }

    public function sendFriendRequest(Request $request){
        $data = $request->all();

        $amigoExiste = Amigo::where('user_id', $data['userID'])->where('amigo_id', $data['friendID'])->exists();
        if ($amigoExiste) return response()->json(['message' => 'Ya existe'], 200);


        $amigo = new Amigo();
        $amigo->user_id = $data['userID'];
        $amigo->amigo_id = $data['friendID'];
        $amigo->status = 'pending';
        $amigo->save();

        $payload = json_encode([
            'to'     => $request->friendID,
            'from'   => $request->userID,
            'status' => 'pending',
        ]);

        Redis::publish('friend-request', $payload);

        return response()->json(['status' => 'ok']);
    }

    public function amIBeingAddedByOwner($from, $to){
        $pending = Amigo::where('user_id', $from)->where('amigo_id', $to)->where('status', 'pending')->exists();
        return response()->json(['being_added' => $pending], 200);
    }

    public function isMyFriend($from, $to)
    {
        $accepted = Amigo::where('status', 'accepted')
            ->whereIn('user_id',   [$from, $to])
            ->whereIn('amigo_id', [$from, $to])
            ->exists();
    
        return response()->json(['accepted' => $accepted], 200);
    }

    public function acceptFriend(Request $request){
        $data = $request->all();
        $amigo = Amigo::where(function ($q) use ($data) {
            $q->where('user_id',  $data['from'])
                ->where('amigo_id', $data['to']);
            })
            ->orWhere(function ($q) use ($data) {
                $q->where('user_id',  $data['to'])
                ->where('amigo_id', $data['from']);
            })
            ->where('status', 'pending')
            ->first();
    
        if (! $amigo) {
            return response()->json(['status' => 'error', 'message' => 'Friend request not found or already handled.'], 404);
        }
    
        $amigo->status = 'accepted';
        $amigo->save();

        Redis::publish('friend-accepted', json_encode([
            'type'   => 'friend-accepted',
            'to'     => $data['from'],
            'from'   => $data['to'],
            'status' => 'accepted',
        ]));
        
        return response()->json(['status'  => 'success','message' => 'Friend request accepted.'], 200);
    }

    public function denyRequest(Request $request){
        $data = $request->all();

        $amigo = Amigo::where('user_id',  $data['from'])->where('amigo_id', $data['to'])->where('status', 'pending')->first();
    
        if (! $amigo) {
            return response()->json(['status' => 'error', 'message' => 'Friend request not found or already handled.'], 404);
        }
    
        $amigo->status = 'denied';
        $amigo->save();

        return response()->json(['status'  => 'success','message' => 'Friend request accepted.'], 200);
    }

    public function getNotFriendsUsers($userID){
        $sent = Amigo::where('user_id', $userID)->where('status', 'accepted')->pluck('amigo_id')->toArray();

        $received = Amigo::where('amigo_id', $userID)->where('status', 'accepted')->pluck('user_id')->toArray();

        $exclude = array_unique(array_merge($sent, $received, [$userID]));
        return User::whereNotIn('id', $exclude)->get();
    }
}
