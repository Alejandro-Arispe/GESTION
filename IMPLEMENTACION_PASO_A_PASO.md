# 🎨 IMPLEMENTACIÓN PASO A PASO - CON EJEMPLOS VISUALES

## 📍 Paso 1: Preparar HTML

```html
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="container mt-5">
  <div class="row">
    <div class="col-lg-8">
      <h2>Asignar Horario</h2>
      
      <!-- FORMULARIO -->
      <form id="form-horario" class="needs-validation">
        
        <!-- GRUPO -->
        <div class="mb-3">
          <label for="id_grupo" class="form-label">Grupo *</label>
          <select id="id_grupo" name="id_grupo" class="form-select" data-horario-field required>
            <option value="">-- Seleccionar grupo --</option>
            <!-- Se cargan dinámicamente -->
          </select>
          <div class="invalid-feedback">
            Por favor selecciona un grupo.
          </div>
        </div>

        <!-- AULA -->
        <div class="mb-3">
          <label for="id_aula" class="form-label">Aula *</label>
          <select id="id_aula" name="id_aula" class="form-select" data-horario-field required>
            <option value="">-- Seleccionar aula --</option>
            <!-- Se cargan dinámicamente -->
          </select>
          <div class="invalid-feedback">
            Por favor selecciona una aula.
          </div>
        </div>

        <!-- DÍA DE SEMANA -->
        <div class="mb-3">
          <label for="dia_semana" class="form-label">Día de Semana *</label>
          <select id="dia_semana" name="dia_semana" class="form-select" data-horario-field required>
            <option value="">-- Seleccionar día --</option>
            <option value="Lunes">Lunes</option>
            <option value="Martes">Martes</option>
            <option value="Miércoles">Miércoles</option>
            <option value="Jueves">Jueves</option>
            <option value="Viernes">Viernes</option>
            <option value="Sábado">Sábado</option>
          </select>
          <div class="invalid-feedback">
            Por favor selecciona un día.
          </div>
        </div>

        <!-- HORAS -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="hora_inicio" class="form-label">Hora Inicio *</label>
            <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" data-horario-field required>
            <div class="invalid-feedback">
              Por favor ingresa la hora de inicio.
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <label for="hora_fin" class="form-label">Hora Fin *</label>
            <input type="time" id="hora_fin" name="hora_fin" class="form-control" data-horario-field required>
            <div class="invalid-feedback">
              Por favor ingresa la hora de fin.
            </div>
          </div>
        </div>

        <!-- TIPO DE ASIGNACIÓN -->
        <div class="mb-3">
          <label for="tipo_asignacion" class="form-label">Tipo de Asignación</label>
          <select id="tipo_asignacion" name="tipo_asignacion" class="form-select">
            <option value="Manual">Manual</option>
            <option value="Automática">Automática</option>
          </select>
        </div>

        <!-- MENSAJES DE CONFLICTOS -->
        <div id="mensajes-conflicto" class="my-3"></div>

        <!-- BOTONES -->
        <div class="mt-4">
          <button type="button" id="btn-guardar" data-action="guardar-horario" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Horario
          </button>
          <button type="reset" class="btn btn-secondary ms-2">
            <i class="fas fa-redo"></i> Limpiar
          </button>
          <button type="button" class="btn btn-outline-secondary ms-2" onclick="location.reload()">
            <i class="fas fa-times"></i> Cancelar
          </button>
        </div>

      </form>
    </div>

    <!-- PANEL INFORMATIVO -->
    <div class="col-lg-4">
      <div class="card bg-light">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información</h5>
        </div>
        <div class="card-body">
          <p class="mb-2">
            <i class="fas fa-check-circle text-success"></i>
            <strong>Validación:</strong> En tiempo real
          </p>
          <p class="mb-2">
            <i class="fas fa-ban text-danger"></i>
            <strong>Conflictos:</strong> Detecta automáticamente
          </p>
          <p class="mb-2">
            <i class="fas fa-cubes"></i>
            <strong>Tipos:</strong> Aula, Docente, Grupo
          </p>
          <hr>
          <p class="text-muted small">
            El sistema previene choques de horarios detectando conflictos 
            en tiempo real. Si hay conflictos, el botón "Guardar" se 
            deshabilitará automáticamente.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- TABLA DE HORARIOS EXISTENTES -->
  <div class="row mt-5">
    <div class="col-12">
      <h3>Horarios Registrados</h3>
      <table class="table table-striped table-hover" id="tabla-horarios">
        <thead class="table-dark">
          <tr>
            <th>Grupo</th>
            <th>Materia</th>
            <th>Docente</th>
            <th>Aula</th>
            <th>Día</th>
            <th>Hora</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="tbody-horarios">
          <!-- Se cargan dinámicamente -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="ejemplos_validador_horarios.js"></script>
<script src="app.js"></script>

</body>
</html>
```

