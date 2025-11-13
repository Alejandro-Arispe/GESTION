# Guía: Bitácora y QR de Aulas

## ⏰ CONFIGURACIÓN DE HORA Y FECHA

### Zona horaria configurada
- **Zona horaria:** America/La_Paz (Bolivia UTC-4)
- **Archivo configuración:** `config/app.php`

### ¿Cómo funciona?
- ✅ Hora/fecha se registra **automáticamente** del servidor
- ✅ No requiere input manual del usuario
- ✅ Se usa en asistencia y bitácora
- ✅ Siempre es hora real local

### Ejemplo de registro de asistencia
```
Entrada:  No requiere enviar fecha/hora
Registro: Se guarda automáticamente con hora/fecha local del servidor
Bitácora: Se registra la acción con timestamp real
```

---

## 📊 BITÁCORA DEL SISTEMA

### ¿Dónde se registra?
La bitácora automáticamente registra **todos los cambios** (POST, PUT, DELETE, PATCH) que se realicen en la plataforma.

### ¿Qué se registra?
- **Usuario:** Quién realizó la acción
- **Acción:** Ruta + método HTTP (ej: POST /usuarios, DELETE /grupos/5)
- **Descripción:** Datos enviados (sin contraseñas)
- **IP origen:** Dirección IP del cliente
- **Navegador:** User Agent del navegador
- **Tabla afectada:** Tabla de BD que se modificó
- **Registro ID:** ID específico modificado
- **Fecha/Hora:** Cuándo ocurrió

### Limitaciones actuales
- ✅ Registra POST, PUT, DELETE, PATCH
- ❌ NO registra GET (consultas normales)
- ✅ Excluye campos sensibles (passwords)
- ✅ Solo registra usuarios autenticados

### Mejora recomendada
Para ver más detalles en tiempo real, necesitas:
1. Crear una vista en el panel de administración
2. Ejecutar: `php artisan make:controller Administracion\BitacoraController`
3. Agregar la ruta a `routes/web.php`

---

## 🔐 QR DE AULAS

### ¿Dónde los encuentro?
Los QR están en la sección **Planificación → Generador de QR**

**URL directo:**
```
http://127.0.0.1:8000/planificacion/generador-qr
```

### ¿Qué puedes hacer con los QR?

#### 1. **Ver todas las aulas**
```
GET /api/qr-aula/listar
```
Retorna lista de aulas con estado de QR generado

#### 2. **Generar QR para una aula**
```
POST /api/qr-aula/generar/{idAula}
```
Ejemplo: `POST /api/qr-aula/generar/1`

#### 3. **Generar QR para TODAS las aulas**
```
POST /api/qr-aula/generar-todos
```

#### 4. **Descargar QR individual**
```
GET /api/qr-aula/{idAula}/descargar
```
Descarga el QR como SVG

#### 5. **Descargar múltiples QR como ZIP**
```
GET /api/qr-aula/descargar-zip?aulas=1,2,3,4
```

#### 6. **Descargar TODOS los QR**
```
GET /api/qr-aula/descargar-zip-todos
```

#### 7. **Regenerar QR (cambiar token)**
```
POST /api/qr-aula/regenerar/{idAula}
```

#### 8. **Ver QR en navegador**
```
GET /api/qr-aula/{idAula}/mostrar
```

#### 9. **Validar QR escaneado**
```
POST /api/qr-aula/validar
Body: {"codigo_qr_leido": "..."}
```

### Estructura del QR
Cada QR contiene JSON:
```json
{
  "id_aula": 1,
  "nro_aula": "A101",
  "token": "abc123xyz789...",
  "generado_en": "2025-11-12T21:00:00Z"
}
```

### Flujo de uso (Asistencia)
1. Docente escanea QR del aula
2. Se valida el QR contra la BD
3. Si es válido, se registra la asistencia
4. Sistema verifica:
   - ✅ QR válido
   - ✅ Aula correcta
   - ✅ Hora (presente/atrasado)
   - ✅ GPS (si está configurado)

### Datos guardados en BD (tabla: qr_aula)
- `id_qr_aula` - ID único
- `id_aula` - Referencia al aula
- `codigo_qr` - SVG del código QR
- `token` - Token único para validación
- `created_at` - Fecha de creación

