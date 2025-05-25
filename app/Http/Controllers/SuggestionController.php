<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;

class SuggestionController extends Controller
{
    public function sendSuggestion(Request $request){
        $data = $request->all();
        
        $suggestion = new Suggestion();
        $suggestion->suggestion_text = $data['suggestion'];
        $suggestion->save();

        return response()->json(['status' => 'ok', 'message' => 'Suggestion sent successfully']);
    }
}
