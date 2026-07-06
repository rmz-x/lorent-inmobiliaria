Pasos:

1. Abre el archivo:
[generate_diagrams.cjs](D:/PROYECTO-V7.0/docs/diagramas_comunicacion_ciclo5/generate_diagrams.cjs)

2. Cambia el título que quieras, por ejemplo:

title: "CU26 Gestionar asistente de voz IA",
por:
title: "CU26 Gestionar asistente inteligente por voz",

3. Guarda el archivo.

4. En PowerShell, desde la raíz del proyecto, ejecuta:

node docs/diagramas_comunicacion_ciclo5/generate_diagrams.cjs

5. Eso vuelve a generar los SVG en la misma carpeta:
docs/diagramas_comunicacion_ciclo5/

6. Abre el .svg que cambiaste con doble clic, o insértalo en Word:
CU26_gestionar_asistente_voz_ia.svg

Importante: cada vez que edites el .cjs, vuelve a ejecutar el comando node ... para que el SVG se actualice.



Sí, eso es PowerShell. Para ir a la raíz de tu proyecto escribe esto y presiona Enter:

cd "D:\PROYECTO-V7.0"

Te debería quedar así:

PS D:\PROYECTO-V7.0>

Luego ejecutas el generador:

node docs/diagramas_comunicacion_ciclo5/generate_diagrams.cjs

Y para abrir la carpeta donde están los diagramas:

explorer docs\diagramas_comunicacion_ciclo5