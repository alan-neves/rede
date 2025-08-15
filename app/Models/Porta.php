<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Porta extends Model
{
    use HasFactory;

    protected $fillable = [
        'porta', 'equipamento_id', 'tipo'
    ];
    
    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }
    
    public function snapshots()
    {
        return $this->hasMany(Snapshot::class);
    }
    
    public function latest_snapshot()
    {
        return $this->hasOne(Snapshot::class)->latest();
    }
    
    public function patchPanelsConectados()
    {
        return $this->belongsToMany(Porta::class, 'equipamento_patch_panel', 'equipamento_porta_id', 'patch_panel_porta_id')
            ->withPivot('user_id', 'created_at');
    }

    public function patchPanelPorta()
    {
        return $this->belongsToMany(Porta::class, 'equipamento_patch_panel', 'equipamento_porta_id', 'patch_panel_porta_id')
            ->withPivot('user_id', 'created_at');
    }
}