---

## 🔧 CONFIGURACIÓN RECOMENDADA

### Para mejorar la Bitácora
1. Crear controlador de bitácora:
```bash
php artisan make:controller Administracion\BitacoraController
```

2. Agregar ruta en `routes/web.php`:
```php
Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
Route::get('bitacora/filtrar', [BitacoraController::class, 'filtrar'])->name('bitacora.filtrar');
```

3. Crear vista en `resources/views/administracion/bitacora.blade.php`

### Para mejorar los QR
1. Agregar ubicación GPS en aulas
2. Configurar radio de validación en config/app.php
3. Crear vista bonita para escaneo

---

## 📈 ESTADÍSTICAS

### Tabla: bitacora
```sql
SELECT COUNT(*) as total_movimientos,
       COUNT(DISTINCT id_usuario) as usuarios_activos,
       COUNT(DISTINCT DATE(created_at)) as dias_activos
FROM bitacora
WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Acciones más frecuentes
```sql
SELECT accion, COUNT(*) as cantidad
FROM bitacora
GROUP BY accion
ORDER BY cantidad DESC
LIMIT 10;
```

---

## ✅ Estado Actual
- ✅ Bitácora registra todos los cambios
- ✅ QR generados y funcionales
- ✅ Validación de QR implementada
- ✅ Sistema de asistencia usando QR
- ✅ Descarga de QR en múltiples formatos
- ✅ Hora/fecha **automática** con timezone local
- ✅ Bitácora con timestamp real

---

## 🔧 FUNCIONAMIENTO AUTOMÁTICO DE HORA/FECHA

### En registro de asistencia
```javascript
// NO es necesario enviar estas líneas desde el cliente
fecha: "2025-11-12"      // Se obtiene automáticamente
hora_marcado: "21:45"    // Se obtiene automáticamente

// El servidor automáticamente usa:
const ahora = Carbon::now(); // Hora actual del servidor
```

### En tabla asistencia
| Campo | Valor | Origen |
|-------|-------|--------|
| fecha | 2025-11-12 | Automático (server) |
| hora_marcado | 21:45 | Automático (server) |
| created_at | 2025-11-12 21:45:32 | Automático (database) |

### En tabla bitacora
| Campo | Valor | Origen |
|-------|-------|--------|
| accion | POST /control-seguimiento/asistencia | Usuario |
| created_at | 2025-11-12 21:45:35 | Automático (database) |
| ip_origen | 127.0.0.1 | Automático (server) |
| navegador | Mozilla/5.0... | Automático (request) |

### Ventajas
✅ No hay desincronización de hora cliente-servidor
✅ Imposible manipular la fecha/hora desde cliente
✅ Todos los registros con hora real consistente
✅ Auditoría confiable en bitácora

---

## 🌍 INFORMACIÓN DE ZONA HORARIA

**Zona actual:** America/La_Paz (Bolivia)
- UTC-4 (sin horario de verano)
- No cambia a UTC-3

Si necesitas cambiar a otra zona:
```php
// En config/app.php, línea 68
'timezone' => 'America/La_Paz'  // Cambiar a:
'timezone' => 'America/Denver'   // UTC-6
'timezone' => 'America/New_York' // UTC-5
'timezone' => 'UTC'              // UTC
```

Luego ejecutar:
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📱 API: REGISTRO AUTOMÁTICO

### Antes (requerimiento manual)
```json
{
  "id_docente": 1,
  "id_horario": 5,
  "fecha": "2025-11-12",        // ← Antes requerido
  "hora_marcado": "21:45",       // ← Antes requerido
  "qr_aula_validada": "1"
}
```

### Ahora (automático)
```json
{
  "id_docente": 1,
  "id_horario": 5,
  "qr_aula_validada": "1"
  // fecha y hora_marcado se agregan automáticamente
}
```

### Respuesta del servidor
```json
{
  "message": "Asistencia registrada exitosamente a las 21:45",
  "asistencia": {
    "id": 123,
    "fecha": "2025-11-12",
    "hora_marcado": "21:45"
  }
}
```

