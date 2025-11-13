# 📱 GUÍA VISUAL - CÓMO USAR LA APP

## 🎯 INICIO RÁPIDO

### Acceso a la Aplicación
```
URL: http://localhost:8000/
     (o tu dominio en producción)

Login:
  Usuario: [tu usuario]
  Contraseña: [tu contraseña]
```

---

## 🔐 GENERADOR DE QR - PASO A PASO

### 1️⃣ Entrar al Generador QR
```
Navegación:
  Menú izquierdo 
    → Planificación 
      → Generador de QR
```

### 2️⃣ Pantalla Principal (Escritorio)
```
┌─────────────────────────────────────────────────────┐
│  🔐 Generador de Códigos QR       [Generar Todos] │
├─────────────────────────────────────────────────────┤
│  Filtros:                                            │
│  Piso: [▼] | Tipo: [▼] | Estado: [▼]              │
├─────────────────────────────────────────────────────┤
│  ☐ | Aula | Piso | Tipo | Cap | GPS | Estado | ⚙️ │
│  ☐ | A101 |  1   | Lab  | 80  | Sí  | ✓     | ⚙️ │
│  ☐ | A102 |  1   | Teor | 120 | Sí  | ⏳    | ⚙️ │
│  ☐ | A103 |  2   | Lab  | 80  | No  | ⏳    | ⚙️ │
└─────────────────────────────────────────────────────┘
```

### 3️⃣ Generar QR para UNA Aula

**Opción A: Usar botón de fila**
```
1. En la tabla, busca el aula
2. Click en botón 📋 (QR)
3. Confirma en el diálogo
4. Espera a que se genere (2-3 segundos)
5. Verás: "✓ QR generado exitosamente"
```

**Opción B: Ver y descargar QR**
```
1. Click en botón 👁️ (Ver)
2. Se abrirá un modal con el código QR
3. Click en botón "Descargar" para guardar SVG
4. El archivo se descargará como: qr-aula-A101.svg
```

### 4️⃣ Generar QR para TODAS las Aulas

```
1. Click en botón "Generar Todos" (arriba derecha)
2. Confirma: "¿Generar QR para TODAS las aulas?"
3. Espera (puede tardar 30-60 segundos)
4. Recibirás: "✓ 50 QR generados exitosamente"
5. Tabla se actualiza automáticamente
```

### 5️⃣ Regenerar QR (Invalidar antiguo)

```
1. En la tabla, busca el aula
2. Click en botón 🔄 (Regenerar)
3. Confirma: "¿Regenerar QR? El anterior se invalida"
4. Genera nuevo token
5. El QR anterior DEJA DE FUNCIONAR
```

### 6️⃣ Filtrar Aulas

```
Piso:   [▼ Todos los pisos]
  → Selecciona: Primer Piso, Segundo Piso, etc.
  → Tabla se actualiza automáticamente

Tipo:   [▼ Todos los tipos]
  → Selecciona: Laboratorio, Teoría, Seminario, Práctico
  
Estado: [▼ Todos]
  → Generados: Solo muestra aulas con QR
  → No Generados: Solo muestra aulas sin QR
```

### 7️⃣ Descargar Múltiples QR

**Descargar seleccionados:**
```
1. Click en checkboxes de aulas deseadas
2. Click en botón "Descargar Seleccionados (ZIP)"
3. Se descarga: qr_aulas.zip con los QR
4. Descomprime para obtener archivos SVG
```

**Descargar TODOS:**
```
1. Click en botón "Descargar Todos (ZIP)"
2. Se descarga: qr_todas_aulas.zip
3. Contiene todos los QR en formato SVG
```

**Descargar PDF imprimible:**
```
1. Click en botón "PDF"
2. Se genera documento con todos los QR
3. Puedes imprimir directamente
```

---

## 📱 EN DISPOSITIVO MÓVIL

### Pantalla Móvil (375px)
```
┌──────────────────────┐
│ Generador QR         │
│ [Generar]            │
├──────────────────────┤
│ Filtros:             │
│ Piso: [▼]           │
│ Tipo: [▼]           │
│ Estado: [▼]         │
├──────────────────────┤
│ ☐ | Aula | Estado    │
│ ☐ | A101 | ✓         │
│ ☐ | A102 | ⏳        │
│ ☐ | A103 | ⏳        │
│                      │
│ [Descargar]          │
│ [Regenerar]          │
└──────────────────────┘
```

