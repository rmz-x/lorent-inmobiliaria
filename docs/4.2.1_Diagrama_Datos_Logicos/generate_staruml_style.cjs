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
  { name: "Usuario", x: 500, y: 70, w: 280, attrs: ["id", "nombre", "correo", "usuario", "contrasena", "rol", "estado"] },
  { name: "Propiedad", x: 475, y: 360, w: 330, attrs: ["id", "titulo", "tipo", "zona", "precio", "area", "descripcion", "estado", "imagen", "habitaciones", "banos", "antiguedad", "latitud", "longitud", "agente_id", "categoria_id", "propietario_id"] },

  { name: "Categoria", x: 80, y: 310, w: 235, attrs: ["id", "nombre", "descripcion"] },
  { name: "Propietario", x: 80, y: 510, w: 235, attrs: ["id", "nombre", "telefono", "email", "direccion"] },
  { name: "RegistroActividad", x: 955, y: 210, w: 280, attrs: ["id", "usuario_id", "nombre", "correo", "rol", "accion", "descripcion", "ip", "fecha_hora"] },

  { name: "SolicitudVisita", x: 60, y: 780, w: 280, attrs: ["id", "propiedad_id", "cliente_id", "fecha_solicitada", "mensaje", "estado"] },
  { name: "Contrato", x: 500, y: 810, w: 280, attrs: ["id", "propiedad_id", "cliente_id", "agente_id", "tipo_contrato", "monto", "fecha_inicio", "fecha_fin", "estado"] },
  { name: "Resena", x: 950, y: 780, w: 270, attrs: ["id", "propiedad_id", "cliente_id", "puntuacion", "comentario", "fecha"] },

  { name: "Prospecto", x: 85, y: 1120, w: 285, attrs: ["id", "agente_id", "nombre", "telefono", "email", "propiedad_id", "etapa", "notas", "fecha_contacto"] },
  { name: "Seguimiento", x: 920, y: 1120, w: 285, attrs: ["id", "agente_id", "cliente_id", "tipo_contacto", "descripcion", "fecha"] },

  { name: "Notificacion", x: 55, y: 1500, w: 285, attrs: ["id", "usuario_id", "propiedad_id", "tipo", "mensaje", "leida", "fecha_envio"] },
  { name: "Recomendacion", x: 500, y: 1510, w: 300, attrs: ["id", "cliente_id", "propiedad_id", "puntuacion_recomendacion", "vista", "fecha_recomendacion"] },
  { name: "Prediccion", x: 930, y: 1500, w: 310, attrs: ["id", "propiedad_id", "zona", "tipo_propiedad", "probabilidad_venta", "dias_estimados_venta", "tendencia", "fecha_prediccion"] },
  { name: "HistorialCliente", x: 495, y: 1840, w: 300, attrs: ["id", "cliente_id", "propiedad_id", "accion", "fecha_accion"] },
];

const byName = Object.fromEntries(classes.map((item) => [item.name, item]));

function boxHeight(item) {
  return 42 + item.attrs.length * 22 + 14;
}

function classBox(item) {
  const h = boxHeight(item);
  const headerH = 42;
  const attrsY = item.y + headerH + 24;
  return [
    `<rect x="${item.x}" y="${item.y}" width="${item.w}" height="${h}" fill="#ffffff" stroke="#000000" stroke-width="1.6"/>`,
    `<line x1="${item.x}" y1="${item.y + headerH}" x2="${item.x + item.w}" y2="${item.y + headerH}" stroke="#000000" stroke-width="1.2"/>`,
    `<text x="${item.x + item.w / 2}" y="${item.y + 27}" text-anchor="middle" font-family="Arial, sans-serif" font-size="17" font-weight="700" fill="#000000">${esc(item.name)}</text>`,
    ...item.attrs.map((attr, index) =>
      `<text x="${item.x + 14}" y="${attrsY + index * 22}" font-family="Arial, sans-serif" font-size="15" fill="#000000">- ${esc(attr)}</text>`,
    ),
  ].join("\n");
}

function centerBottom(name) {
  const item = byName[name];
  return { x: item.x + item.w / 2, y: item.y + boxHeight(item) };
}

function centerTop(name) {
  const item = byName[name];
  return { x: item.x + item.w / 2, y: item.y };
}

function leftMid(name) {
  const item = byName[name];
  return { x: item.x, y: item.y + boxHeight(item) / 2 };
}

function rightMid(name) {
  const item = byName[name];
  return { x: item.x + item.w, y: item.y + boxHeight(item) / 2 };
}

function relation(from, to, label, fromPoint = "bottom", toPoint = "top", points = [], curved = true) {
  const pick = {
    bottom: centerBottom,
    top: centerTop,
    left: leftMid,
    right: rightMid,
  };
  const a = pick[fromPoint](from);
  const b = pick[toPoint](to);
  let d;
  if (curved && points.length === 1) {
    const [cx, cy] = points[0];
    d = `M${a.x} ${a.y} Q${cx} ${cy} ${b.x} ${b.y}`;
  } else if (curved && points.length === 2) {
    const [c1, c2] = points;
    d = `M${a.x} ${a.y} C${c1[0]} ${c1[1]}, ${c2[0]} ${c2[1]}, ${b.x} ${b.y}`;
  } else {
    d = [`M${a.x} ${a.y}`, ...points.map(([x, y]) => `L${x} ${y}`), `L${b.x} ${b.y}`].join(" ");
  }
  const labelX = points.length ? points[Math.floor(points.length / 2)][0] : (a.x + b.x) / 2;
  const labelY = points.length ? points[Math.floor(points.length / 2)][1] - 8 : (a.y + b.y) / 2 - 8;
  return [
    `<path d="${d}" fill="none" stroke="#000000" stroke-width="1.1" marker-end="url(#arrow)"/>`,
    `<rect x="${labelX - label.length * 3.2}" y="${labelY - 13}" width="${label.length * 6.4}" height="17" fill="#ffffff" opacity="0.9"/>`,
    `<text x="${labelX}" y="${labelY}" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" fill="#000000">${esc(label)}</text>`,
  ].join("\n");
}

