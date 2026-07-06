<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCliente extends Model
{
    protected $table = 'historial_cliente';
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'propiedad_id',
        'accion',
        'fecha_accion',
    ];

    protected $casts = [
        'fecha_accion' => 'datetime',
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
