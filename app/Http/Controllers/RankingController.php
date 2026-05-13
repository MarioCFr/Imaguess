<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index()
    {
        // Subconsulta: mejor puntuación de cada usuario
        $bestPerUser = Score::select('user_id', DB::raw('MAX(points) as points'))
            ->groupBy('user_id');

        $topScores = Score::joinSub($bestPerUser, 'best', function ($join) {
                $join->on('scores.user_id', '=', 'best.user_id')
                     ->on('scores.points', '=', 'best.points');
            })
            ->with(['user' => fn($q) => $q->withCount('scores')])
            ->orderByDesc('scores.points')
            ->get()
            ->unique('user_id')
            ->values();

        return view('ranking', compact('topScores'));
    }
}
