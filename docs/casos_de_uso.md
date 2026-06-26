# Casos de uso - Mapeo Backend / Frontend

---

## CU1 — Iniciar sesión
- Descripción: Permitir acceso seguro al sistema a usuarios autorizados.
- Backend:
  - Controlador: [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php)
  - Método: `login()`
  - Ruta: `POST /login` → definida en [routes/web.php](routes/web.php)
- Frontend:
  - Vista: [resources/views/auth/login.blade.php](resources/views/auth/login.blade.php)
  - Assets: `public/js/auth/login.js`, `public/css/auth/login.css`
- Comentario: Flujo crítico; validar mensajes de error y bloqueo tras múltiples intentos.
-- Backend:
  - Controlador: [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php)
  - Método: `login()`
  - Ruta: `POST /login` → definida en [routes/web.php](routes/web.php)
-- Frontend:
  - Vista: [resources/views/auth/login.blade.php](resources/views/auth/login.blade.php)
  - Assets: `public/js/auth/login.js`, `public/css/auth/login.css`
-- Comentario: Flujo crítico; validar mensajes de error y bloqueo tras múltiples intentos.

---

## CU2 — Iniciar sesión
- Comentario: Permite acceso y redirección por rol; importante manejo de errores.
- Backend:
  - Controlador: [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php)
  - Método: `login()`
  - Ruta: `POST /login` → definida en [routes/web.php](routes/web.php)
- Frontend:
  - Vista: [resources/views/auth/login.blade.php](resources/views/auth/login.blade.php)
  - Assets: `public/js/auth/login.js`, `public/css/auth/login.css` (referenciados desde la vista)

---

## CU3 — Cerrar sesión
- Comentario: Finaliza sesión y registra actividad; invalida sesión y token.
- Backend:
  - Controlador: [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php)
  - Método: `logout()`
  - Ruta: `POST /logout` → [routes/web.php](routes/web.php)
- Frontend:
  - No hay vista dedicada; el logout se invoca por formulario/JS desde la interfaz (cabecera/layout): [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php)

---

## CU4 — Recuperar contraseña
- Comentario: Flujo de recuperación con token; verificar expiración y seguridad.
- Backend:
  - Controlador: [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php)
  - Métodos: `showForgotPassword()`, `sendForgotPassword()`, `showResetPassword()`, `resetPassword()`
  - Rutas: `/forgot-password`, `/reset-password/{token}` en [routes/web.php](routes/web.php)
- Frontend:
  - Vistas: [resources/views/auth/forgot-password.blade.php](resources/views/auth/forgot-password.blade.php), [resources/views/auth/reset-password.blade.php](resources/views/auth/reset-password.blade.php)

---

## CU5 — Gestionar usuarios
- Comentario: CRUD de usuarios; validar roles y restricciones (no autodestrucción).
- Backend:
  - Controlador: [app/Http/Controllers/UsuarioController.php](app/Http/Controllers/UsuarioController.php)
  - Métodos: `index()`, `store()`, `update()`, `destroy()`, `perfil()`, `actualizarPerfil()`
  - Rutas: prefijo `admin` → `admin/usuarios` (ver [routes/web.php](routes/web.php))
  - Modelo: [app/Models/Usuario.php](app/Models/Usuario.php)
- Frontend:
  - Vistas: [resources/views/admin/usuarios.blade.php](resources/views/admin/usuarios.blade.php)

---

## CU6 — Registrar propiedad
- Comentario: Creación con validación y subida de imágenes; uso de geolocalización opcional.
- Backend:
  - Controlador: [app/Http/Controllers/PropiedadController.php](app/Http/Controllers/PropiedadController.php)
  - Método: `store()`
  - Ruta: `POST /admin/propiedades` or `POST /agente/propiedades` (según rol) — ver [routes/web.php](routes/web.php)
  - Modelo: [app/Models/Propiedad.php](app/Models/Propiedad.php)
- Frontend:
  - Vistas de formulario: [resources/views/admin/propiedades.blade.php](resources/views/admin/propiedades.blade.php) (y [resources/views/agente/propiedades.blade.php](resources/views/agente/propiedades.blade.php) para agentes)

---

## CU7 — Eliminar propiedad
- Comentario: Acción sensible; confirmar y registrar actividad antes de eliminar.
- Backend:
  - Controlador: [app/Http/Controllers/PropiedadController.php](app/Http/Controllers/PropiedadController.php)
  - Método: `destroy()`
  - Ruta: `DELETE /admin/propiedades/{propiedad}` or `DELETE /agente/propiedades/{propiedad}` → [routes/web.php](routes/web.php)
