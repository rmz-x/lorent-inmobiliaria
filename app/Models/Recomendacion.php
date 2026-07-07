<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{
    protected $table = 'recomendaciones';

    protected $fillable = [
        'cliente_id',
        'propiedad_id',
        'puntuacion_recomendacion',
        'vista',
        'feedback',
        'fecha_recomendacion',
    ];

    protected $casts = [
        'vista' => 'boolean',
        'fecha_recomendacion' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'propiedad_id');
    }
}
