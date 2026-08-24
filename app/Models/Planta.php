<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planta extends Model
{
    public function predio(){
        return $this->belongsTo(Predio::class);
    }

    public function sala(){
        return $this->belongsTo(Sala::class);
    }

    public function markers()
    {
        return $this->hasMany(PatchPanelSala::class, 'planta_id');
    }

    public function salas()
    {
        return $this->hasMany(Sala::class);
    }
}
