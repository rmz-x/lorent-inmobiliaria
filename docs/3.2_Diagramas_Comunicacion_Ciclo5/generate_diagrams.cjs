const fs = require("fs");
const path = require("path");

const outputDir = __dirname;

function escapeXml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function diagram({ fileName, title, actor, objects, messages }) {
  const width = 1280;
  const height = 430;
  const actorX = 180;
  const centerY = 220;
  const objectStartX = 470;
  const objectGap = 220;
  const radius = 38;
  const colors = ["#1d4ed8", "#b91c1c", "#15803d", "#7c3aed", "#c2410c"];
  const markerNames = ["blue", "red", "green", "purple", "orange"];
  const nodes = objects.map((name, index) => ({
    name,
    x: objectStartX + index * objectGap,
    y: centerY,
  }));

  const lines = [];
  lines.push(`<?xml version="1.0" encoding="UTF-8"?>`);
  lines.push(`<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">`);
  lines.push(`<defs>`);
  [
    ["blue", "#1d4ed8"],
    ["red", "#b91c1c"],
    ["green", "#15803d"],
    ["purple", "#7c3aed"],
    ["orange", "#c2410c"],
  ].forEach(([name, color]) => {
    lines.push(`<marker id="arrow-${name}" markerWidth="12" markerHeight="8" refX="10" refY="4" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L12,4 L0,8 Z" fill="${color}"/></marker>`);
  });
  lines.push(`</defs>`);
  lines.push(`<rect width="100%" height="100%" fill="#ffffff"/>`);
  lines.push(`<text x="45" y="44" font-family="Times New Roman, serif" font-size="34" font-weight="700">DIAGRAMA DE COMUNICACION / COLABORACION</text>`);
  lines.push(`<rect x="45" y="72" width="1190" height="295" fill="none" stroke="#111827" stroke-width="1.6"/>`);
  lines.push(`<path d="M45 72 H270 L248 106 H45 Z" fill="#ffffff" stroke="#111827" stroke-width="1.4"/>`);
  lines.push(`<text x="57" y="96" font-family="Times New Roman, serif" font-size="16" font-weight="700">sd ${escapeXml(title)}</text>`);

  lines.push(`<circle cx="${actorX}" cy="170" r="19" fill="#fde7b3" stroke="#6b7280" stroke-width="2"/>`);
  lines.push(`<line x1="${actorX}" y1="189" x2="${actorX}" y2="246" stroke="#374151" stroke-width="2"/>`);
  lines.push(`<line x1="${actorX - 32}" y1="210" x2="${actorX + 32}" y2="210" stroke="#374151" stroke-width="2"/>`);
  lines.push(`<line x1="${actorX}" y1="246" x2="${actorX - 31}" y2="292" stroke="#374151" stroke-width="2"/>`);
  lines.push(`<line x1="${actorX}" y1="246" x2="${actorX + 31}" y2="292" stroke="#374151" stroke-width="2"/>`);
  lines.push(`<text x="${actorX}" y="326" text-anchor="middle" font-family="Arial, sans-serif" font-size="17" fill="#111827">${escapeXml(actor)}</text>`);

  lines.push(`<line x1="${actorX + 58}" y1="${centerY}" x2="${nodes[0].x - radius}" y2="${centerY}" stroke="#9ca3af" stroke-width="3"/>`);
  nodes.forEach((node, index) => {
    lines.push(`<circle cx="${node.x}" cy="${node.y}" r="${radius}" fill="#cdeff0" stroke="#6b7280" stroke-width="2.2"/>`);
    lines.push(`<text x="${node.x}" y="320" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" font-weight="700" fill="#374151">${escapeXml(node.name)}</text>`);
    if (index < nodes.length - 1) {
      lines.push(`<line x1="${node.x + radius}" y1="${centerY}" x2="${nodes[index + 1].x - radius}" y2="${centerY}" stroke="#9ca3af" stroke-width="3"/>`);
    }
  });

  messages.forEach((msg, index) => {
    const fromX = msg.from === "actor" ? actorX + 70 : nodes[msg.from].x + 48;
    const toX = msg.to === "actor" ? actorX + 28 : nodes[msg.to].x - 48;
    const baseY = 116 + (index % 5) * 27;
    const laneOffset = Math.floor(index / 5) * 142;
    let y = baseY + laneOffset;
    if (y > 285) y = 285 - (index % 3) * 22;
    const color = colors[index % colors.length];
    const marker = markerNames[index % markerNames.length];

    if (fromX < toX) {
      lines.push(`<line x1="${fromX}" y1="${y}" x2="${toX}" y2="${y}" stroke="${color}" stroke-width="2.4" marker-end="url(#arrow-${marker})"/>`);
      lines.push(`<text x="${fromX + 6}" y="${y - 8}" font-family="Arial, sans-serif" font-size="14" font-weight="700" fill="${color}">${escapeXml(msg.text)}</text>`);
    } else {
      const midY = y - 18;
      lines.push(`<path d="M${fromX} ${y} C${fromX - 50} ${midY}, ${toX + 50} ${midY}, ${toX} ${y}" fill="none" stroke="${color}" stroke-width="2.4" marker-end="url(#arrow-${marker})"/>`);
      lines.push(`<text x="${toX + 8}" y="${midY - 6}" font-family="Arial, sans-serif" font-size="14" font-weight="700" fill="${color}">${escapeXml(msg.text)}</text>`);
    }
  });

  lines.push(`</svg>`);
  fs.writeFileSync(path.join(outputDir, fileName), lines.join("\n"), "utf8");
}

