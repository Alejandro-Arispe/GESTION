# ⚡ GUÍA RÁPIDA - VALIDACIONES DE HORARIOS

## 🎯 ¿Qué se implementó?

Sistema de **validación de conflictos de horarios** en 3 dimensiones:

1. **🏢 Conflicto de Aula** → El aula NO puede estar ocupada en dos clases a la vez
2. **👨‍🏫 Conflicto de Docente** → Un docente NO puede tener dos clases simultáneas
3. **👥 Conflicto de Grupo** → Un grupo NO puede estar en dos clases a la vez

---

## 📂 Archivos Modificados/Creados

| Archivo | Tipo | Descripción |
|---------|------|-------------|
| `app/Http/Controllers/Planificacion/HorarioController.php` | 🔧 Modificado | Métodos de validación mejorados |
| `routes/api.php` | 🔧 Modificado | Nuevas rutas de API agregadas |
| `VALIDACION_CONFLICTOS_HORARIOS.md` | 📖 Creado | Documentación completa de API |
| `RESUMEN_VALIDACIONES.md` | 📖 Creado | Resumen ejecutivo |
| `ejemplos_validador_horarios.js` | 💻 Creado | Código JavaScript listo para usar |
| `CHECKLIST_IMPLEMENTACION.md` | ✅ Creado | Checklist de implementación |

---

## 🔌 Nuevos Endpoints de API

```
POST   /api/horarios/validar-conflictos    ← Validar ANTES de crear
POST   /api/horarios                       ← Crear horario
GET    /api/horarios                       ← Listar horarios
GET    /api/horarios/{id}                  ← Obtener horario
PUT    /api/horarios/{id}                  ← Actualizar horario
DELETE /api/horarios/{id}                  ← Eliminar horario
```

---

## 🚀 Uso Rápido (Para Frontend)

### 1. Incluir validador
```html
<script src="ejemplos_validador_horarios.js"></script>
```

### 2. HTML del formulario
```html
<form id="form-horario">
  <select id="id_grupo" data-horario-field required>...</select>
  <select id="id_aula" data-horario-field required>...</select>
  <select id="dia_semana" data-horario-field required>...</select>
  <input type="time" id="hora_inicio" data-horario-field required>
  <input type="time" id="hora_fin" data-horario-field required>
  
  <div id="mensajes-conflicto"></div>
  <button id="btn-guardar" data-action="guardar-horario">Guardar</button>
</form>
```

### 3. JavaScript
```javascript
const validador = new ValidadorHorarios({
  token: localStorage.getItem('token'),
  apiUrl: '/api'
});

// Validar automáticamente al cambiar campos
document.querySelectorAll('[data-horario-field]').forEach(campo => {
  campo.addEventListener('change', async () => {
    const datos = {
      id_grupo: document.getElementById('id_grupo').value,
      id_aula: document.getElementById('id_aula').value,
      dia_semana: document.getElementById('dia_semana').value,
      hora_inicio: document.getElementById('hora_inicio').value,
      hora_fin: document.getElementById('hora_fin').value
    };
    
    const resultado = await validador.validar(datos);
    validador.mostrarConflictos(resultado);
    validador.actualizarBotonesGuardar(resultado.puede_guardar);
  });
});
```

---

## 📋 Validación Automática

El sistema valida **automáticamente** los conflictos:

| Evento | Acción |
|--------|--------|
| Cuando rellenan formulario | ✅ Valida en tiempo real |
| Cuando intenta guardar | ✅ Verifica conflictos |
| Si hay conflictos | ❌ Rechaza y muestra detalles |
| Si no hay conflictos | ✅ Permite guardar |

---

## 🔍 Ejemplo de Respuesta con Conflicto

```json
{
  "tiene_conflictos": true,
  "cantidad_conflictos": 1,
  "conflictos": [
    {
      "tipo": "aula",
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
    }
  ],
  "puede_guardar": false
}
```

---

## 🧪 Prueba Rápida en cURL

