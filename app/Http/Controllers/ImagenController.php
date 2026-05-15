<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ImagenController extends Controller
{
    public function subir(Request $request)
    {
        $path = Storage::disk('s3')->put('imagenes', $request->file('foto'));

        $url = Storage::disk('s3')->url($path);

        return response()->json([
            'path' => $path,
            'url' => $url
        ]);
    }
}
