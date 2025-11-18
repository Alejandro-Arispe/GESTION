# Validación de Conflictos de Horarios

## Descripción General

El sistema ahora valida automáticamente **3 tipos de conflictos** al crear o modificar horarios:

1. **Conflicto de Aula**: El aula ya está ocupada en ese horario
2. **Conflicto de Docente**: El docente ya tiene clase en ese horario
3. **Conflicto de Grupo**: El grupo ya tiene clase en ese horario

---

## Endpoints de API

### 1. Validar Conflictos (ANTES de guardar)

```
POST /api/horarios/validar-conflictos
```

**Headers requeridos:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "id_grupo": 1,
  "id_aula": 5,
  "dia_semana": "Lunes",
  "hora_inicio": "08:00",
  "hora_fin": "10:00",
  "id_horario_excluir": null
}
```

**Respuesta exitosa (sin conflictos):**
```json
{
  "tiene_conflictos": false,
  "cantidad_conflictos": 0,
  "conflictos": [],
  "puede_guardar": true
}
```

**Respuesta con conflictos:**
```json
{
  "tiene_conflictos": true,
  "cantidad_conflictos": 2,
  "conflictos": [
    {
      "tipo": "aula",
      "severidad": "error",
      "titulo": "Conflicto de Aula",
      "mensaje": "El aula 101 ya está ocupada en este horario",
      "detalle": {
        "aula_ocupada": "101",
        "docente": "Ing. Juan Pérez",
        "materia": "Programación I",
        "grupo_ocupante": "1-A",
        "hora_conflicto": "08:30 - 10:30",
        "dia": "Lunes"
      }
    },
    {
      "tipo": "docente",
      "severidad": "error",
      "titulo": "Conflicto de Docente",
      "mensaje": "El docente Ing. Juan Pérez ya tiene clase en este horario",
      "detalle": {
        "docente": "Ing. Juan Pérez",
        "materia_existente": "Programación I",
        "grupo_existente": "1-A",
        "aula_existente": "101",
        "hora_conflicto": "08:30 - 10:30",
        "dia": "Lunes",
        "nueva_materia": "Programación II",
        "nueva_aula": "102"
      }
    }
  ],
  "puede_guardar": false
}
```

---

### 2. Crear Horario

```
POST /api/horarios
```

**Headers requeridos:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "id_grupo": 1,
  "id_aula": 5,
  "dia_semana": "Lunes",
  "hora_inicio": "08:00",
  "hora_fin": "10:00",
  "tipo_asignacion": "Manual"
}
```

**Respuesta exitosa:**
```json
{
  "message": "Horario creado exitosamente",
  "horario": {
    "id_horario": 123,
    "id_grupo": 1,
    "id_aula": 5,
    "dia_semana": "Lunes",
    "hora_inicio": "08:00",
    "hora_fin": "10:00",
    "tipo_asignacion": "Manual",
    "grupo": {
      "id_grupo": 1,
      "nombre": "1-A",
      "materia": { "nombre": "Programación II" },
      "docente": { "nombre": "Ing. Juan Pérez" }
    },
    "aula": { "id_aula": 5, "nro": "102" }
  }
}
```

**Respuesta con error (conflictos):**
```json
{
  "message": "Existen conflictos de horario",
  "conflictos": [
    { "tipo": "aula", "mensaje": "El aula 101 ya está ocupada..." }
  ]
}
```

---

### 3. Actualizar Horario

```
PUT /api/horarios/{id_horario}
```

