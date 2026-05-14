<?php
// app/Models/SolicitudVisita.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SolicitudVisita extends Model
{
    protected $table    = 'solicitudes_visita';
    public $timestamps  = false;

    protected $fillable = [
        'propiedad_id','cliente_id','fecha_solicitada','mensaje','estado'
    ];

    public function propiedad() {
        return $this->belongsTo(Propiedad::class, 'propiedad_id');
    }
    public function cliente() {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }
}