---

## 📍 Paso 2: Crear archivo `app.js`

```javascript
// ============================================================
// CONFIGURACIÓN INICIAL
// ============================================================

const token = localStorage.getItem('token');
const apiUrl = '/api';

// Inicializar validador
const validador = new ValidadorHorarios({
  token: token,
  apiUrl: apiUrl,
  contenedorConflictos: '#mensajes-conflicto'
});

// ============================================================
// 1. CARGAR DATOS INICIALES
// ============================================================

document.addEventListener('DOMContentLoaded', async () => {
  console.log('📋 Inicializando...');
  
  await cargarGrupos();
  await cargarAulas();
  await cargarHorarios();
  
  console.log('✅ Inicialización completada');
});

/**
 * Cargar grupos
 */
async function cargarGrupos() {
  try {
    const respuesta = await fetch(`${apiUrl}/grupos`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    if (!respuesta.ok) throw new Error('Error cargando grupos');
    
    const data = await respuesta.json();
    const select = document.getElementById('id_grupo');
    
    data.forEach(grupo => {
      const option = document.createElement('option');
      option.value = grupo.id_grupo;
      option.textContent = `${grupo.nombre} - ${grupo.materia?.nombre || 'N/A'}`;
      select.appendChild(option);
    });
    
    console.log('✅ Grupos cargados');
  } catch (error) {
    console.error('❌ Error:', error);
    alert('Error al cargar grupos');
  }
}

/**
 * Cargar aulas
 */
async function cargarAulas() {
  try {
    const respuesta = await fetch(`${apiUrl}/aulas`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    if (!respuesta.ok) throw new Error('Error cargando aulas');
    
    const data = await respuesta.json();
    const select = document.getElementById('id_aula');
    
    data.forEach(aula => {
      const option = document.createElement('option');
      option.value = aula.id_aula;
      option.textContent = `Aula ${aula.nro}`;
      select.appendChild(option);
    });
    
    console.log('✅ Aulas cargadas');
  } catch (error) {
    console.error('❌ Error:', error);
    alert('Error al cargar aulas');
  }
}

/**
 * Cargar horarios existentes
 */
async function cargarHorarios() {
  try {
    const respuesta = await fetch(`${apiUrl}/horarios`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    if (!respuesta.ok) throw new Error('Error cargando horarios');
    
    const data = await respuesta.json();
    const tbody = document.getElementById('tbody-horarios');
    tbody.innerHTML = '';
    
    data.horarios.forEach(horario => {
      const fila = document.createElement('tr');
      fila.innerHTML = `
        <td><strong>${horario.grupo?.nombre || 'N/A'}</strong></td>
        <td>${horario.grupo?.materia?.nombre || 'N/A'}</td>
        <td>${horario.grupo?.docente?.nombre || 'Sin asignar'}</td>
        <td><span class="badge bg-info">${horario.aula?.nro || 'N/A'}</span></td>
        <td>${horario.dia_semana}</td>
        <td>
          <i class="far fa-clock"></i>
          ${horario.hora_inicio} - ${horario.hora_fin}
        </td>
        <td>
          <button class="btn btn-sm btn-warning" onclick="editarHorario(${horario.id_horario})">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-sm btn-danger" onclick="eliminarHorario(${horario.id_horario})">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(fila);
    });
    
    console.log(`✅ ${data.horarios.length} horarios cargados`);
  } catch (error) {
    console.error('❌ Error:', error);
  }
}

