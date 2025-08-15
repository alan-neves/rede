<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatchPanel extends Model
{
    protected $fillable = [
        'nome',
        'rack_id',
        'qtde_portas',
        'user_id',
    ];

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function salas()
    {
        return $this->belongsToMany(Sala::class)->withPivot(['porta', 'user_id']);
    }

    public function salasVinculadas()
    {
        return $this->belongsToMany(Sala::class, 'patch_panel_sala', 'patch_panel_id', 'sala_id')
                    ->withPivot('porta')
                    ->withTimestamps();
    }

    public function getVinculoPorPorta($porta)
    {
        return $this->salasVinculadas()
                ->wherePivot('porta', $porta)
                ->first();
    }

    public function portas()
    {
        return $this->hasMany(Porta::class);
    }
}