function svg() {
  const content = [
    `<?xml version="1.0" encoding="UTF-8"?>`,
    `<svg xmlns="http://www.w3.org/2000/svg" width="1280" height="2180" viewBox="0 0 1280 2180">`,
    `<defs><marker id="arrow" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto" markerUnits="strokeWidth"><path d="M0,0 L10,4 L0,8 Z" fill="#000000"/></marker></defs>`,
    `<rect width="100%" height="100%" fill="#ffffff"/>`,
    `<text x="640" y="28" text-anchor="middle" font-family="Times New Roman, serif" font-size="26" font-weight="700" fill="#000000">DIAGRAMA DE DISEÑO LÓGICO ACTUALIZADO</text>`,
    `<text x="640" y="58" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" fill="#000000">Modelo vertical estilo UML - ciclo 5</text>`,
    relation("Usuario", "Propiedad", "1 gestiona 0..*", "bottom", "top", [], false),
    relation("Categoria", "Propiedad", "1 clasifica 0..*", "right", "left", [[365, 355]]),
    relation("Propietario", "Propiedad", "1 posee 0..*", "right", "left", [[375, 570], [455, 520]]),
    relation("Usuario", "RegistroActividad", "1 genera 0..*", "right", "top", [[880, 115], [1030, 210]]),

    relation("Propiedad", "SolicitudVisita", "1 recibe 0..*", "left", "top", [[255, 710], [200, 780]]),
    relation("Usuario", "SolicitudVisita", "1 solicita 0..*", "left", "top", [[160, 180], [120, 720]]),
    relation("Propiedad", "Contrato", "1 tiene 0..*", "bottom", "top", [], false),
    relation("Usuario", "Contrato", "1 firma/administra 0..*", "bottom", "top", [[630, 520]]),
    relation("Propiedad", "Resena", "1 recibe 0..*", "right", "top", [[1010, 710], [1085, 780]]),
    relation("Usuario", "Resena", "1 escribe 0..*", "right", "top", [[1120, 180], [1090, 720]]),

    relation("Usuario", "Prospecto", "1 registra 0..*", "left", "top", [[210, 260], [225, 1120]]),
    relation("Propiedad", "Prospecto", "1 asociada a 0..*", "left", "top", [[260, 970], [227, 1120]]),
    relation("Usuario", "Seguimiento", "1 realiza/recibe 0..*", "right", "top", [[1060, 260], [1060, 1120]]),

    relation("Usuario", "Notificacion", "1 recibe 0..*", "left", "top", [[115, 520], [197, 1500]]),
    relation("Propiedad", "Notificacion", "1 genera 0..*", "left", "top", [[160, 1040], [197, 1500]]),
    relation("Usuario", "Recomendacion", "1 recibe 0..*", "bottom", "top", [[640, 980]]),
    relation("Propiedad", "Recomendacion", "1 recomendada en 0..*", "bottom", "top", [[640, 1200]]),
    relation("Propiedad", "Prediccion", "1 tiene 0..*", "right", "top", [[1120, 1030], [1085, 1500]]),
    relation("Usuario", "HistorialCliente", "1 genera 0..*", "bottom", "top", [[640, 1360]]),
    relation("Propiedad", "HistorialCliente", "1 registra 0..*", "bottom", "top", [[640, 1430]]),
    ...classes.map(classBox),
    `</svg>`,
  ];
  fs.writeFileSync(path.join(outputDir, "diagrama_logico_staruml_vertical.svg"), content.join("\n"), "utf8");
}

function starUmlMdj() {
  let id = 1;
  const nextId = (prefix) => `${prefix}_${id++}`;
  const modelId = nextId("model");
  const diagramId = nextId("diagram");
  const classIds = {};

  const classElements = classes.map((item) => {
    const classId = nextId(`class_${item.name}`);
    classIds[item.name] = classId;
    return {
      _type: "UMLClass",
      _id: classId,
      name: item.name,
      attributes: item.attrs.map((attr) => ({
        _type: "UMLAttribute",
        _id: nextId(`attr_${item.name}`),
        name: attr,
      })),
      operations: [],
    };
  });

  const views = classes.map((item) => ({
    _type: "UMLClassView",
    _id: nextId(`view_${item.name}`),
    model: { $ref: classIds[item.name] },
    x: item.x,
    y: item.y,
    width: item.w,
    height: boxHeight(item),
  }));

  const project = {
    _type: "Project",
    _id: nextId("project"),
    name: "Diagrama logico actualizado ciclo 5",
    ownedElements: [
      {
        _type: "UMLModel",
        _id: modelId,
        name: "Modelo logico actualizado ciclo 5",
        ownedElements: [
          ...classElements,
          {
            _type: "UMLClassDiagram",
            _id: diagramId,
            name: "Diagrama logico vertical",
            ownedViews: views,
          },
        ],
      },
    ],
  };

  fs.writeFileSync(
    path.join(outputDir, "staruml_diagrama_logico_ciclo5.mdj"),
    JSON.stringify(project, null, 2),
    "utf8",
  );
}

svg();
starUmlMdj();
