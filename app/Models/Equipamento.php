<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostname',
        'model',
        'ip',
        'poe_type',
        'qtde_portas',
        'patrimonio',
        'predio_id',
        'rack_id',
        'user_id' 
    ];

    public function portas()
    {
        return $this->hasMany(Porta::class);
    }

    public function predio()
    {
        return $this->belongsTo(Predio::class);
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    const model = [
        'hp_comware',
        'alcatel_aos',
    ];
}