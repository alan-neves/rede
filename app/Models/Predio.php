<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Predio extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'user_id',
    ];

    public function salas()
    {
        return $this->hasMany(Sala::class);
    }

    public function racks()
    {
        return $this->hasMany(Rack::class);
    }

    public function equipamentos()
    {
        return $this->hasMany(Equipamento::class);
    }

    public function plantas()
    {
        return $this->hasMany(Planta::class);
    }
}
