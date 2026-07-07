from pathlib import Path

path = Path('resources/views/cliente/recomendaciones.blade.php')
text = path.read_text(encoding='utf-8')
start = text.index('.feedback-btn:hover .feedback-label {')
end = text.index('<div class="rec-body">', start)
new = '''.feedback-btn:hover .feedback-label {
    opacity: 1;
    transform: translateX(-50%) translateY(-2px);
}
.feedback-btn.selected .feedback-label,
.feedback-btn.selected:hover .feedback-label {
    opacity: 0;
    visibility: hidden;
}
</style>
@endpush

@section('contenido')
<div class="rec-hero">
    <h2 style="font-size:22px;font-weight:800;margin-bottom:6px">Sugerencias personalizadas para ti</h2>
    <p style="color:rgba(255,255,255,.76);font-size:13px;max-width:720px">
        El sistema prioriza propiedades disponibles según tus visitas, solicitudes y zonas/tipos consultados recientemente.
    </p>
</div>

<div class="card" style="margin-bottom:16px">
    <span style="font-size:13px;color:#64748b">
        Preferencias detectadas:
        <strong>Tipos:</strong> {{ count($preferencias['tipos']) ? implode(', ', $preferencias['tipos']) : 'Sin historial suficiente' }}
        ·
        <strong>Zonas:</strong> {{ count($preferencias['zonas']) ? implode(', ', $preferencias['zonas']) : 'Sin historial suficiente' }}
    </span>
</div>

<div class="rec-grid">
    @forelse($propiedades as $p)
        <div class="rec-card">
            <div class="rec-img">
                @if($p->imagen)
                    <img src="{{ $p->imagen_url }}" alt="{{ $p->titulo }}" style="width:100%;height:100%;object-fit:cover">
                @else
                    Sin foto
                @endif
            </div>
'''
path.write_text(text[:start] + new + text[end:], encoding='utf-8')
print('fixed')
