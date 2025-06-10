<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;

class SuggestionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);

        return response()->json(Suggestion::latest()->where('checked', false)->paginate($perPage), 200);
    }

    public function sendSuggestion(Request $request){
        $data = $request->all();
        
        $suggestion = new Suggestion();
        $suggestion->suggestion_text = $data['suggestion'];
        $suggestion->save();

        return response()->json(['status' => 'ok', 'message' => 'Suggestion sent successfully']);
    }

    public function hideSuggestion(Request $request){
        $data = $request->all();
        Suggestion::where('id', $data['id'])->update(['checked' => true]);

        return response()->json(['message' => 'Reporte ocultado correctamente']);
    }
}
