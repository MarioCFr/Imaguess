<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación: un usuario tiene muchas puntuaciones
    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    // Posición global del usuario en el ranking
    public function globalRank(): int|string
    {
        $myBest = $this->scores()->max('points');
        if (!$myBest) return '—';

        $rank = User::whereHas('scores')
            ->get()
            ->filter(fn($u) => $u->scores()->max('points') > $myBest)
            ->count();

        return $rank + 1;
    }
}