// ============================================================
// 2. VALIDACIÓN EN TIEMPO REAL
// ============================================================

document.querySelectorAll('[data-horario-field]').forEach(campo => {
  campo.addEventListener('change', async () => {
    await validarFormulario();
  });
});

/**
 * Validar formulario
 */
async function validarFormulario() {
  const datos = obtenerDatosFormulario();
  
  // Validar que no estén vacíos
  if (!datos.id_grupo || !datos.id_aula || !datos.dia_semana || 
      !datos.hora_inicio || !datos.hora_fin) {
    document.getElementById('mensajes-conflicto').innerHTML = '';
    document.getElementById('btn-guardar').disabled = true;
    return;
  }
  
  try {
    const resultado = await validador.validar(datos);
    validador.mostrarConflictos(resultado);
    validador.actualizarBotonesGuardar(resultado.puede_guardar);
  } catch (error) {
    console.error('Error en validación:', error);
  }
}

/**
 * Obtener datos del formulario
 */
function obtenerDatosFormulario() {
  return {
    id_grupo: document.getElementById('id_grupo').value,
    id_aula: document.getElementById('id_aula').value,
    dia_semana: document.getElementById('dia_semana').value,
    hora_inicio: document.getElementById('hora_inicio').value,
    hora_fin: document.getElementById('hora_fin').value,
    tipo_asignacion: document.getElementById('tipo_asignacion').value
  };
}

// ============================================================
// 3. GUARDAR HORARIO
// ============================================================

document.getElementById('btn-guardar').addEventListener('click', async () => {
  const datos = obtenerDatosFormulario();
  
  if (!datos.id_grupo || !datos.id_aula) {
    alert('Por favor completa todos los campos');
    return;
  }
  
  try {
    const respuesta = await fetch(`${apiUrl}/horarios`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(datos)
    });
    
    const resultado = await respuesta.json();
    
    if (respuesta.ok) {
      alert('✅ ¡Horario creado exitosamente!');
      document.getElementById('form-horario').reset();
      document.getElementById('mensajes-conflicto').innerHTML = '';
      await cargarHorarios();
    } else {
      if (resultado.conflictos) {
        validador.mostrarConflictos({
          tiene_conflictos: true,
          conflictos: resultado.conflictos,
          puede_guardar: false
        });
      } else {
        alert('❌ Error: ' + (resultado.message || 'Error desconocido'));
      }
    }
  } catch (error) {
    console.error('❌ Error:', error);
    alert('Error al guardar: ' + error.message);
  }
});

// ============================================================
// 4. EDITAR HORARIO
// ============================================================

