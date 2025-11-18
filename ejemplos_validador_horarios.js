/**
 * COMPONENTE PARA VALIDAR CONFLICTOS DE HORARIOS
 * Uso: Integrar en formulario de asignación de horarios
 */

// ============================================================
// OPCIÓN 1: VANILLA JAVASCRIPT (Sin frameworks)
// ============================================================

class ValidadorHorarios {
  constructor(options = {}) {
    this.token = options.token || localStorage.getItem('token');
    this.apiUrl = options.apiUrl || '/api';
    this.contenedorConflictos = options.contenedorConflictos || '#mensajes-conflicto';
  }

  /**
   * Validar conflictos de horario
   */
  async validar(datos) {
    try {
      const respuesta = await fetch(`${this.apiUrl}/horarios/validar-conflictos`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(datos)
      });

      if (!respuesta.ok) {
        throw new Error(`HTTP error! status: ${respuesta.status}`);
      }

      const resultado = await respuesta.json();
      return resultado;

    } catch (error) {
      console.error('Error validando conflictos:', error);
      return {
        tiene_conflictos: true,
        cantidad_conflictos: 1,
        conflictos: [{
          tipo: 'error',
          severidad: 'error',
          titulo: 'Error de Validación',
          mensaje: error.message
        }],
        puede_guardar: false
      };
    }
  }

  /**
   * Mostrar conflictos en la UI
   */
  mostrarConflictos(resultado) {
    const contenedor = document.querySelector(this.contenedorConflictos);
    if (!contenedor) return;

    contenedor.innerHTML = '';

    if (!resultado.tiene_conflictos) {
      // Mostrar mensaje de éxito
      const elemento = document.createElement('div');
      elemento.className = 'alert alert-success';
      elemento.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <strong>¡Bien!</strong> No hay conflictos. Puedes guardar el horario.
      `;
      contenedor.appendChild(elemento);
      return;
    }

    // Mostrar cada conflicto
    resultado.conflictos.forEach(conflicto => {
      const elemento = document.createElement('div');
      elemento.className = 'alert alert-danger mb-2';
      elemento.innerHTML = this.generarHTMLConflicto(conflicto);
      contenedor.appendChild(elemento);
    });
  }

  /**
   * Generar HTML para un conflicto
   */
  generarHTMLConflicto(conflicto) {
    const tipoIcono = {
      'aula': 'fa-building',
      'docente': 'fa-user',
      'grupo': 'fa-users',
      'error': 'fa-exclamation-triangle'
    };

    const icon = tipoIcono[conflicto.tipo] || 'fa-exclamation-circle';

    let html = `
      <div class="d-flex align-items-start">
        <i class="fas ${icon} mt-1 me-2" style="color: #dc3545;"></i>
        <div class="flex-grow-1">
          <h6 class="mb-1">${conflicto.titulo}</h6>
          <p class="mb-2"><strong>${conflicto.mensaje}</strong></p>
    `;

    // Agregar detalles si existen
    if (conflicto.detalle) {
      html += '<small class="text-muted"><ul class="mb-0">';
      
      Object.entries(conflicto.detalle).forEach(([clave, valor]) => {
        const etiqueta = this.formatearEtiqueta(clave);
        html += `<li><strong>${etiqueta}:</strong> ${valor}</li>`;
      });

      html += '</ul></small>';
    }

    html += '</div></div>';
    return html;
  }

  /**
   * Formatear nombres de campos a texto legible
   */
  formatearEtiqueta(clave) {
    const etiquetas = {
      'aula_ocupada': 'Aula Ocupada',
      'docente': 'Docente',
      'materia': 'Materia',
      'materia_existente': 'Materia Existente',
      'grupo_ocupante': 'Grupo Ocupante',
      'grupo': 'Grupo',
      'grupo_existente': 'Grupo Existente',
      'hora_conflicto': 'Hora del Conflicto',
      'hora_inicio': 'Hora Inicio',
      'hora_fin': 'Hora Fin',
      'dia': 'Día',
      'aula_existente': 'Aula Existente',
      'docente_existente': 'Docente Existente',
      'nueva_materia': 'Nueva Materia',
      'nueva_aula': 'Nueva Aula'
    };

    return etiquetas[clave] || clave.replace(/_/g, ' ').toUpperCase();
  }

  /**
   * Habilitar/deshabilitar botón de guardar
   */
  actualizarBotonesGuardar(puede_guardar) {
    const botones = document.querySelectorAll('[data-action="guardar-horario"]');
    botones.forEach(boton => {
      boton.disabled = !puede_guardar;
      boton.title = puede_guardar ? 'Guardar horario' : 'Hay conflictos que resolver';
      
      if (puede_guardar) {
        boton.classList.remove('opacity-50');
      } else {
        boton.classList.add('opacity-50');
      }
    });
  }
}

// ============================================================
// OPCIÓN 2: INTEGRACIÓN CON FORMULARIO
// ============================================================

// Inicializar validador
const validador = new ValidadorHorarios({
  token: localStorage.getItem('token'),
  apiUrl: '/api',
  contenedorConflictos: '#mensajes-conflicto'
});

// Escuchar cambios en campos de horario
document.querySelectorAll('[data-horario-field]').forEach(campo => {
  campo.addEventListener('change', async () => {
    const datos = obtenerDatosFormulario();
    
    if (!validarCamposObligatorios(datos)) {
      return;
    }

    const resultado = await validador.validar(datos);
    validador.mostrarConflictos(resultado);
    validador.actualizarBotonesGuardar(resultado.puede_guardar);
  });
});

/**
 * Obtener datos del formulario
 */
function obtenerDatosFormulario() {
  return {
    id_grupo: document.getElementById('id_grupo')?.value,
    id_aula: document.getElementById('id_aula')?.value,
    dia_semana: document.getElementById('dia_semana')?.value,
    hora_inicio: document.getElementById('hora_inicio')?.value,
    hora_fin: document.getElementById('hora_fin')?.value
  };
}

/**
 * Validar campos obligatorios
 */
function validarCamposObligatorios(datos) {
  return datos.id_grupo && datos.id_aula && datos.dia_semana && 
         datos.hora_inicio && datos.hora_fin;
}

/**
 * Guardar horario
 */
document.getElementById('btn-guardar')?.addEventListener('click', async () => {
  const datos = obtenerDatosFormulario();
  
  if (!validarCamposObligatorios(datos)) {
    alert('Por favor completa todos los campos');
    return;
  }

  try {
    const respuesta = await fetch('/api/horarios', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(datos)
    });

    const resultado = await respuesta.json();

    if (respuesta.ok) {
      alert('¡Horario creado exitosamente!');
      location.reload();
    } else {
      if (resultado.conflictos) {
        validador.mostrarConflictos({
          tiene_conflictos: true,
          conflictos: resultado.conflictos,
          puede_guardar: false
        });
      } else {
        alert('Error: ' + resultado.message);
      }
    }
  } catch (error) {
    alert('Error al guardar: ' + error.message);
  }
});

// ============================================================
// OPCIÓN 3: COMPONENTE BLADE (Para Laravel)
// ============================================================

/*
<!-- resources/views/components/validador-horarios.blade.php -->

<div id="validador-horarios">
  <form id="form-horario">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="id_grupo" class="form-label">Grupo *</label>
          <select 
            id="id_grupo" 
            name="id_grupo" 
            class="form-select" 
            data-horario-field 
            required>
            <option value="">Seleccionar grupo</option>
            @foreach($grupos as $grupo)
              <option value="{{ $grupo->id_grupo }}">
                {{ $grupo->nombre }} - {{ $grupo->materia->nombre }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label for="id_aula" class="form-label">Aula *</label>
          <select 
            id="id_aula" 
            name="id_aula" 
            class="form-select" 
            data-horario-field 
            required>
            <option value="">Seleccionar aula</option>
            @foreach($aulas as $aula)
              <option value="{{ $aula->id_aula }}">{{ $aula->nro }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label for="dia_semana" class="form-label">Día *</label>
          <select 
            id="dia_semana" 
            name="dia_semana" 
            class="form-select" 
            data-horario-field 
            required>
            <option value="">Seleccionar día</option>
            <option value="Lunes">Lunes</option>
            <option value="Martes">Martes</option>
            <option value="Miércoles">Miércoles</option>
            <option value="Jueves">Jueves</option>
            <option value="Viernes">Viernes</option>
            <option value="Sábado">Sábado</option>
          </select>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="hora_inicio" class="form-label">Hora Inicio *</label>
          <input 
            type="time" 
            id="hora_inicio" 
            name="hora_inicio" 
            class="form-control" 
            data-horario-field 
            required>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="hora_fin" class="form-label">Hora Fin *</label>
          <input 
            type="time" 
            id="hora_fin" 
            name="hora_fin" 
            class="form-control" 
            data-horario-field 
            required>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label for="tipo_asignacion" class="form-label">Tipo</label>
          <select 
            id="tipo_asignacion" 
            name="tipo_asignacion" 
            class="form-select">
            <option value="Manual">Manual</option>
            <option value="Automática">Automática</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Área para mensajes de conflictos -->
    <div id="mensajes-conflicto" class="mt-3"></div>

    <div class="mt-4">
      <button 
        type="button" 
        id="btn-guardar" 
        data-action="guardar-horario" 
        class="btn btn-primary">
        <i class="fas fa-save"></i> Guardar Horario
      </button>
      <button type="reset" class="btn btn-secondary ms-2">
        <i class="fas fa-redo"></i> Limpiar
      </button>
    </div>
  </form>
</div>

<script>
// Incluir ValidadorHorarios aquí
</script>
*/
