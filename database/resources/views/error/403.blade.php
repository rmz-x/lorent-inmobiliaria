{{-- resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sin permiso — Lorent</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:Roboto,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f0f2f5;margin:0}
        .box{text-align:center;background:#fff;padding:40px 50px;border-radius:12px;border:1px solid #e2e6ea}
        h1{font-size:60px;color:#1a3a6b;margin:0}
        p{color:#6c757d;margin:10px 0 24px}
        a{background:#46A2FD;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-size:14px}
    </style>
</head>
<body>
    <div class="box">
        <h1>403</h1>
        <p>No tienes permiso para acceder a esta sección.</p>
        <a href="javascript:history.back()">← Volver</a>
    </div>
</body>
</html>
