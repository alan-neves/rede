<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeloSwitch extends Model
{
    protected $fillable = [
        'nome',
        'fabricante',
        'qtde_portas',
        'qtde_portas_poe',
        'user_id',
    ];

    public function equipamentos()
    {
        return $this->hasMany(Equipamento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
