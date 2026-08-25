<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    protected $fillable = [
        'nome',
        'predio_id',
        'user_id',
        'planta_id', 
        'x',
        'y',
        'fontsize',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class);
    }

    public function patchPanels()
    {
        return $this->belongsToMany(PatchPanel::class)
                ->using(PatchPanelSala::class) 
                ->withPivot('porta','user_id','tipo_porta_id')
                ->withTimestamps();
    }

    public function tipoPortas()
    {
        return $this->belongsToMany(TipoPorta::class, 'patch_panel_sala', 'sala_id', 'tipo_porta_id')
                ->withPivot('porta', 'patch_panel_id')
                ->withTimestamps();
    }

    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }
}
