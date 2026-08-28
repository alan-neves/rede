<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPorta extends Model
{
    protected $fillable = [
        'nome',
        'user_id',
        'cor'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patchPanelSalas()
    {
        return $this->hasMany(PatchPanelSala::class);
    }
}
