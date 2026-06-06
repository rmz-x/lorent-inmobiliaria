<?php
namespace App\Http\Controllers;

use App\Models\Propiedad;
use App\Models\Usuario;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PropiedadController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->esAdmin()) {
            $agentes     = Usuario::where('rol','agente')->orderBy('nombre')->get();
            $propiedades = Propiedad::with('agente')->orderBy('id','desc')->get();
            return view('admin.propiedades', compact('propiedades','agentes'));
        }
        $propiedades = Propiedad::where('agente_id',$user->id)->orderBy('id','desc')->get();
        return view('agente.propiedades', compact('propiedades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'      => 'required|min:5',
            'tipo'        => 'required|in:Venta,Alquiler,Anticretico',
            'zona'        => 'required',
            'precio'      => 'required|numeric|min:1',
            'area'        => 'required|numeric|min:1',
            'descripcion' => 'required|min:10',
            'estado'      => 'required|in:Disponible,Reservado,Vendido',
            'imagen'      => 'nullable|image|max:2048',
        ]);
        $data = $request->only(['titulo','tipo','zona','precio','area','descripcion','estado','agente_id']);
        if (Auth::user()->esAgente()) {
            $data['agente_id'] = Auth::id();
        }

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->uploadImageToDisks($request->file('imagen'));
        }

        $data['latitud']  = $request->latitud  ?: null;
        $data['longitud'] = $request->longitud ?: null;
        $prop = Propiedad::create($data);
        RegistroActividad::log('Propiedad registrada', "Se registró: \"{$prop->titulo}\" ({$prop->tipo}) en {$prop->zona}.");
        return back()->with('success','Propiedad registrada correctamente.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo'      => 'required|min:5',
            'tipo'        => 'required|in:Venta,Alquiler,Anticretico',
            'zona'        => 'required',
            'precio'      => 'required|numeric|min:1',
            'area'        => 'required|numeric|min:1',
            'descripcion' => 'required|min:10',
            'estado'      => 'required|in:Disponible,Reservado,Vendido',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $propiedad = Propiedad::findOrFail($id);
        $datos = $request->except('imagen');

        if ($request->hasFile('imagen')) {

            if ($propiedad->imagen) {
                $this->deleteImageFromDisks($propiedad->imagen);
            }

            $datos['imagen'] = $this->uploadImageToDisks($request->file('imagen'));
        }

        $propiedad->update($datos);
        RegistroActividad::log('Propiedad modificada', "Se modificó ID {$propiedad->id}: \"{$propiedad->titulo}\".");
        return back()->with('success','Propiedad actualizada correctamente.');
    }


    public function destroy(Propiedad $propiedad)
    {
        $titulo = $propiedad->titulo;

        if ($propiedad->imagen) {
            $this->deleteImageFromDisks($propiedad->imagen);
        }

        $propiedad->delete();
        RegistroActividad::log('Propiedad eliminada', "Se eliminó: \"$titulo\".");
        return back()->with('success','Propiedad eliminada correctamente.');
    }

    public function disponibles(Request $request)
    {
        $tipos_validos = ['Venta','Alquiler','Anticretico'];
        $tipo   = $request->query('tipo');
        $filtro = ($tipo && in_array($tipo, $tipos_validos)) ? $tipo : 'Todas';
        $query  = Propiedad::where('estado','Disponible');
        if ($filtro !== 'Todas') $query->where('tipo', $filtro);
        $propiedades = $query->orderBy('id','desc')->get();
        return view('cliente.propiedades', compact('propiedades','filtro'));
    }

    //buscar propiedades
    public function buscar(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Disponible');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');

        if (!in_array($tipo,   ['Todas','Venta','Alquiler','Anticretico'])) $tipo   = 'Todas';
        if (!in_array($estado, ['Disponible','Reservado','Todas']))          $estado = 'Disponible';

        $query = Propiedad::query();

        if ($estado !== 'Todas') $query->where('estado', $estado);
        if ($tipo   !== 'Todas') $query->where('tipo', $tipo);

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo',       'ilike', "%{$q}%")
                    ->orWhere('zona',        'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }

        if ($precioMax !== '' && is_numeric($precioMax)) {
            $query->where('precio', '<=', (float)$precioMax);
        }
        if ($areaMin !== '' && is_numeric($areaMin)) {
            $query->where('area', '>=', (float)$areaMin);
        }

        $propiedades      = $query->orderBy('id','desc')->get();
        $totalDisponibles = Propiedad::where('estado','Disponible')->count();

        return view('cliente.buscar', compact(
            'propiedades','q','tipo','estado','precioMax','areaMin','totalDisponibles'
        ));
    }

    public function detalle(Propiedad $propiedad)
    {
        $propiedad->load('agente');
        return view('cliente.detalle', compact('propiedad'));
    }

    //buscar propiedades para el administrador
    public function buscarAdmin(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Todas');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');
        $agenteId  = $request->query('agente_id', '');
 
        if (!in_array($tipo,   ['Todas','Venta','Alquiler','Anticretico'])) $tipo   = 'Todas';
        if (!in_array($estado, ['Disponible','Reservado','Vendido','Todas'])) $estado = 'Todas';
 
        // Admin ve TODAS las propiedades
        $query = Propiedad::with('agente');
 
        if ($estado !== 'Todas')        $query->where('estado', $estado);
        if ($tipo   !== 'Todas')        $query->where('tipo', $tipo);
        if ($agenteId !== '')           $query->where('agente_id', $agenteId);
 
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo',       'ilike', "%{$q}%")
                    ->orWhere('zona',        'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }
 
        if ($precioMax !== '' && is_numeric($precioMax)) {
            $query->where('precio', '<=', (float)$precioMax);
        }
        if ($areaMin !== '' && is_numeric($areaMin)) {
            $query->where('area', '>=', (float)$areaMin);
        }
 
        $propiedades      = $query->orderBy('id', 'desc')->get();
        $totalPropiedades = Propiedad::count();
        $agentes          = Usuario::where('rol', 'agente')->orderBy('nombre')->get();
 
        return view('admin.buscar', compact(
            'propiedades', 'q', 'tipo', 'estado',
            'precioMax', 'areaMin', 'agenteId',
            'totalPropiedades', 'agentes'
        ));
    }
 
    //bucar propiedades para el agente
    public function buscarAgente(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Todas');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');
 
        if (!in_array($tipo,   ['Todas','Venta','Alquiler','Anticretico'])) $tipo   = 'Todas';
        if (!in_array($estado, ['Disponible','Reservado','Vendido','Todas'])) $estado = 'Todas';
 
        // Agente solo ve SUS propiedades
        $query = Propiedad::where('agente_id', Auth::id());
 
        if ($estado !== 'Todas') $query->where('estado', $estado);
        if ($tipo   !== 'Todas') $query->where('tipo', $tipo);
 
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo',       'ilike', "%{$q}%")
                    ->orWhere('zona',        'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }
 
        if ($precioMax !== '' && is_numeric($precioMax)) {
            $query->where('precio', '<=', (float)$precioMax);
        }
        if ($areaMin !== '' && is_numeric($areaMin)) {
            $query->where('area', '>=', (float)$areaMin);
        }
 
        $propiedades      = $query->orderBy('id', 'desc')->get();
        $totalPropiedades = Propiedad::where('agente_id', Auth::id())->count();
 
        return view('agente.buscar', compact(
            'propiedades', 'q', 'tipo', 'estado',
            'precioMax', 'areaMin', 'totalPropiedades'
        ));
    }
 
    //buscar propiedades para el asistente
    public function buscarAsistente(Request $request)
    {
        $q         = trim($request->query('q', ''));
        $tipo      = $request->query('tipo', 'Todas');
        $estado    = $request->query('estado', 'Disponible');
        $precioMax = $request->query('precio_max', '');
        $areaMin   = $request->query('area_min', '');
 
        if (!in_array($tipo,   ['Todas','Venta','Alquiler','Anticretico'])) $tipo   = 'Todas';
        if (!in_array($estado, ['Disponible','Reservado','Todas']))          $estado = 'Disponible';
 
        // Asistente ve todas las propiedades (solo lectura)
        $query = Propiedad::with('agente');
 
        if ($estado !== 'Todas') $query->where('estado', $estado);
        if ($tipo   !== 'Todas') $query->where('tipo', $tipo);
 
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('titulo',       'ilike', "%{$q}%")
                    ->orWhere('zona',        'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }
 
        if ($precioMax !== '' && is_numeric($precioMax)) {
            $query->where('precio', '<=', (float)$precioMax);
        }
        if ($areaMin !== '' && is_numeric($areaMin)) {
            $query->where('area', '>=', (float)$areaMin);
        }
 
        $propiedades      = $query->orderBy('id', 'desc')->get();
        $totalPropiedades = Propiedad::count();
 
        return view('asistente.buscar', compact(
            'propiedades', 'q', 'tipo', 'estado',
            'precioMax', 'areaMin', 'totalPropiedades'
        ));
    }

    private function uploadImageToDisks($file): string
    {
        $fileName = $file->hashName();
        $path = 'propiedades/'.$fileName;

        if (config('filesystems.disks.s3.bucket')) {
            try {
                $success = Storage::disk('s3')->putFileAs('propiedades', $file, $fileName);
                if ($success) {
                    return $path;
                }
            } catch (\Throwable $e) {
                Log::error('S3 upload failed: '.$e->getMessage());
            }
        }

        return $file->storeAs('propiedades', $fileName, 'public');
    }

    private function deleteImageFromDisks(?string $imagen): void
    {
        if (!$imagen) {
            return;
        }

        if (preg_match('/^https?:\/\//i', $imagen)) {
            $path = ltrim(parse_url($imagen, PHP_URL_PATH) ?: '', '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, strlen('storage/'));
                Storage::disk('public')->delete($path);
            } else {
                try {
                    if (config('filesystems.disks.s3.bucket')) {
                        Storage::disk('s3')->delete($path);
                    }
                } catch (\Throwable $e) {
                    // Ignorar si S3 no está disponible.
                }
            }

            return;
        }

        Storage::disk('public')->delete($imagen);

        try {
            if (config('filesystems.disks.s3.bucket')) {
                Storage::disk('s3')->delete($imagen);
            }
        } catch (\Throwable $e) {
            // Ignorar si S3 no está disponible.
        }
    }
}