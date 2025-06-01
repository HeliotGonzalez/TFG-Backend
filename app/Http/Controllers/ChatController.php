<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    public function getMyConversations($userID){
        $chats = Chat::where('from', $userID)->orWhere('to', $userID)->orderBy('created_at')->get();
        return $chats;
    }

    public function sendMessage(Request $request){
        $data = $request->all();

        $chat = new Chat();
        $chat->from = $data['from'];
        $chat->to = $data['to'];
        $chat->message = $data['text'];
        $chat->save();


        $payload = json_encode([
            'type'    => 'chat',
            'from'    => $data['from'],     
            'to'      => $data['to'],
            'message' => $data['text'],
            'ts'      => $chat->created_at->getTimestampMs(),
        ]);

        /* canal exacto al que está suscrito el listener */
        Redis::publish('chat', $payload);

        return response()->json(['status' => 'ok']);
    }

    public function markChatAsRead(Request $request){
        $data = $request->all();
        
        Chat::where('from', $data['from'])->where('to', $data['to'])->where('read', false)->update(['read' => true]);

        return response()->json(['status' => 'ok']);
    }
}
