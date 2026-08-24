<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planta extends Model
{
    public function predio(){
        return $this->belongsTo(Predio::class);
    }

    public function markers()
    {
        return $this->hasMany(PatchPanelSala::class, 'planta_id');
    }
}
