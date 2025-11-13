# Guía: Uso de Fecha/Hora Automática

## 🎯 Objetivos
- Usar hora/fecha local automáticamente
- Evitar enviar datos de tiempo desde cliente
- Mantener consistencia en toda la aplicación
- Facilitar auditoría y bitácora

---

## 📍 Helper Disponible: FechaHoraHelper

### Ubicación
```
app/Helpers/FechaHoraHelper.php
```

### Métodos disponibles

#### 1. Fecha actual
```php
use App\Helpers\FechaHoraHelper;

$fecha = FechaHoraHelper::fechaHoy();
// Resultado: "2025-11-12"
```

#### 2. Hora actual
```php
$hora = FechaHoraHelper::horaActual();
// Resultado: "21:45"

$horaConSegundos = FechaHoraHelper::horaActualConSegundos();
// Resultado: "21:45:32"
```

#### 3. Obtener objeto Carbon (para manipulaciones)
```php
$ahora = FechaHoraHelper::ahora();
// Resultado: Carbon instance con hora actual

// Luego puedes manipular:
$ahora->addDays(1)->format('Y-m-d');
```

#### 4. Fecha legible
```php
$legible = FechaHoraHelper::fechaLegible();
// Resultado: "12 de noviembre de 2025"
```

#### 5. Día de la semana
```php
$dia = FechaHoraHelper::diaSemana();
// Resultado: "miércoles"
```

#### 6. Verificar si es día laborable
```php
if (FechaHoraHelper::esLaborable()) {
    // Es lunes a viernes
}
```

#### 7. Minutos desde una hora
```php
$minutos = FechaHoraHelper::minutosDesde('14:30');
// Resultado: 195 (minutos desde las 14:30)
```

#### 8. Verificar si una hora pasó
```php
$paso = FechaHoraHelper::horaPasada('18:00');
// Resultado: true o false
```

#### 9. Minutos hasta una hora
```php
$hasta = FechaHoraHelper::minutosHasta('22:00');
// Resultado: 15 (minutos faltantes)
```

---

## 💻 Ejemplos en Controladores

### Ejemplo 1: Registrar asistencia sin enviar hora/fecha
```php
<?php

namespace App\Http\Controllers\ControlSeguimiento;

use App\Http\Controllers\Controller;
use App\Helpers\FechaHoraHelper;
use App\Models\ControlSeguimiento\Asistencia;

class AsistenciaController extends Controller
{
    public function registrar(Request $request)
    {
        // El cliente solo envía:
        $validated = $request->validate([
            'id_docente' => 'required|exists:docente,id_docente',
            'id_horario' => 'required|exists:horario,id_horario',
            'qr_aula_validada' => 'required'
        ]);

        // El servidor automáticamente agrega:
        Asistencia::create([
            'id_docente' => $validated['id_docente'],
            'id_horario' => $validated['id_horario'],
            'fecha' => FechaHoraHelper::fechaHoy(),        // ← Automático
            'hora_marcado' => FechaHoraHelper::horaActual(), // ← Automático
            'estado' => 'Presente'
        ]);

        return response()->json([
            'message' => 'Asistencia registrada a las ' . FechaHoraHelper::horaActual()
        ]);
    }
}
```

### Ejemplo 2: Validar horario
```php
public function validarAsistenciaATraso($horario)
{
    $horaInicio = Carbon::parse($horario->hora_inicio);
    $ahora = FechaHoraHelper::ahora();
    $minutos = FechaHoraHelper::minutosDesde($horario->hora_inicio);

    if ($minutos > 10) {
        return 'Atrasado';
    }
    
    return 'Presente';
}
```

### Ejemplo 3: Generar reporte
```php
public function reporteDia()
{
    $fecha = FechaHoraHelper::fechaHoy();
    $dia = FechaHoraHelper::diaSemana();
    
    $asistencias = Asistencia::whereDate('fecha', $fecha)->get();
    
    return response()->json([
        'fecha' => $fecha,
        'dia' => $dia,
        'total_registros' => $asistencias->count()
    ]);
}
```

---

## 🗄️ Uso en Vistas (Blade)

### Mostrar fecha formateada
```blade
@php
use App\Helpers\FechaHoraHelper;
@endphp

<p>Hoy es: {{ FechaHoraHelper::fechaLegible() }}</p>
<!-- Resultado: Hoy es: 12 de noviembre de 2025 -->

<p>Día: {{ FechaHoraHelper::diaSemana() }}</p>
<!-- Resultado: Día: miércoles -->
```

### Mostrar hora actual
```blade
<p>Hora actual: {{ FechaHoraHelper::horaActual() }}</p>
<!-- Resultado: Hora actual: 21:45 -->
```

### Verificar disponibilidad
```blade
@if (FechaHoraHelper::esLaborable())
    <p>Es un día laborable</p>
@else
    <p>Es fin de semana</p>
@endif
```

---

## 🔄 Uso en Modelos

### Extender BaseModel
```php
<?php

namespace App\Models\ControlSeguimiento;

use App\Models\BaseModel;

class Asistencia extends BaseModel
{
    protected $table = 'asistencia';
    
    // Automáticamente hereda métodos de fecha/hora
}

// Uso:
$asistencia = Asistencia::find(1);
echo $asistencia->fecha_formateada;     // "12/11/2025"
echo $asistencia->hora_formateada;      // "21:45"
echo $asistencia->fecha_hora_formateada; // "12/11/2025 21:45"
echo $asistencia->hace_tiempo;          // "hace 2 horas"
```

---

## 🛡️ Ventajas de este enfoque

✅ **Seguridad:** Cliente no puede manipular fecha/hora
✅ **Consistencia:** Todos los registros usan servidor como fuente de verdad
✅ **Simplificidad:** No hay que enviar datos de tiempo desde cliente
✅ **Auditoría:** Bitácora siempre tiene timestamps reales
✅ **Facilidad:** Un solo lugar para configurar timezone

---

## ⚙️ Configuración Global

### Archivo: `config/app.php`
```php
'timezone' => 'America/La_Paz',  // Zona horaria aplicada globalmente
```

Todos los métodos FechaHoraHelper usan esta zona automáticamente.

---

## 🔗 Relación con Bitácora

Cuando se registra una asistencia:

1. **Cliente envía:** id_docente, id_horario, qr
2. **Servidor agrega:** fecha, hora (usando FechaHoraHelper)
3. **BD guarda:** asistencia con timestamps
4. **Bitácora registra:** 
   - Acción: "POST /control-seguimiento/asistencia"
   - Timestamp: hora real del servidor
   - Usuario: autenticado

**Resultado:** Auditoría 100% confiable con timestamps reales

---

## 📝 Checklist para nuevas funcionalidades

- [ ] ¿Necesita fecha/hora? → Usar FechaHoraHelper
- [ ] ¿Es manipulable desde cliente? → Usar automático en servidor
- [ ] ¿Se debe auditar? → Bitácora lo registra automáticamente
- [ ] ¿Timezone correcto? → Verificar en config/app.php
- [ ] ¿Formato correcto? → Usar métodos del helper