### Usar en Móvil
```
✓ Todo es táctil (finger-friendly)
✓ Botones grandes para presionar
✓ Texto legible en pantalla pequeña
✓ Scroll vertical para ver más
✓ Modales adaptados al ancho
```

---

## ⚠️ VALIDACIÓN DE HORARIOS - CÓMO FUNCIONA

### Crear Horario SIN Conflictos ✅

```
Formulario:
  Grupo: [1A]
  Aula: [A101]
  Día: [Lunes]
  Hora Inicio: [09:00]
  Hora Fin: [10:00]

✓ Se guarda exitosamente
✓ Aparece en la tabla de horarios
```

### Crear Horario CON Conflicto ❌

**Conflicto 1: Aula Ocupada**
```
Intento:
  Aula: A101
  Lunes 09:00-10:00
  
Problema:
  Ya existe: Lunes 09:30-10:30 en A101

Resultado:
┌─────────────────────────────────────┐
│ ❌ Error: Conflictos de Horario     │
├─────────────────────────────────────┤
│ Aula: El aula ya está ocupada       │
│ Detalles:                           │
│ • Aula: A101                        │
│ • Materia: Física                   │
│ • Grupo: 1B                         │
│ • Horario: 09:30 - 10:30            │
│                                     │
│ [Aceptar]                           │
└─────────────────────────────────────┘
```

**Conflicto 2: Docente Ocupado**
```
Intento:
  Docente: Juan Pérez
  Lunes 14:00-15:00
  
Problema:
  Juan ya dicta: Lunes 14:30-15:30

Resultado:
┌─────────────────────────────────────┐
│ ❌ Error: Conflictos de Horario     │
├─────────────────────────────────────┤
│ Docente: Ya tiene clase en horario  │
│ Detalles:                           │
│ • Docente: Juan Pérez               │
│ • Materia: Matemáticas              │
│ • Aula: A203                        │
│ • Horario: 14:30 - 15:30            │
│                                     │
│ [Aceptar]                           │
└─────────────────────────────────────┘
```

**Conflicto 3: Grupo Ocupado**
```
Intento:
  Grupo: 2C
  Lunes 15:00-16:00
  
Problema:
  Grupo 2C ya tiene: Lunes 15:00-16:00

Resultado:
┌─────────────────────────────────────┐
│ ❌ Error: Conflictos de Horario     │
├─────────────────────────────────────┤
│ Grupo: Ya tiene clase en horario    │
│ Detalles:                           │
│ • Grupo: 2C                         │
│ • Materia: Química                  │
│ • Aula: A305                        │
│ • Horario: 15:00 - 16:00            │
│                                     │
│ [Aceptar]                           │
└─────────────────────────────────────┘
```

---

## 🎓 FLUJO COMPLETO

### Caso de Uso: Crear un Nuevo Horario

```
PASO 1: Acceder
  Menú → Planificación → Horarios → Nuevo

PASO 2: Llenar Formulario
  Grupo: 1A ✓
  Aula: A101 ✓
  Día: Lunes ✓
  Hora: 09:00 - 10:00 ✓

PASO 3: Validación
  Sistema valida automáticamente:
  ✓ ¿Aula libre? → SÍ
  ✓ ¿Docente libre? → SÍ
  ✓ ¿Grupo libre? → SÍ

PASO 4: Guardar
  Click en "Guardar"
  ✓ Horario creado exitosamente
  ✓ Aparece en tabla de horarios
  ✓ Se registra en bitácora

PASO 5: Verificar
  Tabla muestra:
  | 1A | A101 | Lunes | 09:00-10:00 | ✓ |
```

---

## 📊 LECTURA DE LA TABLA

