<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PatchPanelSala extends Pivot
{
    protected $table = 'patch_panel_sala';

    protected $fillable = [
        'patch_panel_id',
        'sala_id',
        'porta',
        'user_id',
        'tipo_porta_id'
    ];

    public function tipoPorta()
    {
        return $this->belongsTo(TipoPorta::class, 'tipo_porta_id');
    }

    public function patchPanel()
    {
        return $this->belongsTo(PatchPanel::class);
    }

    public function sala()
    {
        return $this->belongsTo(Sala::class);
    }
}