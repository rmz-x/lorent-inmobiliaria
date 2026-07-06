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

const classes = [
  { name: "Usuario", x: 500, y: 80, w: 250, attrs: ["id", "nombre", "correo", "usuario", "contrasena", "rol", "estado"] },
  { name: "Propiedad", x: 470, y: 360, w: 310, attrs: ["id", "titulo", "tipo", "zona", "precio", "area", "descripcion", "estado", "imagen", "habitaciones", "banos", "antiguedad", "latitud", "longitud", "agente_id", "categoria_id", "propietario_id"] },

  { name: "Categoria", x: 85, y: 210, w: 220, attrs: ["id", "nombre", "descripcion"] },
  { name: "Propietario", x: 85, y: 405, w: 220, attrs: ["id", "nombre", "telefono", "email", "direccion"] },
  { name: "RegistroActividad", x: 890, y: 70, w: 270, attrs: ["id", "usuario_id", "nombre", "correo", "rol", "accion", "descripcion", "ip", "fecha_hora"] },

  { name: "SolicitudVisita", x: 50, y: 710, w: 265, attrs: ["id", "propiedad_id", "cliente_id", "fecha_solicitada", "mensaje", "estado"] },
  { name: "Contrato", x: 365, y: 735, w: 260, attrs: ["id", "propiedad_id", "cliente_id", "agente_id", "tipo_contrato", "monto", "fecha_inicio", "fecha_fin", "estado"] },
  { name: "Resena", x: 690, y: 710, w: 255, attrs: ["id", "propiedad_id", "cliente_id", "puntuacion", "comentario", "fecha"] },
  { name: "Prospecto", x: 990, y: 705, w: 270, attrs: ["id", "agente_id", "nombre", "telefono", "email", "propiedad_id", "etapa", "notas", "fecha_contacto"] },

  { name: "Seguimiento", x: 55, y: 1050, w: 270, attrs: ["id", "agente_id", "cliente_id", "tipo_contacto", "descripcion", "fecha"] },
  { name: "Notificacion", x: 365, y: 1060, w: 270, attrs: ["id", "usuario_id", "propiedad_id", "tipo", "mensaje", "leida", "fecha_envio"] },
  { name: "Recomendacion", x: 690, y: 1060, w: 285, attrs: ["id", "cliente_id", "propiedad_id", "puntuacion_recomendacion", "vista", "fecha_recomendacion"] },
  { name: "Prediccion", x: 1010, y: 1035, w: 290, attrs: ["id", "propiedad_id", "zona", "tipo_propiedad", "probabilidad_venta", "dias_estimados_venta", "tendencia", "fecha_prediccion"] },
  { name: "HistorialCliente", x: 500, y: 1340, w: 285, attrs: ["id", "cliente_id", "propiedad_id", "accion", "fecha_accion"] },
];

const byName = Object.fromEntries(classes.map((item) => [item.name, item]));

function boxHeight(item) {
  return 42 + item.attrs.length * 20 + 14;
}

function classBox(item) {
  const h = boxHeight(item);
  const headerH = 42;
  const attrsY = item.y + headerH + 22;
  return [
    `<rect x="${item.x}" y="${item.y}" width="${item.w}" height="${h}" fill="#ffffff" stroke="#000000" stroke-width="1.4"/>`,
    `<line x1="${item.x}" y1="${item.y + headerH}" x2="${item.x + item.w}" y2="${item.y + headerH}" stroke="#000000" stroke-width="1.1"/>`,
    `<text x="${item.x + item.w / 2}" y="${item.y + 27}" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" font-weight="700" fill="#000000">${esc(item.name)}</text>`,
    ...item.attrs.map((attr, index) =>
      `<text x="${item.x + 12}" y="${attrsY + index * 20}" font-family="Arial, sans-serif" font-size="14" fill="#000000">- ${esc(attr)}</text>`,
    ),
  ].join("\n");
}

function point(name, side) {
  const item = byName[name];
  const h = boxHeight(item);
  const cx = item.x + item.w / 2;
  const cy = item.y + h / 2;
  if (side === "top") return { x: cx, y: item.y };
  if (side === "bottom") return { x: cx, y: item.y + h };
  if (side === "left") return { x: item.x, y: cy };
  return { x: item.x + item.w, y: cy };
}