async function editarHorario(id) {
  try {
    const respuesta = await fetch(`${apiUrl}/horarios/${id}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    if (!respuesta.ok) throw new Error('Error cargando horario');
    
    const data = await respuesta.json();
    const horario = data.horario;
    
    // Llenar formulario
    document.getElementById('id_grupo').value = horario.id_grupo;
    document.getElementById('id_aula').value = horario.id_aula;
    document.getElementById('dia_semana').value = horario.dia_semana;
    document.getElementById('hora_inicio').value = horario.hora_inicio;
    document.getElementById('hora_fin').value = horario.hora_fin;
    document.getElementById('tipo_asignacion').value = horario.tipo_asignacion;
    
    // Cambiar botón a "Actualizar"
    const btnGuardar = document.getElementById('btn-guardar');
    btnGuardar.textContent = '✓ Actualizar Horario';
    btnGuardar.dataset.horarioId = id;
    
    // Scroll al formulario
    document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
    
  } catch (error) {
    console.error('❌ Error:', error);
    alert('Error al cargar horario');
  }
}

// Modificar guardar para soportar actualización
const originalGuardar = document.getElementById('btn-guardar').onclick;

document.getElementById('btn-guardar').addEventListener('click', async function(e) {
  const idHorario = this.dataset.horarioId;
  const datos = obtenerDatosFormulario();
  
  try {
    const url = idHorario ? `${apiUrl}/horarios/${idHorario}` : `${apiUrl}/horarios`;
    const metodo = idHorario ? 'PUT' : 'POST';
    
    const respuesta = await fetch(url, {
      method: metodo,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(datos)
    });
    
    const resultado = await respuesta.json();
    
    if (respuesta.ok) {
      alert(idHorario ? '✅ ¡Horario actualizado!' : '✅ ¡Horario creado!');
      document.getElementById('form-horario').reset();
      document.getElementById('btn-guardar').dataset.horarioId = '';
      document.getElementById('btn-guardar').textContent = '✓ Guardar Horario';
      document.getElementById('mensajes-conflicto').innerHTML = '';
      await cargarHorarios();
    } else {
      alert('❌ Error: ' + resultado.message);
    }
  } catch (error) {
    alert('❌ Error: ' + error.message);
  }
});

// ============================================================
// 5. ELIMINAR HORARIO
// ============================================================

async function eliminarHorario(id) {
  if (!confirm('¿Estás seguro de que deseas eliminar este horario?')) {
    return;
  }
  
  try {
    const respuesta = await fetch(`${apiUrl}/horarios/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    if (respuesta.ok) {
      alert('✅ Horario eliminado');
      await cargarHorarios();
    } else {
      alert('❌ Error al eliminar');
    }
  } catch (error) {
    console.error('❌ Error:', error);
    alert('Error: ' + error.message);
  }
}
```

---

## 🎨 Resultado Visual

Cuando el usuario usa la aplicación, verá:

### 📍 Inicial (SIN conflictos)
```
┌────────────────────────────────────┐
│ Grupo: [1-A - Programación II ▼]   │
│ Aula: [101 ▼]                      │
│ Día: [Lunes ▼]                     │
│ Hora: [08:00] - [10:00]            │
│                                    │
│ 🟢 ¡Bien! No hay conflictos.       │
│                                    │
│ [✓ Guardar] [Limpiar] [Cancelar]   │
└────────────────────────────────────┘
```

### ⚠️ Con conflicto (DESHABILITADO)
```
┌────────────────────────────────────┐
│ Grupo: [1-A - Programación II ▼]   │
│ Aula: [101 ▼]                      │
│ Día: [Lunes ▼]                     │
│ Hora: [08:00] - [10:00]            │
│                                    │
│ 🔴 CONFLICTO DE AULA               │
│ El aula 101 ya está ocupada en     │
│ este horario                       │
│                                    │
│ • Aula: 101                        │
│ • Docente: Ing. Juan Pérez         │
│ • Materia: Programación I          │
│ • Grupo: 1-A                       │
│ • Hora: 08:30 - 10:30              │
│ • Día: Lunes                       │
│                                    │
│ [✓ Guardar] [Limpiar] [Cancelar]   │ ← DESHABILITADO
└────────────────────────────────────┘
```

---

## 🚀 Prueba Rápida

1. **Abre la página** → Se cargan grupos y aulas
2. **Rellena el formulario** → Se valida automáticamente
3. **Sin conflictos** → Botón habilitado
4. **Con conflictos** → Botón deshabilitado + detalles
5. **Hace click guardar** → Crea o actualiza horario
6. **Tabla se actualiza** → Muestra nuevo horario

---

## ✨ Características Adicionales

- Botones en tabla para editar/eliminar
- Scroll automático al formulario al editar
- Cambio de botón "Guardar" → "Actualizar"
- Confirmación antes de eliminar
- Validación en tiempo real
- Mensajes claros y amigables

