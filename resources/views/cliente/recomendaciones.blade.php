@extends('layouts.panel')
@section('titulo', 'Recomendaciones')
@section('titulo_pagina', 'Propiedades Recomendadas')

@push('styles')
<style>
:root {
    font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
.rec-page {
    min-height: auto;
    padding: 10px 16px 18px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 55%, #eff6ff 100%);
    background-image: radial-gradient(circle at top right, rgba(59, 130, 246, .14), transparent 22%),
                      linear-gradient(135deg, #ffffff 0%, #f8fbff 55%, #eff6ff 100%);
    text-align: left !important;
    width: 100%;
    max-width: 100%;
}
.rec-header {
    width: 100%;
    max-width: 100%;
    margin: 0 0 10px;
    text-align: left !important;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.pref-card {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
    align-items: center;
    padding: 14px 18px;
    background: #ffffff;
    border: 1px solid rgba(96, 123, 211, .20);
    border-radius: 18px;
    box-shadow: 0 16px 34px rgba(96, 123, 211, .08);
    color: #1e3a8a;
    font-size: 0.95rem;
}
.pref-card span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.rec-hero {
    background: linear-gradient(135deg, #0b1f47 0%, #122b5f 48%, #1c3a7a 100%);
    border-radius: 18px;
    color: #ffffff;
    padding: 18px 20px 18px;
    box-shadow: 0 16px 38px rgba(15, 23, 42, .18);
    text-align: left !important;
    width: 100%;
}
.rec-hero h2,
.rec-hero p {
    margin: 0;
    text-align: left !important;
}
.rec-hero h2 {
    font-size: 1.45rem;
    line-height: 1.1;
    margin-bottom: 6px;
}
.rec-hero p {
    color: rgba(255,255,255,.85);
    font-size: 0.92rem;
    max-width: 720px;
    line-height: 1.6;
}
.rec-grid {
    width: 100%;
    max-width: 100%;
    display: grid;
    gap: 16px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
}
.rec-card {
    display: flex;
    flex-direction: column;
}
.rec-card {
    border-radius: 24px;
    overflow: hidden;
    background: linear-gradient(180deg, #dbeafe 0%, #e2edff 40%, #eff6ff 100%);
    transition: transform .25s ease, box-shadow .25s ease;
    border: 1px solid rgba(37, 99, 235, .16);
}
.rec-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 22px 48px rgba(37, 99, 235, .12);
}
.rec-img {
    align-items: center;
    background: #e2e8f0;
    color: #64748b;
    display: flex;
    height: 220px;
    justify-content: center;
    overflow: hidden;
    position: relative;
}
.rec-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.img-badge {
    position: absolute;
    top: 0;
    left: 0;
    transform: translate(0, 0);
    background: rgba(37, 99, 235, .94);
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 0 0 10px 0;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: .01em;
    box-shadow: none;
    border: none;
}
.rec-body {
    padding: 10px 12px 14px;
}
.rec-body h3 {
    font-size: 1rem;
    margin: 0 0 6px;
}
.rec-body p {
    color: #475569;
    font-size: 0.86rem;
    margin: 0 0 8px;
    line-height: 1.4;
}
.prop-summary {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}
.prop-zone,
.prop-area {
    margin: 0;
}
.prop-zone {
    color: #334155;
    font-size: 0.96rem;
    font-weight: 600;
    line-height: 1.3;
}
.prop-area {
    color: #64748b;
    font-size: 0.8rem;
    line-height: 1.4;
}
.prop-title {
    color: #0f172a;
    font-size: 1.25rem;
    font-weight: 900;
    margin: 0 0 8px;
    line-height: 1.1;
    max-width: 100%;
}
.prop-title {
    margin-bottom: 8px;
}
.prop-footer {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 10px;
    margin-top: 10px;
}
.prop-price {
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 800;
}
.detail-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #2563eb;
    color: #ffffff;
    border-radius: 999px;
    padding: 10px 24px;
    min-width: 170px;
    font-size: 0.92rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s ease, transform .2s ease;
    margin: 0 auto;
}
.detail-link:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}
.feedback-group {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.feedback-trigger {
    align-items: center;
    background: transparent;
    border: 1px solid rgba(148, 163, 184, .25);
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    font-size: 0.92rem;
    font-weight: 700;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 999px;
    transition: background .2s ease, color .2s ease, box-shadow .2s ease;
}
.feedback-trigger:hover {
    background: rgba(37, 99, 235, .08);
}
.feedback-trigger.selected {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 12px 24px rgba(37, 99, 235, .15);
}
.feedback-options {
    display: none;
    position: absolute;
    left: 50%;
    top: calc(100% + 10px);
    transform: translateX(-50%);
    z-index: 5;
    border-radius: 22px;
    background: linear-gradient(135deg, rgba(255,255,255,.98), rgba(219,234,254,.94));
    border: 1px solid rgba(96, 123, 211, .22);
    box-shadow: 0 18px 42px rgba(96, 123, 211, .16);
    padding: 8px;
    flex-direction: row;
    gap: 10px;
}
.feedback-group.open .feedback-options {
    display: flex;
}
.feedback-group.open .feedback-trigger {
    background: #1d4ed8;
    color: #ffffff;
}
.feedback-btn {
    width: 46px;
    height: 46px;
    min-width: unset;
    padding: 0;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid rgba(148, 163, 184, .26);
    color: #64748b;
    box-shadow: 0 6px 14px rgba(15, 23, 42, .08);
}
.feedback-btn .feedback-label {
    display: none;
}
.feedback-btn:hover {
    background: #f1f5f9;
    color: #334155;
    transform: translateY(-1px);
    border-color: rgba(148, 163, 184, .35);
}
.feedback-btn.like,
.feedback-btn.dislike {
    color: #64748b;
}
.feedback-btn.selected.like {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 12px 24px rgba(37, 99, 235, .18);
}
.feedback-btn.selected.dislike {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 12px 24px rgba(239, 68, 68, .18);
}
.prop-zone {
    color: #334155;
    font-size: 0.96rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
}
.prop-area {
    color: #64748b;
    font-size: 0.8rem;
    margin: 4px 0 0;
    line-height: 1.4;
}
.prop-title {
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0 0 6px;
    line-height: 1.2;
}
.prop-price {
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 800;
}
.detail-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #2563eb;
    color: #ffffff;
    border-radius: 999px;
    padding: 8px 12px;
    font-size: 0.85rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s ease, transform .2s ease;
}
.detail-link:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}
.empty-state {
    grid-column: 1/-1;
    text-align: center;
    color: #64748b;
    background: #ffffff;
    border-radius: 24px;
    padding: 28px;
    border: 1px solid rgba(148, 163, 184, .16);
}
.feedback-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
}
.detail-row {
    margin-top: 12px;
    text-align: center;
}
.feedback-btn {
    align-items: center;
    background: #f8fafc;
    border: 1px solid rgba(148, 163, 184, .22);
    border-radius: 999px;
    color: #64748b;
    cursor: pointer;
    display: inline-flex;
    font-size: 12px;
    font-weight: 700;
    gap: 8px;
    justify-content: center;
    min-width: 52px;
    padding: 10px 14px;
    position: relative;
    text-decoration: none;
    transition: transform .15s ease, background .15s ease, color .15s ease, box-shadow .15s ease, border-color .15s ease;
}
.feedback-btn:hover {
    background: #f1f5f9;
    color: #334155;
    transform: translateY(-1px);
    border-color: rgba(148, 163, 184, .35);
}
.feedback-btn.like,
.feedback-btn.dislike {
    color: #64748b;
}
.feedback-btn.selected.like {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 16px 40px rgba(37, 99, 235, .18);
}
.feedback-btn.selected.dislike {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 16px 40px rgba(220, 38, 38, .18);
}
.feedback-icon {
    display: inline-flex;
    width: 18px;
    height: 18px;
    font-size: 14px;
    line-height: 1;
}
.feedback-emoji {
    display: inline-flex;
    font-size: 14px;
}
.feedback-label {
    display: block;
    position: absolute;
    top: -34px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15, 23, 42, .95);
    color: white;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity .15s ease, transform .15s ease;
    z-index: 5;
}
.feedback-btn:hover .feedback-label {
    opacity: 1;
    transform: translateX(-50%) translateY(-2px);
}
.feedback-btn.selected .feedback-label,
.feedback-btn.selected:hover .feedback-label {
    opacity: 0;
    visibility: hidden;
}
@media (max-width: 900px) {
    .rec-header {
        padding: 0 16px;
    }
}
@media (max-width: 640px) {
    .rec-grid {
        grid-template-columns: 1fr;
    }
    .rec-actions {
        flex-direction: column;
        align-items: stretch;
    }
}
.rec-header,
.rec-hero,
.rec-hero h2,
.rec-hero p {
    text-align: left !important;
}
.rec-hero {
    align-items: flex-start !important;
}
</style>
@endpush

