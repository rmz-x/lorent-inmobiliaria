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

function classBox({ x, y, w, title, attrs = [], methods = [] }) {
  const headerH = 58;
  const lineH = 22;
  const attrsH = Math.max(attrs.length * lineH + 16, 34);
  const methodsH = Math.max(methods.length * lineH + 16, 34);
  const h = headerH + attrsH + methodsH;
  const titleLines = title.split("\\n");
  const out = [];

  out.push(`<rect x="${x}" y="${y}" width="${w}" height="${h}" rx="6" fill="#eadfce" stroke="#6b7280" stroke-width="2"/>`);
  out.push(`<line x1="${x}" y1="${y + headerH}" x2="${x + w}" y2="${y + headerH}" stroke="#6b7280" stroke-width="1.4"/>`);
  out.push(`<line x1="${x}" y1="${y + headerH + attrsH}" x2="${x + w}" y2="${y + headerH + attrsH}" stroke="#6b7280" stroke-width="1.4"/>`);

  titleLines.forEach((line, index) => {
    out.push(`<text x="${x + w / 2}" y="${y + 22 + index * 17}" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" font-weight="700" fill="#111827">${escapeXml(line)}</text>`);
  });

  attrs.forEach((attr, index) => {
    out.push(`<text x="${x + 14}" y="${y + headerH + 24 + index * lineH}" font-family="Arial, sans-serif" font-size="14" fill="#7f1d1d">- ${escapeXml(attr)}</text>`);
  });

  methods.forEach((method, index) => {
    out.push(`<text x="${x + 14}" y="${y + headerH + attrsH + 24 + index * lineH}" font-family="Arial, sans-serif" font-size="14" fill="#166534">+ ${escapeXml(method)}</text>`);
  });

  return { svg: out.join("\n"), h };
}

function actorSvg(x, y, label) {
  return [
    `<circle cx="${x}" cy="${y}" r="18" fill="#fde7b3" stroke="#6b7280" stroke-width="2"/>`,
    `<line x1="${x}" y1="${y + 18}" x2="${x}" y2="${y + 72}" stroke="#374151" stroke-width="2"/>`,
    `<line x1="${x - 30}" y1="${y + 38}" x2="${x + 30}" y2="${y + 38}" stroke="#374151" stroke-width="2"/>`,
    `<line x1="${x}" y1="${y + 72}" x2="${x - 29}" y2="${y + 116}" stroke="#374151" stroke-width="2"/>`,
    `<line x1="${x}" y1="${y + 72}" x2="${x + 29}" y2="${y + 116}" stroke="#374151" stroke-width="2"/>`,
    `<text x="${x}" y="${y + 144}" text-anchor="middle" font-family="Arial, sans-serif" font-size="15" fill="#111827">${escapeXml(label)}</text>`,
  ].join("\n");
}

function connector(x1, y1, x2, y2) {
  return `<line x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}" stroke="#6b7280" stroke-width="2"/>`;
}

function diagram({ fileName, tabTitle, actor, classes }) {
  const width = 1320;
  const height = 470;
  const frame = { x: 55, y: 108, w: 1210, h: 310 };
  const classW = classes.length === 4 ? 200 : 230;
  const startX = classes.length === 4 ? 365 : 420;
  const gap = classes.length === 4 ? 215 : 260;
  const classY = 185;
  const actorX = 190;
  const actorY = 205;
  const centerY = 255;

  const lines = [];
  lines.push(`<?xml version="1.0" encoding="UTF-8"?>`);
  lines.push(`<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">`);
  lines.push(`<rect width="100%" height="100%" fill="#ffffff"/>`);
  lines.push(`<text x="55" y="62" font-family="Times New Roman, serif" font-size="34" font-weight="700">DIAGRAMA DE CLASE ANALISIS</text>`);
  lines.push(`<rect x="${frame.x}" y="${frame.y}" width="${frame.w}" height="${frame.h}" fill="none" stroke="#111827" stroke-width="1.6"/>`);
  lines.push(`<path d="M${frame.x} ${frame.y} H270 L248 136 H${frame.x} Z" fill="#ffffff" stroke="#111827" stroke-width="1.4"/>`);
  lines.push(`<text x="${frame.x + 8}" y="${frame.y + 22}" font-family="Times New Roman, serif" font-size="15" font-weight="700">sd diagrama de clase analisis</text>`);
  lines.push(actorSvg(actorX, actorY, actor));

  const boxes = classes.map((item, index) => {
    const x = startX + index * gap;
    const box = classBox({ ...item, x, y: classY, w: classW });
    lines.push(box.svg);
    return { x, y: classY, w: classW, h: box.h };
  });

  lines.push(connector(actorX + 50, centerY, boxes[0].x, centerY));
  for (let i = 0; i < boxes.length - 1; i += 1) {
    lines.push(connector(boxes[i].x + boxes[i].w, centerY, boxes[i + 1].x, centerY));
  }

  lines.push(`</svg>`);
  fs.writeFileSync(path.join(outputDir, fileName), lines.join("\n"), "utf8");
}

