<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RegistroActividad extends Model
{
    protected $table = 'registro_actividad';

    // ← Esto le dice a Laravel que tu tabla no tiene created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'usuario_id', 'nombre', 'correo', 'rol',
        'accion', 'descripcion', 'ip',
    ];

    public static function log(string $accion, string $descripcion = ''): void
    {
        $user = Auth::user();
        self::create([
            'usuario_id'  => $user?->id,
            'nombre'      => $user?->nombre,
            'correo'      => $user?->correo,
            'rol'         => $user?->rol,
            'accion'      => $accion,
            'descripcion' => $descripcion,
            'ip'          => request()->ip(),
        ]);
    }
}