```
┌─────┬──────┬──────┬──────┬──────┬──────┬────────┬──────┐
│ ☑  │ Aula │ Piso │ Tipo │ Cap  │ GPS  │ Estado │ ⚙️   │
├─────┼──────┼──────┼──────┼──────┼──────┼────────┼──────┤
│ ☐  │ A101 │  1   │ Lab  │ 80   │ Sí   │ ✓      │ ⚙️   │
│ ☑  │ A102 │  1   │ Teor │ 120  │ Sí   │ ⏳     │ ⚙️   │
│ ☐  │ A103 │  2   │ Lab  │ 80   │ No   │ ⏳     │ ⚙️   │
└─────┴──────┴──────┴──────┴──────┴──────┴────────┴──────┘

Significados:
  ☐ / ☑  = Checkbox (seleccionar para operaciones masivas)
  ✓      = QR ya generado
  ⏳     = QR pendiente de generar
  ⚙️     = Botones de acción (Ver, Generar, Regenerar)
```

---

## 🔧 BOTONES Y SUS FUNCIONES

| Botón | Nombre | Función |
|-------|--------|---------|
| 📋 | QR | Generar QR para esa aula |
| 👁️ | Ver | Ver y descargar el QR |
| 🔄 | Regenerar | Generar nuevo QR (invalida anterior) |
| 💾 | Guardar | Guardar cambios (gris = deshabilitado) |
| ✏️ | Editar | Modificar horario |
| 🗑️ | Eliminar | Borrar horario (con confirmación) |

---

## 🎨 INDICADORES VISUALES

```
Badge/Estado:
  ✓ VERDE = Completado/Generado
  ⏳ AMARILLO = Pendiente/En proceso
  ✗ ROJO = Error/Conflicto
  ℹ️ AZUL = Información
```

---

## 📞 SOLUCIÓN DE PROBLEMAS

### "Error al generar QRs"
```
Solución:
  1. Recarga la página (Ctrl+F5)
  2. Intenta de nuevo
  3. Si persiste, contacta soporte
```

### "QR no se genera pero no hay error"
```
Solución:
  1. Espera 30 segundos (podría estar procesando)
  2. Revisa la consola (F12 → Console)
  3. Copia el error y comparte
```

### "Tabla no se actualiza"
```
Solución:
  1. Recarga la página completa
  2. Limpia el cache del navegador
  3. Cierra la pestaña y abre de nuevo
```

### "Conflicto no detectado"
```
Solución:
  1. Recarga la página
  2. Intenta crear el horario de nuevo
  3. Verifica que todos los datos sean correctos
```

---

## 💡 CONSEJOS Y TRUCOS

✅ **Generar todos los QR de una vez**
   - Es más eficiente que generar uno por uno
   - Tarda ~1 minuto para 100 aulas

✅ **Descargar en ZIP**
   - Es más práctico que descargar uno a uno
   - Se descargan en formato SVG (escalable)

✅ **Usar filtros**
   - Filtra por piso para generar por niveles
   - Filtra por tipo para organizar mejor

✅ **Verificar conflictos**
   - Sistema valida automáticamente
   - No necesitas hacer nada, está integrado

✅ **Esperar carga**
   - La generación de 50+ QR puede tardar 30-60s
   - No cierres la ventana ni presiones el botón nuevamente

---

## 🚀 ACCIONES RÁPIDAS

### En Desktop
```
Ctrl+F5     = Recargar todo (limpiar caché)
F12         = Abrir consola (para ver errores)
Ctrl+Shift+I = Modo responsive (ver en móvil)
```

### En Móvil
```
Doble tap   = Zoom in/out
Swipe izq   = Volver
Swipe der   = Adelante
```

---

## 📧 CONTACTAR SOPORTE

Si tienes problemas:

1. **Toma screenshot del error**
2. **Anota la hora exacta**
3. **Copia el error del navegador (F12)**
4. **Contacta al equipo técnico con esta info**

```
Información útil:
  • Navegador: Chrome, Firefox, Safari
  • Dispositivo: Desktop, Tablet, Móvil
  • Sistema operativo: Windows, Mac, Linux
  • Hora del error: [exacta]
  • Pasos para reproducir: [detallados]
```

---

**¡Listo! Ahora puedes usar la aplicación correctamente.** 🎉

**Versión:** 1.0.0  
**Última actualización:** 13 de noviembre de 2025