const diagrams = [
  {
    fileName: "CU22_visualizar_mapa_general_propiedades.svg",
    title: "CU22",
    actor: "Cliente",
    objects: ["UI_mapa", "PropiedadController", "Propiedad", "ServicioMapa"],
    messages: [
      ["actor", 0, "1: abrirMapa()"],
      [0, 1, "1.1: buscar()"],
      [1, 2, "1.2: obtenerDisponibles()"],
      [2, 1, "1.3: get()"],
      [1, 0, "1.4: mostrarPropiedades()"],
      [0, 3, "1.5: cargarTiles()"],
      ["actor", 0, "2: filtrarMapa()"],
      [0, 1, "2.1: buscar(filtros)"],
      [1, 2, "2.2: consultarPorFiltros()"],
      [0, 3, "2.3: renderizarMarcadores()"],
    ],
  },
  {
    fileName: "CU23_gestionar_notificaciones_automaticas.svg",
    title: "CU23",
    actor: "Administrador",
    objects: ["UI_notificacion", "SolicitudController", "SolicitudVisita", "RegistroActividad"],
    messages: [
      ["actor", 0, "1: configurarAviso()"],
      [0, 1, "1.1: guardarRegla()"],
      [1, 2, "1.2: verificarVisitas()"],
      [2, 1, "1.3: pendientes()"],
      [1, 3, "1.4: log()"],
      ["actor", 0, "2: enviarRecordatorio()"],
      [0, 1, "2.1: cambiarEstado()"],
      [1, 2, "2.2: update()"],
      [1, 0, "2.3: confirmarEnvio()"],
    ],
  },
  {
    fileName: "CU24_recomendar_propiedades_ia.svg",
    title: "CU24",
    actor: "Cliente",
    objects: ["UI_busqueda", "PropiedadController", "Propiedad", "MotorIA"],
    messages: [
      ["actor", 0, "1: solicitarRecomendaciones()"],
      [0, 1, "1.1: buscar(preferencias)"],
      [1, 2, "1.2: consultarDisponibles()"],
      [2, 1, "1.3: get()"],
      [1, 3, "1.4: priorizarCoincidencias()"],
      [3, 1, "1.5: ranking()"],
      [1, 0, "1.6: mostrarSugeridas()"],
      ["actor", 0, "2: verDetalle()"],
      [0, 1, "2.1: detalle(id)"],
      [1, 2, "2.2: findOrFail()"],
    ],
  },
  {
    fileName: "CU25_predecir_tendencias_mercado_ia.svg",
    title: "CU25",
    actor: "Administrador",
    objects: ["UI_reportes", "ReporteController", "RegistroActividad", "MotorIA"],
    messages: [
      ["actor", 0, "1: generarPrediccion()"],
      [0, 1, "1.1: index(filtros)"],
      [1, 2, "1.2: consultarHistorico()"],
      [2, 1, "1.3: datos()"],
      [1, 3, "1.4: analizarTendencia()"],
      [3, 1, "1.5: prediccion()"],
      [1, 0, "1.6: mostrarResultado()"],
      ["actor", 0, "2: exportar()"],
      [0, 1, "2.1: export(type)"],
      [1, 2, "2.2: obtenerRegistros()"],
    ],
  },
  {
    fileName: "CU26_gestionar_asistente_voz_ia.svg",
    title: "CU26",
    actor: "Usuario",
    objects: ["UI_voice", "ReporteController", "GeminiAPI", "AmazonPolly"],
    messages: [
      ["actor", 0, "1: activarMicrofono()"],
      [0, 1, "1.1: voiceQuery(q)"],
      [1, 2, "1.2: generateContent()"],
      [2, 1, "1.3: speech"],
      [1, 0, "1.4: mostrarRespuesta()"],
      [0, 1, "2: voicePolly(text)"],
      [1, 3, "2.1: synthesizeSpeech()"],
      [3, 1, "2.2: audioBase64"],
      [1, 0, "2.3: reproducirAudio()"],
      ["actor", 0, "3: consultarReporte()"],
    ],
  },
];

diagrams.forEach((item) => {
  diagram({
    ...item,
    messages: item.messages.map(([from, to, text]) => ({ from, to, text })),
  });
});

fs.writeFileSync(
  path.join(outputDir, "README.md"),
  [
    "# Diagramas de comunicacion - Ciclo 5",
    "",
    "Estos archivos SVG siguen el estilo del documento: actor, objetos de frontera/control/modelo/servicio y mensajes numerados.",
    "",
    "- CU22: Visualizar mapa general de propiedades.",
    "- CU23: Gestionar notificaciones automaticas.",
    "- CU24: Recomendar propiedades (IA).",
    "- CU25: Predecir tendencias del mercado (IA).",
    "- CU26: Gestionar asistente de voz (IA).",
    "",
    "Nota: los nombres de UI/controlador/modelo se alinean con la estructura Laravel existente. Para CU23-CU25 se usan objetos de servicio conceptuales cuando la funcionalidad se documenta como IA/notificacion y no existe una clase dedicada en el codigo.",
    "",
  ].join("\n"),
  "utf8",
);