- Frontend:
  - Botón/acción en las vistas de lista: [resources/views/admin/propiedades.blade.php](resources/views/admin/propiedades.blade.php), [resources/views/agente/propiedades.blade.php](resources/views/agente/propiedades.blade.php)

---

## CU8 — Cambiar estado de propiedad / Filtrar/Buscar (agrupado)
- Comentario: Operaciones combinadas de estado y búsqueda; importantes filtros y permisos.
- Backend:
  - Controlador: [app/Http/Controllers/PropiedadController.php](app/Http/Controllers/PropiedadController.php)
  - Métodos relevantes: `update()` (estado), `buscar()`, `buscarAdmin()`, `buscarAgente()`, `buscarAsistente()`, `disponibles()`
  - Rutas: varias en [routes/web.php](routes/web.php) (admin/agente/cliente/asistente)
- Frontend:
  - Vistas de búsqueda/filtrado: [resources/views/cliente/buscar.blade.php](resources/views/cliente/buscar.blade.php), [resources/views/admin/buscar.blade.php](resources/views/admin/buscar.blade.php), [resources/views/agente/buscar.blade.php](resources/views/agente/buscar.blade.php)

---

## CU9 — Buscar propiedades
- Comentario: Búsqueda indexada por múltiples parámetros; optimizar consultas.
- (Ver CU8) — `PropiedadController::buscar()` y vistas `cliente/buscar.blade.php`, `admin/buscar.blade.php`, `agente/buscar.blade.php`.

---

## CU10 — Filtrar propiedades
- Comentario: Filtrado con query strings; mantener parámetros idempotentes en URLs.
- Backend:
  - `PropiedadController::buscar*` (manejando query strings: `q`, `tipo`, `estado`, `precio_max`, `area_min`)
- Frontend:
  - Filtros en las vistas de listado: [resources/views/cliente/propiedades.blade.php](resources/views/cliente/propiedades.blade.php), [resources/views/admin/buscar.blade.php](resources/views/admin/buscar.blade.php)

---

## CU11 — Ver detalle de propiedad
- Comentario: Presentación enriquecida (imágenes, mapa); cachear consultas pesadas.
- Backend:
  - Controlador: [app/Http/Controllers/PropiedadController.php](app/Http/Controllers/PropiedadController.php)
  - Método: `detalle(Propiedad $propiedad)`
  - Ruta: `GET /cliente/propiedades/{propiedad}` → [routes/web.php](routes/web.php)
- Frontend:
  - Vista: [resources/views/cliente/detalle.blade.php](resources/views/cliente/detalle.blade.php)
  - Mapa/geolocalización: el `detalle` incluye Leaflet map (ver dentro de la vista)

---

## CU12 — Registrar cliente interesado
- Comentario: Registro rápido de prospecto; vincular con propiedad y agente.
- Backend:
  - Controlador: [app/Http/Controllers/ProspectoController.php](app/Http/Controllers/ProspectoController.php)
  - Método: `store()`
  - Modelo: [app/Models/Prospecto.php](app/Models/Prospecto.php)
- Frontend:
  - Vistas: [resources/views/agente/prospectos/create.blade.php](resources/views/agente/prospectos/create.blade.php) (y listado en `agente.prospectos.index`)

---