@section('contenido')
<div class="rec-page">
    <div class="rec-header">
        <div class="rec-hero">
            <h2>Sugerencias personalizadas para ti</h2>
            <p>
                El sistema prioriza propiedades disponibles según tus visitas, solicitudes y zonas/tipos consultados recientemente.
            </p>
        </div>
        <div class="pref-card">
            <span><strong>Tipos:</strong> {{ count($preferencias['tipos']) ? implode(', ', $preferencias['tipos']) : 'Sin historial suficiente' }}</span>
            <span><strong>Zonas:</strong> {{ count($preferencias['zonas']) ? implode(', ', $preferencias['zonas']) : 'Sin historial suficiente' }}</span>
        </div>
    </div>

    <div class="rec-grid">
    @forelse($propiedades as $p)
        <div class="rec-card">
            <div class="rec-img">
                @if($p->imagen)
                    <img src="{{ $p->imagen_url }}" alt="{{ $p->titulo }}">
                @else
                    <span style="color:#64748b;font-size:14px">Sin foto disponible</span>
                @endif
                <div class="img-badge">Coincidencia {{ number_format($p->puntaje_recomendacion, 0) }}%</div>
            </div>
            <div class="rec-body">
                <h3 class="prop-title">{{ $p->titulo }}</h3>
                <div class="prop-summary">
                    <p class="prop-zone">{{ $p->zona }}</p>
                    <p class="prop-area">{{ $p->area }} m²</p>
                </div>
                @php $feedback = $p->feedback ?? null; @endphp
                <div class="prop-footer">
                    <span class="prop-price">${{ number_format($p->precio, 0, ',', '.') }}</span>
                    <div class="feedback-group" data-feedback="{{ $feedback ?? 'none' }}">
                        <button type="button" class="feedback-trigger {{ $feedback === 'like' ? 'selected' : '' }}" aria-expanded="false" aria-label="Feedback">
                            <span class="feedback-icon">👍</span>
                        </button>
                        <div class="feedback-options" aria-hidden="true">
                            <form class="feedback-form like" method="POST" action="{{ route('cliente.recomendaciones.feedback', $p) }}" style="margin:0;">
                                @csrf
                                <input type="hidden" name="feedback" value="like">
                                <button type="submit" data-propiedad="{{ $p->id }}" class="feedback-btn like {{ $feedback === 'like' ? 'selected' : '' }}">
                                    <span class="feedback-icon">👍</span>
                                </button>
                            </form>
                            <form class="feedback-form dislike" method="POST" action="{{ route('cliente.recomendaciones.feedback', $p) }}" style="margin:0;">
                                @csrf
                                <input type="hidden" name="feedback" value="dislike">
                                <button type="submit" data-propiedad="{{ $p->id }}" class="feedback-btn dislike {{ $feedback === 'dislike' ? 'selected' : '' }}">
                                    <span class="feedback-icon">👎</span>
                                </button>
                            </form>
                            <form class="feedback-form none" method="POST" action="{{ route('cliente.recomendaciones.feedback', $p) }}" style="display:none;">
                                @csrf
                                <input type="hidden" name="feedback" value="none">
                                <button type="submit" hidden></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="detail-row">
                    <a class="detail-link" href="{{ route('cliente.propiedades.detalle', $p) }}">Ver detalle</a>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            No hay propiedades disponibles para recomendar por ahora.
        </div>
    @endforelse
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.feedback-trigger').forEach(trigger => {
            const group = trigger.closest('.feedback-group');
            const likeForm = group.querySelector('form.like');
            const noneForm = group.querySelector('form.none');
            const options = group.querySelector('.feedback-options');
            let holdTimer = null;
            let opened = false;

            const openOptions = () => {
                opened = true;
                holdTimer = null;
                group.classList.add('open');
                if (options) {
                    options.style.display = 'flex';
                    options.setAttribute('aria-hidden', 'false');
                }
                trigger.setAttribute('aria-expanded', 'true');
            };

            const closeOptions = () => {
                opened = false;
                group.classList.remove('open');
                if (options) {
                    options.style.display = 'none';
                    options.setAttribute('aria-hidden', 'true');
                }
                trigger.setAttribute('aria-expanded', 'false');
            };

            const cancelHold = () => {
                if (holdTimer) {
                    clearTimeout(holdTimer);
                    holdTimer = null;
                }
            };

            const submitLike = () => {
                if (likeForm) {
                    likeForm.querySelector('button[type="submit"]').click();
                }
            };

            const submitNone = () => {
                if (noneForm) {
                    noneForm.querySelector('button[type="submit"]').click();
                }
            };

            trigger.addEventListener('mousedown', () => {
                holdTimer = setTimeout(openOptions, 400);
            });
            trigger.addEventListener('touchstart', () => {
                holdTimer = setTimeout(openOptions, 400);
            });

            trigger.addEventListener('mouseup', () => {
                if (holdTimer) {
                    cancelHold();
                    const current = group.dataset.feedback;
                    if (current === 'like') {
                        submitNone();
                    } else {
                        submitLike();
                    }
                }
            });
            trigger.addEventListener('touchend', () => {
                if (holdTimer) {
                    cancelHold();
                    const current = group.dataset.feedback;
                    if (current === 'like') {
                        submitNone();
                    } else {
                        submitLike();
                    }
                }
            });
            trigger.addEventListener('mouseleave', cancelHold);

            document.addEventListener('click', function (event) {
                if (!group.contains(event.target)) {
                    closeOptions();
                }
            });
        });

        document.querySelectorAll('.feedback-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const button = event.submitter || form.querySelector('button[type="submit"]');
                const url = form.action;
                const data = new FormData(form);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: data,
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const group = button.closest('.feedback-group');
                        const triggerButton = group ? group.querySelector('.feedback-trigger') : null;
                        const selectedFeedback = result.feedback || 'none';

                        if (group) {
                            group.dataset.feedback = selectedFeedback;
                            group.querySelectorAll('.feedback-btn').forEach(btn => {
                                btn.classList.remove('selected');
                            });

                            if (triggerButton) {
                                triggerButton.classList.toggle('selected', selectedFeedback === 'like');
                            }

                            const options = group.querySelector('.feedback-options');
                            if (options) {
                                options.style.display = 'none';
                                group.classList.remove('open');
                            }
                        }

                        if (button.classList.contains('like') && selectedFeedback === 'like') {
                            button.classList.add('selected');
                        }
                    }
                })
                .catch(() => {
                    // En caso de error o respuesta inesperada, recargar como fallback.
                    form.submit();
                });
            });
        });
    });
</script>
@endpush