```bash
# Validar conflictos
curl -X POST http://localhost:8000/api/horarios/validar-conflictos \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id_grupo": 1,
    "id_aula": 5,
    "dia_semana": "Lunes",
    "hora_inicio": "08:00",
    "hora_fin": "10:00"
  }'
```

---

## ✨ Características

✅ **Validación en tiempo real** - Mientras el usuario escribe
✅ **Información detallada** - Muestra qué causa el conflicto
✅ **3 tipos de validación** - Aula, Docente, Grupo
✅ **Solapamiento preciso** - Detecta incluso solapamientos parciales
✅ **Frontend listo** - Código JavaScript completo incluido
✅ **API RESTful** - Endpoints estándar
✅ **Documentado** - Guías completas incluidas

---

## 📚 Documentación Completa

Para más detalles, revisar:

- **`VALIDACION_CONFLICTOS_HORARIOS.md`** ← Guía detallada de API
- **`RESUMEN_VALIDACIONES.md`** ← Resumen técnico
- **`CHECKLIST_IMPLEMENTACION.md`** ← Pasos de implementación
- **`ejemplos_validador_horarios.js`** ← Código JavaScript

---

## ⚡ Casos de Uso

### Caso 1: Crear horario SIN conflictos ✅
```
Usuario selecciona:
- Grupo: 1-A
- Aula: 101
- Día: Lunes
- Hora: 08:00-10:00

Sistema valida → SIN CONFLICTOS ✅
Resultado: "Puedes guardar"
```

### Caso 2: Intenta crear en AULA OCUPADA ❌
```
Usuario selecciona:
- Grupo: 2-A
- Aula: 101 (YA OCUPADA A ESA HORA)
- Día: Lunes
- Hora: 08:30-10:30 (SOLAPAMIENTO)

Sistema valida → CONFLICTO DE AULA ❌
Mensaje: "El aula 101 ya está ocupada (08:30-10:30) por clase de 1-A"
Acción: Deshabilita botón guardar
```

### Caso 3: DOCENTE con dos clases ❌
```
Usuario intenta asignar:
- Docente: Ing. Juan Pérez (TIENE CLASE 08:00-10:00)
- Nueva clase: 09:00-11:00 (SOLAPAMIENTO)

Sistema valida → CONFLICTO DE DOCENTE ❌
Mensaje: "Ing. Juan Pérez ya tiene clase en ese horario"
Solución: Cambiar horario o docente
```

---

## 🎓 Próximos Pasos

1. ✅ Backend implementado
2. ⏳ Integrar en frontend (usar `ejemplos_validador_horarios.js`)
3. ⏳ Probar con casos reales
4. ⏳ Ajustar UI según necesidad
5. ⏳ Entrenar a usuarios

---

## 💡 Tips

- **Reutilizar código**: El archivo `ejemplos_validador_horarios.js` está listo para copiar
- **Agregar iconos**: Usa Font Awesome (fa-building, fa-user, fa-users)
- **Mensajes claros**: Los conflictos incluyen toda la info necesaria
- **Validar antes**: No esperes al guardar, valida en tiempo real
- **UX clara**: Deshabilita botón cuando hay conflictos

---

## 🆘 Si Hay Problemas

### Error: "404 en validar-conflictos"
```bash
php artisan cache:clear
php artisan route:clear
```

### Error: "401 Unauthorized"
```javascript
// Verificar token
console.log(localStorage.getItem('token'));
```

### Error: "Conflictos no se muestran"
```html
<!-- Verifica que existe este contenedor -->
<div id="mensajes-conflicto"></div>
```

---

## 📞 Soporte

Para dudas, revisar:
1. Documentación en `VALIDACION_CONFLICTOS_HORARIOS.md`
2. Ejemplos en `ejemplos_validador_horarios.js`
3. Checklist en `CHECKLIST_IMPLEMENTACION.md`

---

**Estado: 🟢 LISTO PARA USAR**

