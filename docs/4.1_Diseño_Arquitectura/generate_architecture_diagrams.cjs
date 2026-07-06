const fs = require("fs");
const path = require("path");

const outputDir = __dirname;

function esc(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function textLines({ x, y, lines, size = 18, weight = 400, family = "Arial, sans-serif", anchor = "middle", color = "#111827", gap = 24 }) {
  return lines.map((line, index) =>
    `<text x="${x}" y="${y + index * gap}" text-anchor="${anchor}" font-family="${family}" font-size="${size}" font-weight="${weight}" fill="${color}">${esc(line)}</text>`,
  ).join("\n");
}

function node3d({ x, y, w, h, title, stereotype, body }) {
  const dx = 18;
  const dy = -18;
  const inner = { x: x + 32, y: y + 62, w: w - 64, h: h - 86 };
  return [
    `<polygon points="${x},${y} ${x + dx},${y + dy} ${x + w + dx},${y + dy} ${x + w},${y}" fill="#d9ebff" stroke="#64748b" stroke-width="1.5"/>`,
    `<polygon points="${x + w},${y} ${x + w + dx},${y + dy} ${x + w + dx},${y + h + dy} ${x + w},${y + h}" fill="#c6dcf4" stroke="#64748b" stroke-width="1.5"/>`,
    `<rect x="${x}" y="${y}" width="${w}" height="${h}" fill="#cfe4fb" stroke="#64748b" stroke-width="1.8"/>`,
    `<text x="${x + w / 2}" y="${y + 32}" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="#0f172a">${esc(title)}</text>`,
    `<rect x="${inner.x}" y="${inner.y}" width="${inner.w}" height="${inner.h}" fill="#f7f7e7" stroke="#475569" stroke-width="1.6"/>`,
    `<text x="${inner.x + inner.w / 2}" y="${inner.y + 28}" text-anchor="middle" font-family="Arial, sans-serif" font-size="17" font-weight="700" fill="#111827">${esc(stereotype)}</text>`,
    textLines({ x: inner.x + inner.w / 2, y: inner.y + 62, lines: body, size: 16, gap: 22 }),
    `<path d="M${inner.x + inner.w - 42} ${inner.y + 18} h18 v15 h-18 z M${inner.x + inner.w - 34} ${inner.y + 10} h18 v15 h-18 z M${inner.x + inner.w - 26} ${inner.y + 26} h18 v15 h-18 z" fill="none" stroke="#334155" stroke-width="1.8"/>`,
  ].join("\n");
}

function arrow({ x1, y1, x2, y2, label = "", dashed = false, points = [] }) {
  const dash = dashed ? ` stroke-dasharray="8 8"` : "";
  const d = [`M${x1} ${y1}`, ...points.map(([x, y]) => `L${x} ${y}`), `L${x2} ${y2}`].join(" ");
  const labelSvg = label
    ? `<text x="${(x1 + x2) / 2}" y="${(y1 + y2) / 2 - 10}" text-anchor="middle" font-family="Arial, sans-serif" font-size="15" fill="#111827">${esc(label)}</text>`
    : "";
  return `<path d="${d}" fill="none" stroke="#111827" stroke-width="2.2"${dash} marker-end="url(#arrow)"/>${labelSvg}`;
}

function folder({ x, y, w, h, label }) {
  const tab = Math.min(95, w * 0.34);
  const lines = label.split("\n");
  const textY = y + 38 + (h - 45 - (lines.length - 1) * 20) / 2;
  return [
    `<path d="M${x} ${y + 26} V${y + 4} Q${x} ${y} ${x + 4} ${y} H${x + tab - 4} Q${x + tab} ${y} ${x + tab} ${y + 4} V${y + 26} H${x + w - 4} Q${x + w} ${y + 26} ${x + w} ${y + 30} V${y + h - 4} Q${x + w} ${y + h} ${x + w - 4} ${y + h} H${x + 4} Q${x} ${y + h} ${x} ${y + h - 4} Z" fill="#fff7b0" stroke="#111827" stroke-width="2"/>`,
    `<line x1="${x}" y1="${y + 26}" x2="${x + tab}" y2="${y + 26}" stroke="#111827" stroke-width="2"/>`,
    textLines({ x: x + w / 2, y: textY, lines, size: 15, weight: 800, family: "Arial Black, Arial, sans-serif", gap: 20 }),
  ].join("\n");
}

function layerBox({ x, y, w, h, label }) {
  return [
    `<rect x="${x}" y="${y}" width="${w}" height="${h}" fill="#fff7b0" stroke="#111827" stroke-width="1.8"/>`,
    textLines({ x: x + w / 2, y: y + h / 2 + 6, lines: label.split("\n"), size: 15, weight: 800, family: "Arial Black, Arial, sans-serif", gap: 20 }),
  ].join("\n");
}

function deploymentDiagram() {
  const svg = [
    `<?xml version="1.0" encoding="UTF-8"?>`,
    `<svg xmlns="http://www.w3.org/2000/svg" width="1320" height="760" viewBox="0 0 1320 760">`,
    `<defs><marker id="arrow" markerWidth="12" markerHeight="9" refX="10" refY="4.5" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L12,4.5 L0,9 Z" fill="#111827"/></marker></defs>`,
    `<rect width="100%" height="100%" fill="#ffffff"/>`,
    `<text x="42" y="52" font-family="Times New Roman, serif" font-size="30" font-weight="700">DISEÑO FISICO - DIAGRAMA DE DESPLIEGUE ACTUALIZADO</text>`,
    `<rect x="34" y="78" width="1248" height="640" fill="none" stroke="#111827" stroke-width="1.2"/>`,
    `<path d="M34 78 H250 L228 108 H34 Z" fill="#ffffff" stroke="#111827" stroke-width="1.2"/>`,
    `<text x="44" y="98" font-family="Arial, sans-serif" font-size="14" font-weight="700">deployment Deployment Model</text>`,
    node3d({ x: 70, y: 155, w: 215, h: 165, title: "Cliente", stereotype: "<<Browser>>", body: ["Navegador web", "HTML / CSS / JS", "Micrófono"] }),
    node3d({ x: 430, y: 130, w: 430, h: 250, title: "Render Cloud", stereotype: "<<Cloud Platform>>", body: ["Frontend: Blade / UI / Leaflet", "Backend: PHP Laravel", "Controladores y APIs", "IA, reportes y voz"] }),
    node3d({ x: 940, y: 145, w: 240, h: 175, title: "Base de Datos", stereotype: "<<Database>>", body: ["PostgreSQL", "Neon.tech"] }),
    node3d({ x: 430, y: 510, w: 260, h: 150, title: "Servicios IA / Voz", stereotype: "<<Cloud APIs>>", body: ["Gemini API", "Amazon Polly"] }),
    node3d({ x: 840, y: 510, w: 260, h: 150, title: "Servicios Externos", stereotype: "<<Cloud>>", body: ["OpenStreetMap", "AWS S3"] }),
    arrow({ x1: 285, y1: 235, x2: 430, y2: 235, label: "HTTPS" }),
    arrow({ x1: 860, y1: 250, x2: 940, y2: 250, label: "SQL" }),
    arrow({ x1: 590, y1: 380, x2: 565, y2: 510, label: "REST / SDK" }),
    arrow({ x1: 760, y1: 380, x2: 970, y2: 510, label: "API Maps / S3" }),
    arrow({ x1: 220, y1: 320, x2: 840, y2: 585, label: "Tiles mapa", dashed: true, points: [[220, 585]] }),
    `<rect x="70" y="560" width="245" height="112" rx="12" fill="#ffffff" stroke="#111827" stroke-width="1.8" stroke-dasharray="9 8"/>`,
    `<text x="192" y="586" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" font-weight="700">Simbología</text>`,
    `<rect x="95" y="605" width="28" height="20" fill="#cfe4fb" stroke="#64748b" stroke-width="1.3"/>`,
    `<text x="140" y="621" font-family="Arial, sans-serif" font-size="13">Nodo / servidor</text>`,
    `<rect x="95" y="635" width="28" height="20" fill="#f7f7e7" stroke="#475569" stroke-width="1.3"/>`,
    `<text x="140" y="651" font-family="Arial, sans-serif" font-size="13">Artefacto desplegado</text>`,
    `</svg>`,
  ].join("\n");
  fs.writeFileSync(path.join(outputDir, "4.1.1_diagrama_despliegue_actualizado.svg"), svg, "utf8");
}

function layeredDiagram() {
  const svg = [
    `<?xml version="1.0" encoding="UTF-8"?>`,
    `<svg xmlns="http://www.w3.org/2000/svg" width="1660" height="800" viewBox="0 0 1660 800">`,
    `<defs><marker id="arrow" markerWidth="12" markerHeight="9" refX="10" refY="4.5" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L12,4.5 L0,9 Z" fill="#111827"/></marker></defs>`,
    `<rect width="100%" height="100%" fill="#ffffff"/>`,
    textLines({ x: 14, y: 38, lines: ["CAPA DE", "PRESENTACION"], size: 16, weight: 700, anchor: "start", gap: 20 }),
    textLines({ x: 14, y: 238, lines: ["CAPA DE", "NEGOCIO"], size: 16, weight: 700, anchor: "start", gap: 20 }),
    textLines({ x: 14, y: 438, lines: ["CAPA DE", "DATOS"], size: 16, weight: 700, anchor: "start", gap: 20 }),
    textLines({ x: 14, y: 636, lines: ["CAPA DE", "INFRAESTRUCTURA"], size: 16, weight: 700, anchor: "start", gap: 20 }),
    `<line x1="0" y1="190" x2="1660" y2="190" stroke="#111827" stroke-width="1.2"/>`,
    `<line x1="0" y1="390" x2="1660" y2="390" stroke="#111827" stroke-width="1.2"/>`,
    `<line x1="0" y1="590" x2="1660" y2="590" stroke="#111827" stroke-width="1.2"/>`,
    folder({ x: 165, y: 34, w: 145, h: 105, label: "GESTION DE\nUSUARIOS Y\nSEGURIDAD" }),
    folder({ x: 370, y: 34, w: 145, h: 105, label: "GESTION DE\nPROPIEDADES" }),
    folder({ x: 575, y: 34, w: 145, h: 105, label: "BUSQUEDA Y\nCONSULTA DE\nINMUEBLES" }),
    folder({ x: 780, y: 34, w: 145, h: 105, label: "AGENDA Y\nGESTION DE\nVISITAS" }),
    folder({ x: 985, y: 34, w: 145, h: 105, label: "CRM Y\nGESTION DE\nCLIENTES" }),
    folder({ x: 1190, y: 34, w: 145, h: 105, label: "REPORTES Y\nADMINISTRACION\nDEL SISTEMA" }),
    folder({ x: 1390, y: 28, w: 210, h: 116, label: "INTELIGENCIA Y\nASISTENCIA\nPERSONALIZADA" }),
    layerBox({ x: 165, y: 235, w: 150, h: 74, label: "AUTENTICACION" }),
    layerBox({ x: 370, y: 235, w: 150, h: 74, label: "PROPIEDADES" }),
    layerBox({ x: 575, y: 235, w: 150, h: 74, label: "INMUEBLES" }),
    layerBox({ x: 780, y: 235, w: 150, h: 74, label: "VISITAS" }),
    layerBox({ x: 985, y: 235, w: 150, h: 74, label: "CLIENTES" }),
    layerBox({ x: 1190, y: 235, w: 150, h: 74, label: "REPORTES" }),
    layerBox({ x: 1405, y: 235, w: 180, h: 74, label: "IA / VOZ\nMAPAS" }),
    layerBox({ x: 315, y: 435, w: 190, h: 74, label: "BLADE / UI" }),
    layerBox({ x: 675, y: 435, w: 205, h: 74, label: "PHP LARAVEL" }),
    layerBox({ x: 1035, y: 435, w: 205, h: 74, label: "POSTGRESQL" }),
    layerBox({ x: 1365, y: 435, w: 220, h: 74, label: "SERVICIOS IA\nY MAPAS" }),
    layerBox({ x: 315, y: 640, w: 190, h: 74, label: "NAVEGADOR WEB" }),
    layerBox({ x: 675, y: 640, w: 205, h: 74, label: "SERVIDOR LARAVEL" }),
    layerBox({ x: 1035, y: 640, w: 205, h: 74, label: "BD POSTGRESQL" }),
    layerBox({ x: 1365, y: 640, w: 220, h: 74, label: "GEMINI / POLLY\nOSM / S3" }),
    ...[
      [238, 139, 240, 235], [443, 139, 445, 235], [648, 139, 650, 235],
      [853, 139, 855, 235], [1058, 139, 1060, 235], [1263, 139, 1265, 235], [1495, 144, 1495, 235],
      [240, 309, 410, 435], [445, 309, 410, 435], [650, 309, 410, 435],
      [240, 309, 778, 435], [445, 309, 778, 435], [650, 309, 778, 435],
      [855, 309, 778, 435], [1060, 309, 778, 435], [1265, 309, 778, 435], [1495, 309, 778, 435],
      [650, 309, 1138, 435], [855, 309, 1138, 435], [1060, 309, 1138, 435], [1265, 309, 1138, 435],
      [1495, 309, 1475, 435],
      [1495, 509, 778, 640], [1495, 509, 1138, 640], [1495, 509, 1475, 640],
      [410, 509, 410, 640], [778, 509, 778, 640], [1138, 509, 1138, 640],
    ].map(([x1, y1, x2, y2]) => arrow({ x1, y1, x2, y2, dashed: true })),
    `</svg>`,
  ].join("\n");
  fs.writeFileSync(path.join(outputDir, "4.1.2_diseno_paquetes_por_capas_actualizado.svg"), svg, "utf8");
}

deploymentDiagram();
layeredDiagram();

fs.writeFileSync(
  path.join(outputDir, "README.md"),
  [
    "# Arquitectura actualizada - ciclo 5",
    "",
    "Incluye los dos diagramas solicitados:",
    "- `4.1.1_diagrama_despliegue_actualizado.svg`",
    "- `4.1.2_diseno_paquetes_por_capas_actualizado.svg`",
    "",
    "Cambios principales:",
    "- Se agrego el paquete `Inteligencia y Asistencia Personalizada`.",
    "- Se agregaron servicios externos para Gemini API, Amazon Polly, OpenStreetMap y AWS S3.",
    "- Se conecto el nuevo paquete con UI, Laravel, PostgreSQL y servicios externos.",
    "",
    "Para regenerar:",
    "",
    "```powershell",
    "cd \"D:\\\\PROYECTO-V7.0\"",
    "node docs/arquitectura_actualizada_ciclo5/generate_architecture_diagrams.cjs",
    "```",
    "",
  ].join("\n"),
  "utf8",
);
