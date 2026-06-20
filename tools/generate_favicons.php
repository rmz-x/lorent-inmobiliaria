<?php
// tools/generate_favicons.php
// Usage: php tools/generate_favicons.php public/icons/icons1.png
// Requires PHP with GD extension enabled.

if ($argc < 2) {
    echo "Uso: php tools/generate_favicons.php <ruta_al_png>\n";
    exit(1);
}

$input = $argv[1];
if (!file_exists($input)) {
    echo "Archivo no encontrado: $input\n";
    exit(1);
}

$info = getimagesize($input);
if (!$info) {
    echo "No es una imagen válida: $input\n";
    exit(1);
}

$mime = $info['mime'];
switch ($mime) {
    case 'image/png':
        $src = imagecreatefrompng($input);
        break;
    case 'image/jpeg':
        $src = imagecreatefromjpeg($input);
        break;
    case 'image/gif':
        $src = imagecreatefromgif($input);
        break;
    default:
        echo "Formato no soportado: $mime\n";
        exit(1);
}

$sizes = [180, 64, 32, 16];
$dir = dirname($input);
$base = pathinfo($input, PATHINFO_FILENAME);

foreach ($sizes as $size) {
    $dst = imagecreatetruecolor($size, $size);
    // preserve transparency
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);

    $w = imagesx($src);
    $h = imagesy($src);

    // calculate scale to fit
    $scale = min($size / $w, $size / $h);
    $nw = (int)($w * $scale);
    $nh = (int)($h * $scale);
    $dx = (int)(($size - $nw) / 2);
    $dy = (int)(($size - $nh) / 2);

    imagecopyresampled($dst, $src, $dx, $dy, 0, 0, $nw, $nh, $w, $h);

    $out = "$dir/{$base}-$size.png";
    imagepng($dst, $out, 9);
    imagedestroy($dst);
    echo "Generado: $out\n";
}

imagedestroy($src);

echo "Hecho. Para crear favicon.ico puedes usar un servicio online o instalar 'png-to-ico' en Node.\n";
