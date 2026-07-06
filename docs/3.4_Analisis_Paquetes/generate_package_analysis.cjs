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

function packageBox({ x, y, w, h, label, accent = false }) {
  const tabW = Math.min(120, w * 0.35);
  const tabH = 30;
  const lines = label.split("\n");
  const fill = accent ? "#fff176" : "#ffef72";
  const stroke = "#111827";
  const textY = y + tabH + (h - tabH - (lines.length - 1) * 28) / 2 + 8;

  return [
    `<path d="M${x} ${y + tabH} V${y + 4} Q${x} ${y} ${x + 4} ${y} H${x + tabW - 4} Q${x + tabW} ${y} ${x + tabW} ${y + 4} V${y + tabH} H${x + w - 4} Q${x + w} ${y + tabH} ${x + w} ${y + tabH + 4} V${y + h - 4} Q${x + w} ${y + h} ${x + w - 4} ${y + h} H${x + 4} Q${x} ${y + h} ${x} ${y + h - 4} Z" fill="${fill}" stroke="${stroke}" stroke-width="3"/>`,
    `<line x1="${x}" y1="${y + tabH}" x2="${x + tabW}" y2="${y + tabH}" stroke="${stroke}" stroke-width="3"/>`,
    ...lines.map((line, index) =>
      `<text x="${x + w / 2}" y="${textY + index * 28}" text-anchor="middle" font-family="Arial Black, Arial, sans-serif" font-size="22" font-weight="900" fill="#0f172a">${esc(line)}</text>`,
    ),
  ].join("\n");
}

function arrow({ x1, y1, x2, y2, points = [] }) {
  const pathData = [`M${x1} ${y1}`, ...points.map(([x, y]) => `L${x} ${y}`), `L${x2} ${y2}`].join(" ");
  return `<path d="${pathData}" fill="none" stroke="#111827" stroke-width="3" stroke-dasharray="10 10" marker-end="url(#arrow)"/>`;
}

const packages = {
  seguridad: { x: 70, y: 70, w: 335, h: 140, label: "GESTION DE USUARIOS\nY SEGURIDAD" },
  propiedades: { x: 630, y: 70, w: 285, h: 140, label: "GESTION DE\nPROPIEDADES" },
  busqueda: { x: 70, y: 300, w: 335, h: 145, label: "BUSQUEDA Y CONSULTA\nDE INMUEBLES" },
  crm: { x: 590, y: 300, w: 285, h: 140, label: "CRM Y GESTION\nDE CLIENTES" },
  reportes: { x: 970, y: 300, w: 270, h: 165, label: "REPORTES Y\nADMINISTRACION\nDEL SISTEMA" },
  visitas: { x: 395, y: 560, w: 300, h: 140, label: "AGENDA Y GESTION\nDE VISITAS" },
  ia: { x: 900, y: 555, w: 340, h: 150, label: "INTELIGENCIA Y\nASISTENCIA\nPERSONALIZADA", accent: true },
};

const svg = [
  `<?xml version="1.0" encoding="UTF-8"?>`,
  `<svg xmlns="http://www.w3.org/2000/svg" width="1320" height="780" viewBox="0 0 1320 780">`,
  `<defs>`,
  `<marker id="arrow" markerWidth="12" markerHeight="9" refX="10" refY="4.5" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L12,4.5 L0,9 Z" fill="#111827"/></marker>`,
  `</defs>`,
  `<rect width="100%" height="100%" fill="#f4f4f4"/>`,
  `<text x="70" y="42" font-family="Times New Roman, serif" font-size="32" font-weight="700" fill="#111827">ANALISIS DE PAQUETES ACTUALIZADO - CICLO 5</text>`,
  packageBox(packages.seguridad),
  packageBox(packages.propiedades),
  packageBox(packages.busqueda),
  packageBox(packages.crm),
  packageBox(packages.reportes),
  packageBox(packages.visitas),
  packageBox(packages.ia),
  arrow({ x1: 405, y1: 140, x2: 630, y2: 140 }),
  arrow({ x1: 238, y1: 210, x2: 238, y2: 300 }),
  arrow({ x1: 772, y1: 210, x2: 772, y2: 300 }),
  arrow({ x1: 915, y1: 140, x2: 1105, y2: 300, points: [[1120, 140], [1120, 270]] }),
  arrow({ x1: 590, y1: 370, x2: 405, y2: 370 }),
  arrow({ x1: 875, y1: 370, x2: 970, y2: 370 }),
  arrow({ x1: 238, y1: 445, x2: 395, y2: 625, points: [[238, 625]] }),
  arrow({ x1: 735, y1: 440, x2: 695, y2: 625, points: [[735, 625]] }),
  arrow({ x1: 970, y1: 625, x2: 695, y2: 625 }),
  arrow({ x1: 970, y1: 555, x2: 1105, y2: 465, points: [[1105, 555]] }),
  arrow({ x1: 900, y1: 630, x2: 695, y2: 630 }),
  arrow({ x1: 900, y1: 610, x2: 875, y2: 395, points: [[835, 610], [835, 440]] }),
  arrow({ x1: 1030, y1: 555, x2: 1105, y2: 465 }),
  arrow({ x1: 970, y1: 585, x2: 875, y2: 370, points: [[915, 585], [915, 370]] }),
  arrow({ x1: 900, y1: 585, x2: 405, y2: 370, points: [[790, 520], [540, 520], [540, 370]] }),
  `</svg>`,
].join("\n");

fs.writeFileSync(path.join(outputDir, "analisis_paquetes_actualizado_ciclo5.svg"), svg, "utf8");

fs.writeFileSync(
  path.join(outputDir, "README.md"),
  [
    "# Analisis de paquetes actualizado - ciclo 5",
    "",
    "Paquete nuevo agregado: `Inteligencia y Asistencia Personalizada`.",
    "",
    "Este paquete cubre los casos de uso CU22-CU26:",
    "- CU22 Visualizar mapa general de propiedades.",
    "- CU23 Gestionar notificaciones automaticas.",
    "- CU24 Recomendar propiedades (IA).",
    "- CU25 Predecir tendencias del mercado (IA).",
    "- CU26 Gestionar asistente de voz (IA).",
    "",
    "Para regenerar:",
    "",
    "```powershell",
    "cd \"D:\\\\PROYECTO-V7.0\"",
    "node docs/analisis_paquetes_ciclo5/generate_package_analysis.cjs",
    "```",
    "",
  ].join("\n"),
  "utf8",
);
