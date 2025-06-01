<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;

class DailyChallengeController extends Controller
{
    public function checkLastDailyChallenge($userID){
        $user = User::where('id', $userID)->whereDate('last_challenge_made', today()->toDateString())->first();
        return $user ? response()->json(['message' => 'ok'], 200) : response()->json(['message' => 'Not ok'], 200);
    }

    public function sendResults(Request $request){  
        $data = $request->all();

        $user = User::where('id', $data['userID'])->first();
        if ($user) {
            $user->last_challenge_made = Carbon::now();
            $user->points += $data['correctAnswers'] * 10;
            $user->save();

            return response()->json(['message' => 'Results saved successfully'], 200);
        }
        return response()->json(['message' => 'User not found'], 404);
    }
}