function relation(from, to, label, fromSide, toSide, controls = []) {
  const a = point(from, fromSide);
  const b = point(to, toSide);
  let d;
  if (controls.length === 2) {
    d = `M${a.x} ${a.y} C${controls[0][0]} ${controls[0][1]}, ${controls[1][0]} ${controls[1][1]}, ${b.x} ${b.y}`;
  } else if (controls.length === 1) {
    d = `M${a.x} ${a.y} Q${controls[0][0]} ${controls[0][1]} ${b.x} ${b.y}`;
  } else {
    d = `M${a.x} ${a.y} L${b.x} ${b.y}`;
  }
  const lx = controls.length ? controls[Math.floor(controls.length / 2)][0] : (a.x + b.x) / 2;
  const ly = controls.length ? controls[Math.floor(controls.length / 2)][1] - 8 : (a.y + b.y) / 2 - 8;
  const labelWidth = Math.max(70, label.length * 6.1);
  return [
    `<path d="${d}" fill="none" stroke="#000000" stroke-width="1.05" marker-end="url(#arrow)"/>`,
    `<rect x="${lx - labelWidth / 2}" y="${ly - 13}" width="${labelWidth}" height="17" fill="#ffffff" opacity="0.92"/>`,
    `<text x="${lx}" y="${ly}" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" fill="#000000">${esc(label)}</text>`,
  ].join("\n");
}

const svg = [
  `<?xml version="1.0" encoding="UTF-8"?>`,
  `<svg xmlns="http://www.w3.org/2000/svg" width="1350" height="1600" viewBox="0 0 1350 1600">`,
  `<defs><marker id="arrow" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L10,4 L0,8 Z" fill="#000000"/></marker></defs>`,
  `<rect width="100%" height="100%" fill="#ffffff"/>`,
  `<text x="675" y="42" text-anchor="middle" font-family="Times New Roman, serif" font-size="28" font-weight="700" fill="#000000">DIAGRAMA DE DISEÑO LÓGICO ACTUALIZADO</text>`,

  relation("Usuario", "Propiedad", "1 gestiona 0..*", "bottom", "top"),
  relation("Usuario", "RegistroActividad", "1 genera 0..*", "right", "left", [[815, 120]]),
  relation("Categoria", "Propiedad", "1 clasifica 0..*", "right", "left", [[365, 315]]),
  relation("Propietario", "Propiedad", "1 posee 0..*", "right", "left", [[365, 475]]),

  relation("Propiedad", "SolicitudVisita", "1 recibe 0..*", "bottom", "top", [[260, 690]]),
  relation("Usuario", "SolicitudVisita", "1 solicita 0..*", "left", "top", [[180, 515], [180, 690]]),
  relation("Propiedad", "Contrato", "1 tiene 0..*", "bottom", "top", [[520, 700]]),
  relation("Usuario", "Contrato", "1 firma/administra 0..*", "bottom", "top", [[585, 650]]),
  relation("Propiedad", "Resena", "1 recibe 0..*", "bottom", "top", [[815, 700]]),
  relation("Usuario", "Resena", "1 escribe 0..*", "right", "top", [[840, 500], [820, 690]]),
  relation("Propiedad", "Prospecto", "1 asociada a 0..*", "right", "top", [[1030, 650]]),
  relation("Usuario", "Prospecto", "1 registra 0..*", "right", "top", [[1120, 350], [1120, 690]]),

  relation("Usuario", "Seguimiento", "1 realiza/recibe 0..*", "left", "top", [[120, 390], [190, 1025]]),
  relation("Usuario", "Notificacion", "1 recibe 0..*", "bottom", "top", [[500, 1015]]),
  relation("Propiedad", "Notificacion", "1 genera 0..*", "bottom", "top", [[500, 950]]),
  relation("Usuario", "Recomendacion", "1 recibe 0..*", "bottom", "top", [[710, 1015]]),
  relation("Propiedad", "Recomendacion", "1 recomendada en 0..*", "bottom", "top", [[760, 950]]),
  relation("Propiedad", "Prediccion", "1 tiene 0..*", "right", "top", [[1150, 900]]),
  relation("Usuario", "HistorialCliente", "1 genera 0..*", "bottom", "top", [[600, 1260]]),
  relation("Propiedad", "HistorialCliente", "1 registra 0..*", "bottom", "top", [[670, 1260]]),

  ...classes.map(classBox),
  `</svg>`,
].join("\n");

fs.writeFileSync(path.join(outputDir, "diagrama_logico_normal.svg"), svg, "utf8");