## CU13 — Gestionar prospectos (CRM)
- Comentario: CRM básico; historial y permisos para ver/editar prospectos.
- Backend:
  - Controlador: [app/Http/Controllers/ProspectoController.php](app/Http/Controllers/ProspectoController.php)
  - Métodos: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` (resource controller)
- Frontend:
  - Vistas: [resources/views/agente/prospectos/index.blade.php](resources/views/agente/prospectos/index.blade.php), [resources/views/agente/prospectos/edit.blade.php](resources/views/agente/prospectos/edit.blade.php)

---

## CU14 — Agendar visita a inmueble
- Comentario: Crear solicitud con fecha/hora; validar disponibilidad y zona horaria.
- Backend:
  - Controlador: [app/Http/Controllers/SolicitudController.php](app/Http/Controllers/SolicitudController.php)
  - Método: `store()`
  - Modelo: [app/Models/SolicitudVisita.php](app/Models/SolicitudVisita.php)
- Frontend:
  - Vista/acción cliente: [resources/views/cliente/propiedades.blade.php](resources/views/cliente/propiedades.blade.php) (botón "solicitar visita") y [resources/views/cliente/solicitudes.blade.php](resources/views/cliente/solicitudes.blade.php)

---

## CU15 — Reagendar visita
- Comentario: Mantener historial de cambios y notificar a partes involucradas.
- Backend:
  - Controlador: [app/Http/Controllers/SolicitudController.php](app/Http/Controllers/SolicitudController.php)
  - Método: `reagendar()`
- Frontend:
  - Formularios y rutas en [resources/views/cliente/solicitudes.blade.php](resources/views/cliente/solicitudes.blade.php)

---

## CU16 — Cancelar visita
- Comentario: Soporta cancelaciones parciales y registro de motivos.
- Backend:
  - Controlador: [app/Http/Controllers/SolicitudController.php](app/Http/Controllers/SolicitudController.php)
  - Método: `cancelar()`
- Frontend:
  - Acciones en [resources/views/cliente/solicitudes.blade.php](resources/views/cliente/solicitudes.blade.php)

---

## CU17 — Ver calendario de visitas
- Comentario: Calendario por rol; endpoints JSON para calendario JS (FullCalendar/Leaflet si aplica).
- Backend:
  - Controlador: [app/Http/Controllers/SolicitudController.php](app/Http/Controllers/SolicitudController.php)
  - Métodos: `calendarioAgente()`, `calendarioAsistente()`, `calendarioCliente()` y `eventos*()` que retornan JSON
  - Rutas: `/agente/calendario`, `/asistente/calendario`, `/cliente/calendario` (ver [routes/web.php](routes/web.php))
- Frontend:
  - Vistas: [resources/views/agente/calendario.blade.php](resources/views/agente/calendario.blade.php), [resources/views/cliente/calendario.blade.php](resources/views/cliente/calendario.blade.php), [resources/views/asistente/calendario.blade.php](resources/views/asistente/calendario.blade.php)

---

## CU18 — Registrar seguimiento de cliente
- Comentario: Registro de notas/acciones por prospecto; útil para reportes y actividad.
- Backend:
  - Controlador: [app/Http/Controllers/SeguimientoController.php](app/Http/Controllers/SeguimientoController.php)
  - Método: `store()`
  - Modelo: [app/Models/Seguimiento.php](app/Models/Seguimiento.php)
- Frontend:
  - Vista: [resources/views/agente/cliente-seguimientos.blade.php](resources/views/agente/cliente-seguimientos.blade.php)

---

## CU19 — Cargar imágenes de propiedad
- Comentario: Subida segura a S3/almacenamiento local; generar thumbnails.
- Backend:
  - Controlador: [app/Http/Controllers/ImagenController.php](app/Http/Controllers/ImagenController.php) — `subir()` (S3)
  - PropiedadController también gestiona subida/almacenamiento en `uploadImageToDisks()`
- Frontend:
  - Inputs en formularios de propiedad: [resources/views/admin/propiedades.blade.php](resources/views/admin/propiedades.blade.php), [resources/views/agente/propiedades.blade.php](resources/views/agente/propiedades.blade.php)

---

## CU20 — Generar reportes del sistema
- Comentario: Exportes a PDF/XLSX/CSV; añadir filtros y paginación apropiada.
- Backend:
  - Controlador: [app/Http/Controllers/ReporteController.php](app/Http/Controllers/ReporteController.php)
  - Métodos: `index()`, `export()` (pdf|xlsx|csv)
  - Modelo: [app/Models/RegistroActividad.php](app/Models/RegistroActividad.php)
- Frontend:
  - Vista: [resources/views/compartido/reportes.blade.php](resources/views/compartido/reportes.blade.php) (vista que contiene los filtros y tabla)
  - Plantilla PDF: [resources/views/compartido/reportes_pdf.blade.php](resources/views/compartido/reportes_pdf.blade.php)

---

## CU21 — Gestionar geolocalización de propiedades
- Comentario: Guardar coordenadas y mostrar mapas; sanitizar inputs geográficos.
- Backend:
  - Controlador: [app/Http/Controllers/PropiedadController.php](app/Http/Controllers/PropiedadController.php)
  - Campos: `latitud`, `longitud` en la tabla `propiedades` (mapeado en [app/Models/Propiedad.php](app/Models/Propiedad.php))
- Frontend:
  - Formulario con mapa: [resources/views/agente/propiedades.blade.php](resources/views/agente/propiedades.blade.php) — incluye `#mapaAgente` y campos `aLatitud` / `aLongitud`.
  - Mapa en detalle: [resources/views/cliente/detalle.blade.php](resources/views/cliente/detalle.blade.php) (muestra con Leaflet)

---
