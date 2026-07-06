<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'propiedad_id',
        'tipo',
        'mensaje',
        'leida',
        'fecha_envio',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_envio' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class, 'propiedad_id');
    }

    public static function crearPara(?int $usuarioId, string $tipo, string $mensaje, ?int $propiedadId = null): void
    {
        if (!$usuarioId) {
            return;
        }

        self::create([
            'usuario_id' => $usuarioId,
            'propiedad_id' => $propiedadId,
            'tipo' => $tipo,
            'mensaje' => $mensaje,
        ]);
    }
}