**Headers requeridos:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "id_grupo": 1,
  "id_aula": 5,
  "dia_semana": "Martes",
  "hora_inicio": "09:00",
  "hora_fin": "11:00",
  "tipo_asignacion": "Manual"
}
```

---

### 4. Listar Horarios

```
GET /api/horarios
```

**Parámetros opcionales:**
```
?dia_semana=Lunes
?id_aula=5
?id_docente=3
?id_grupo=1
```

**Respuesta:**
```json
{
  "horarios": [
    {
      "id_horario": 1,
      "id_grupo": 1,
      "id_aula": 5,
      "dia_semana": "Lunes",
      "hora_inicio": "08:00",
      "hora_fin": "10:00",
      "tipo_asignacion": "Manual",
      "grupo": { "nombre": "1-A", "materia": {...}, "docente": {...} },
      "aula": { "nro": "102" }
    }
  ]
}
```

---

## Integración en Frontend

### Ejemplo 1: Validar ANTES de crear

```javascript
async function guardarHorario(formulario) {
  // Primero validar conflictos
  const validacion = await fetch('/api/horarios/validar-conflictos', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      id_grupo: formulario.id_grupo,
      id_aula: formulario.id_aula,
      dia_semana: formulario.dia_semana,
      hora_inicio: formulario.hora_inicio,
      hora_fin: formulario.hora_fin
    })
  });

  const resultado = await validacion.json();

  // Mostrar conflictos
  if (resultado.tiene_conflictos) {
    mostrarConflictos(resultado.conflictos);
    return; // No guardar
  }

  // Si no hay conflictos, guardar
  const respuesta = await fetch('/api/horarios', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(formulario)
  });

  const horario = await respuesta.json();
  console.log('Horario creado:', horario);
}
```

### Ejemplo 2: Mostrar conflictos al usuario

```javascript
function mostrarConflictos(conflictos) {
  // Limpiar mensajes anteriores
  const contenedor = document.getElementById('mensajes-conflicto');
  contenedor.innerHTML = '';

  conflictos.forEach(conflicto => {
    const elemento = document.createElement('div');
    elemento.className = 'alert alert-danger mb-3';
    elemento.innerHTML = `
      <h5>${conflicto.titulo}</h5>
      <p><strong>${conflicto.mensaje}</strong></p>
      <hr>
      <ul>
        <li><strong>Aula:</strong> ${conflicto.detalle.aula_ocupada || 'N/A'}</li>
        <li><strong>Docente:</strong> ${conflicto.detalle.docente}</li>
        <li><strong>Materia:</strong> ${conflicto.detalle.materia_existente || conflicto.detalle.materia}</li>
        <li><strong>Grupo:</strong> ${conflicto.detalle.grupo_ocupante || conflicto.detalle.grupo}</li>
        <li><strong>Hora:</strong> ${conflicto.detalle.hora_conflicto}</li>
        <li><strong>Día:</strong> ${conflicto.detalle.dia}</li>
      </ul>
    `;
    contenedor.appendChild(elemento);
  });
}
```

### Ejemplo 3: Validar en tiempo real (mientras escribe)

```javascript
// Validar cuando cambia cualquier campo
document.querySelectorAll('[data-horario-field]').forEach(campo => {
  campo.addEventListener('change', async () => {
    const formulario = {
      id_grupo: document.getElementById('id_grupo').value,
      id_aula: document.getElementById('id_aula').value,
      dia_semana: document.getElementById('dia_semana').value,
      hora_inicio: document.getElementById('hora_inicio').value,
      hora_fin: document.getElementById('hora_fin').value
    };

    // Validar
    const respuesta = await fetch('/api/horarios/validar-conflictos', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formulario)
    });

    const resultado = await respuesta.json();

    // Mostrar/ocultar botón guardar
    const btnGuardar = document.getElementById('btn-guardar');
    if (resultado.tiene_conflictos) {
      btnGuardar.disabled = true;
      btnGuardar.title = `${resultado.cantidad_conflictos} conflicto(s) detectado(s)`;
      mostrarConflictos(resultado.conflictos);
    } else {
      btnGuardar.disabled = false;
      btnGuardar.title = 'Guardar horario';
      document.getElementById('mensajes-conflicto').innerHTML = '';
    }
  });
});
```

---

## Tipos de Conflictos Detectados

| Tipo | Descripción | Solución |
|------|-------------|----------|
| **aula** | El aula ya está ocupada en ese horario | Cambiar aula o cambiar horario |
| **docente** | El docente ya tiene clase en ese horario | Asignar otro docente o cambiar horario |
| **grupo** | El grupo ya tiene clase en ese horario | Cambiar grupo o cambiar horario |

---

## Notas Importantes

1. **Solapamiento de Horarios**: Se detectan solapamientos parciales, no solo iguales exactos
   - Ejemplo: Si existe 08:00-10:00 y intentas 09:00-11:00 → **CONFLICTO**

2. **Mismo Día y Hora**: Solo se valida para el mismo día de la semana

3. **Validación Automática**: Se valida automáticamente al crear/actualizar

4. **Información Detallada**: Cada conflicto incluye información de la clase que causa el conflicto

5. **Permiso Requerido**: Se necesita permiso `gestionar_horarios` para crear/actualizar

---

## Ejemplo Completo de Formulario HTML

```html
<form id="form-horario">
  <div class="row">
    <div class="col-md-6">
      <label>Grupo *</label>
      <select id="id_grupo" name="id_grupo" class="form-control" data-horario-field required>
        <option value="">Seleccionar grupo</option>
      </select>
    </div>
    
    <div class="col-md-6">
      <label>Aula *</label>
      <select id="id_aula" name="id_aula" class="form-control" data-horario-field required>
        <option value="">Seleccionar aula</option>
      </select>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3">
      <label>Día *</label>
      <select id="dia_semana" name="dia_semana" class="form-control" data-horario-field required>
        <option value="Lunes">Lunes</option>
        <option value="Martes">Martes</option>
        <option value="Miércoles">Miércoles</option>
        <option value="Jueves">Jueves</option>
        <option value="Viernes">Viernes</option>
        <option value="Sábado">Sábado</option>
      </select>
    </div>

    <div class="col-md-3">
      <label>Hora Inicio *</label>
      <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" data-horario-field required>
    </div>

    <div class="col-md-3">
      <label>Hora Fin *</label>
      <input type="time" id="hora_fin" name="hora_fin" class="form-control" data-horario-field required>
    </div>

    <div class="col-md-3">
      <label>Tipo Asignación</label>
      <select id="tipo_asignacion" name="tipo_asignacion" class="form-control">
        <option value="Manual">Manual</option>
        <option value="Automática">Automática</option>
      </select>
    </div>
  </div>

  <!-- Área para mostrar conflictos -->
  <div id="mensajes-conflicto"></div>

  <button type="button" id="btn-guardar" class="btn btn-primary">Guardar Horario</button>
</form>
```

---

## Depuración

Para ver los errores en consola:

```javascript
// Agregar logs
console.log('Validando conflictos...');
console.log('Respuesta:', resultado);
console.log('Conflictos:', resultado.conflictos);
```

Para revisar la solicitud en Network:
1. Abre DevTools (F12)
2. Pestaña Network
3. Busca POST `/api/horarios/validar-conflictos`
4. Revisa Request y Response