const diagrams = [
  {
    fileName: "CU22_clase_analisis_visualizar_mapa.svg",
    tabTitle: "CU22 Visualizar mapa general de propiedades",
    actor: "Cliente",
    classes: [
      {
        title: "CU22 Visualizar mapa::\\nUI_mapa",
        methods: ["abrirMapa(): void", "filtrarMapa(): void", "verDetalle(): void"],
      },
      {
        title: "CU22 Visualizar mapa::\\npropiedadController",
        methods: ["buscar(): void", "disponibles(): void", "detalle(): void"],
      },
      {
        title: "CU22 Visualizar mapa::\\nPropiedad",
        attrs: ["id: int", "titulo: string", "zona: string", "latitud: decimal", "longitud: decimal"],
        methods: ["get(): void", "consultarPorFiltros(): void"],
      },
      {
        title: "CU22 Visualizar mapa::\\nServicioMapa",
        methods: ["cargarTiles(): void", "renderizarMarcadores(): void"],
      },
    ],
  },
  {
    fileName: "CU23_clase_analisis_notificaciones.svg",
    tabTitle: "CU23 Gestionar notificaciones automaticas",
    actor: "Administrador",
    classes: [
      {
        title: "CU23 Notificaciones::\\nUI_notificacion",
        methods: ["configurarAviso(): void", "enviarRecordatorio(): void", "mostrarConfirmacion(): void"],
      },
      {
        title: "CU23 Notificaciones::\\nsolicitudController",
        methods: ["visitasAsistente(): void", "cambiarEstado(): void", "actualizarEstado(): void"],
      },
      {
        title: "CU23 Notificaciones::\\nSolicitudVisita",
        attrs: ["id: int", "fecha: date", "hora: time", "estado: string"],
        methods: ["pendientes(): void", "update(): void"],
      },
      {
        title: "CU23 Notificaciones::\\nRegistroActividad",
        attrs: ["id: int", "accion: string", "descripcion: string", "fecha_hora: datetime"],
        methods: ["log(): void"],
      },
    ],
  },
  {
    fileName: "CU24_clase_analisis_recomendar_propiedades.svg",
    tabTitle: "CU24 Recomendar propiedades IA",
    actor: "Cliente",
    classes: [
      {
        title: "CU24 Recomendar::\\nUI_recomendacion",
        methods: ["solicitarRecomendaciones(): void", "mostrarSugeridas(): void", "verDetalle(): void"],
      },
      {
        title: "CU24 Recomendar::\\npropiedadController",
        methods: ["buscar(): void", "detalle(): void", "consultarDisponibles(): void"],
      },
      {
        title: "CU24 Recomendar::\\nPropiedad",
        attrs: ["id: int", "tipo: string", "zona: string", "precio: decimal", "estado: string"],
        methods: ["get(): void", "findOrFail(): void"],
      },
      {
        title: "CU24 Recomendar::\\nMotorIA",
        methods: ["priorizarCoincidencias(): void", "generarRanking(): void"],
      },
    ],
  },
  {
    fileName: "CU25_clase_analisis_predecir_tendencias.svg",
    tabTitle: "CU25 Predecir tendencias del mercado IA",
    actor: "Administrador",
    classes: [
      {
        title: "CU25 Tendencias::\\nUI_reportes",
        methods: ["generarPrediccion(): void", "mostrarResultado(): void", "exportar(): void"],
      },
      {
        title: "CU25 Tendencias::\\nreporteController",
        methods: ["index(): void", "export(): void", "storeReports(): void"],
      },
      {
        title: "CU25 Tendencias::\\nRegistroActividad",
        attrs: ["id: int", "accion: string", "rol: string", "fecha_hora: datetime"],
        methods: ["query(): void", "orderBy(): void"],
      },
      {
        title: "CU25 Tendencias::\\nMotorIA",
        methods: ["analizarTendencia(): void", "generarPrediccion(): void"],
      },
    ],
  },
  {
    fileName: "CU26_clase_analisis_asistente_voz.svg",
    tabTitle: "CU26 Gestionar asistente de voz IA",
    actor: "Usuario",
    classes: [
      {
        title: "CU26 Asistente voz::\\nUI_voice",
        methods: ["activarMicrofono(): void", "enviarConsulta(): void", "reproducirAudio(): void"],
      },
      {
        title: "CU26 Asistente voz::\\nreporteController",
        methods: ["voiceQuery(): void", "voicePolly(): void", "voiceReportProperties(): void"],
      },
      {
        title: "CU26 Asistente voz::\\nGeminiAPI",
        attrs: ["apiKey: string", "model: string", "endpoint: string"],
        methods: ["generateContent(): void"],
      },
      {
        title: "CU26 Asistente voz::\\nAmazonPolly",
        attrs: ["voice: string", "region: string", "engine: string"],
        methods: ["synthesizeSpeech(): void"],
      },
    ],
  },
];

diagrams.forEach(diagram);

fs.writeFileSync(
  path.join(outputDir, "README.md"),
  [
    "# Diagramas de clase analisis - Ciclo 5",
    "",
    "Diagramas para CU22-CU26 siguiendo el estilo del documento: actor, boundary, control, entity/service.",
    "",
    "Ejecutar de nuevo:",
    "",
    "```powershell",
    "cd \"D:\\\\PROYECTO-V7.0\"",
    "node docs/diagramas_clase_analisis_ciclo5/generate_class_analysis.cjs",
    "```",
    "",
  ].join("\n"),
  "utf8",
);
