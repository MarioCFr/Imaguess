<?php

namespace App\Http\Controllers;

use App\Models\Score;

class RankingController extends Controller
{
    public function index()
    {
        $topScores = Score::with('user')
            ->orderByDesc('points')
            ->get()
            ->unique('user_id')
            ->values();

        return view('ranking', compact('topScores'));
    }
}
