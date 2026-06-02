<?php
// app/Models/Propiedad.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Modelo de propiedad inmobiliaria.
 *
 * Representa una fila de la tabla `propiedades` y define
 * las relaciones con agente y solicitudes de visita.
 */
class Propiedad extends Model
{
    protected $table   = 'propiedades';
    public $timestamps = false;

    protected $fillable = [
        'titulo','tipo','zona','precio','area','descripcion',
        'estado','agente_id','imagen',
        'categoria_id','propietario_id',
        'habitaciones','banos','antiguedad',
        'latitud','longitud',
    ];

    public function agente() {
        return $this->belongsTo(Usuario::class, 'agente_id');
    }
    public function solicitudes() {
        return $this->hasMany(SolicitudVisita::class, 'propiedad_id');
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $this->imagen)) {
            return $this->imagen;
        }

        if (Storage::disk('public')->exists($this->imagen)) {
            return Storage::disk('public')->url($this->imagen);
        }

        try {
            if (config('filesystems.disks.s3.bucket') && Storage::disk('s3')->exists($this->imagen)) {
                return Storage::disk('s3')->url($this->imagen);
            }
        } catch (\Throwable $e) {
            // Si S3 no está disponible, continuar con la URL local.
        }

        return Storage::url($this->imagen);
    }
}