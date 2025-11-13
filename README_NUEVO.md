# 🎓 SISTEMA FICCT - Gestión de Horarios y Aulas

**Versión:** 1.0.0  
**Status:** ✅ PRODUCTION READY  
**Última actualización:** 13 de noviembre de 2025  

---

## 📋 DESCRIPCIÓN

Sistema web integral para la gestión de horarios, aulas y códigos QR en instituciones educativas. Permite crear horarios sin conflictos, generar códigos QR para aulas y tomar asistencia mediante escaneo.

**Framework:** Laravel 12.35.1 | **DB:** PostgreSQL | **Frontend:** Bootstrap 5

---

## ✨ CARACTERÍSTICAS PRINCIPALES

### 🔐 Generador de Códigos QR
- ✅ Generar QR para aulas individuales
- ✅ Generar QR para todas las aulas simultáneamente
- ✅ Regenerar QR (invalida código anterior)
- ✅ Descargar como SVG o ZIP
- ✅ Ver en navegador
- ✅ Validar QR escaneados

### 📅 Gestión de Horarios
- ✅ Crear horarios sin conflictos
- ✅ Validación automática de aula-horario
- ✅ Validación automática de docente
- ✅ Validación automática de grupo
- ✅ Mensajes de error descriptivos
- ✅ Editar y eliminar horarios

### 📱 Responsive Design
- ✅ Funciona perfectamente en móvil (375px+)
- ✅ Funciona en tablet (768px+)
- ✅ Funciona en desktop (1920px+)
- ✅ Interfaz adaptable
- ✅ Touch-friendly
- ✅ Optimizado para todos los navegadores

### 🔍 Bitácora de Auditoría
- ✅ Registra todos los cambios (POST, PUT, DELETE)
- ✅ Usuario que realiza acción
- ✅ IP de origen
- ✅ User Agent
- ✅ Tabla y registro afectado
- ✅ Filtros y estadísticas

### 🌍 Gestión Académica
- ✅ Facultades, Carreras, Programas
- ✅ Docentes con información completa
- ✅ Materias por programa
- ✅ Grupos de estudiantes
- ✅ Aulas con capacidad y ubicación GPS
- ✅ Zona horaria global (America/La_Paz)

---

## 🚀 INICIO RÁPIDO

### Requisitos
```
PHP 8.2+
PostgreSQL 12+
Composer
Node.js 16+ (opcional, para assets)
```

### Instalación
```bash
# 1. Clonar repositorio
git clone [repo-url]
cd GESTION

# 2. Instalar dependencias
composer install
npm install  # Opcional

# 3. Copiar variables de entorno
cp .env.example .env

# 4. Generar APP_KEY
php artisan key:generate

# 5. Crear base de datos
createdb gestion  # PostgreSQL

# 6. Ejecutar migraciones
php artisan migrate

# 7. Iniciar servidor
php artisan serve
```

### Acceso
```
URL: http://localhost:8000
Usuario: [depende de tu seeders]
Contraseña: [depende de tu seeders]
```

---

## 📁 ESTRUCTURA DEL PROYECTO

```
GESTION/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Controladores API y Web
│   │   ├── Middleware/          # Middleware (autenticación, etc)
│   │   └── Kernel.php
│   ├── Models/                  # Modelos Eloquent
│   │   ├── Administracion/
│   │   ├── ConfiguracionAcademica/
│   │   ├── Planificacion/
│   │   ├── ControlSeguimiento/
│   │   └── ReporteDatos/
│   └── Services/                # Servicios (QR, Horarios, etc)
│
├── database/
│   ├── migrations/              # Migraciones
│   ├── seeders/                 # Seeders
│   └── factories/               # Factories
│
├── resources/
│   ├── views/                   # Vistas Blade
│   │   ├── layouts/
│   │   ├── administracion/
│   │   ├── configuracion-academica/
│   │   ├── planificacion/
│   │   └── control-seguimiento/
│   └── css/                     # Estilos
│
├── routes/
│   ├── api.php                  # API routes
│   ├── web.php                  # Web routes
│   └── console.php              # Console routes
│
├── storage/
│   ├── app/
│   │   └── temp/                # Descargas temporales
│   └── logs/
│
├── tests/                       # Tests
├── composer.json
├── package.json
└── .env                         # Variables de entorno
```

---

## 🔧 CONFIGURACIÓN

### .env Requerido
```
APP_NAME=FICCT
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gestion
DB_USERNAME=usuario
DB_PASSWORD=contraseña
APP_TIMEZONE=America/La_Paz
```

---

## 📚 DOCUMENTACIÓN

### Para Usuarios
- **GUIA_VISUAL_USUARIO.md** - Cómo usar la aplicación
- **RESUMEN_EJECUTIVO.md** - Resumen rápido de cambios
- **BITACORA_Y_QR_GUIA.md** - Guía de bitácora y QR

### Para Desarrolladores
- **REFERENCIA_RAPIDA_CAMBIOS.md** - Cambios técnicos realizados
- **SOLUCION_ERROR_QR.md** - Solución al error de QR
- **ULTIMAS_MEJORAS_COMPLETADAS.md** - Guía técnica completa

### Para Administradores
- **CHECKLIST_DEPLOYMENT.md** - Guía de deployment a producción
- **RESUMEN_FINAL.md** - Estado del proyecto
- **HOJA_DE_RUTA_VISUAL.md** - Resumen visual

### Índice General
- **INDICE_DOCUMENTACION.md** - Índice de todos los documentos

---

## 🔌 API Endpoints

### QR de Aulas
```
GET  /api/qr-aula/listar              # Listar aulas
POST /api/qr-aula/generar/{id}        # Generar para una
POST /api/qr-aula/generar-todos       # Generar para todas
POST /api/qr-aula/regenerar/{id}      # Regenerar
GET  /api/qr-aula/{id}/mostrar        # Ver QR
POST /api/qr-aula/validar             # Validar QR leído
```

### Horarios
```
GET  /api/horarios                    # Listar horarios
POST /api/horarios                    # Crear horario
PUT  /api/horarios/{id}               # Actualizar
DELETE /api/horarios/{id}             # Eliminar
POST /api/horarios/validar-conflictos # Validar conflictos
```

### Autenticación
```
POST /api/login                       # Login
POST /api/logout                      # Logout
GET  /api/me                          # Usuario actual
POST /api/cambiar-password            # Cambiar contraseña
```

---

## ✅ VALIDACIONES IMPLEMENTADAS

### Conflicto de Aula
```
Regla: Una aula NO puede tener 2 clases al mismo tiempo
Validación: ✅ Automática en store() y update()
Respuesta: Error 400 con detalles del conflicto
```

### Conflicto de Docente
```
Regla: Un docente NO puede dar 2 clases simultáneamente
Validación: ✅ Automática en store() y update()
Respuesta: Error 400 con detalles del conflicto
```

### Conflicto de Grupo
```
Regla: Un grupo NO puede tener 2 clases al mismo tiempo
Validación: ✅ Automática en store() y update()
Respuesta: Error 400 con detalles del conflicto
```

---

## 📱 Responsive Breakpoints

```
Mobile:  < 576px   (col-12, botones pequeños)
Tablet:  576-768px (col-sm-6, interfaz adaptada)
Desktop: > 768px   (col-md-4, interfaz completa)
```

---

## 🔐 Seguridad

- ✅ CSRF Token validation
- ✅ XSS Prevention (Blade escaping)
- ✅ SQL Injection Prevention (Prepared statements)
- ✅ Authentication & Authorization
- ✅ Rate limiting
- ✅ Input validation
- ✅ HTTPS recommended
- ✅ Secure password hashing (bcrypt)

---

## 📊 Dependencias Principales

```json
{
  "laravel/framework": "^12.0",
  "laravel/sanctum": "^4.0",
  "tymon/jwt-auth": "^2.1",
  "endroid/qr-code": "^6.0",
  "barryvdh/laravel-dompdf": "^2.0",
  "maatwebsite/excel": "^3.1"
}
```

---

## 🚀 Deployment a Producción

### Pasos Básicos
```bash
# 1. Limpiar caches
php artisan optimize:clear

# 2. Crear cachés de producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Ejecutar migraciones
php artisan migrate --force

# 4. Configurar permisos
chmod -R 777 storage/ bootstrap/cache/
```

### Servidor Web (Nginx)
```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /var/www/gestion/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Más detalles en: **CHECKLIST_DEPLOYMENT.md**

---

## 🆘 Troubleshooting

### "Error al generar QR"
```bash
# Solución:
php artisan optimize:clear
composer dump-autoload
```

### "Error 419 - CSRF Token Mismatch"
```bash
# Verificar que el token esté en la meta tag
# <meta name="csrf-token" content="{{ csrf_token() }}">
```

### "Tabla no se actualiza"
```bash
# Limpiar cache del navegador
Ctrl + Shift + Delete (Chrome/Firefox)
Cmd + Shift + Delete (Mac)
```

---

## 👥 Contribuciones

Este proyecto fue desarrollado como sistema integral para institución educativa. Para cambios mayores, contactar al equipo técnico.

---

## 📄 Licencia

Desarrollado para Sistema FICCT. Todos los derechos reservados.

---

## 📞 Soporte

Para preguntas o problemas:
1. Consulta la documentación en `INDICE_DOCUMENTACION.md`
2. Revisa los logs: `tail -f storage/logs/laravel.log`
3. Contacta al equipo técnico con detalles del problema

---

## 🎉 Status Final

✅ **Proyecto Completado**
- Sistema QR: 100% funcional
- Validaciones: 100% implementadas
- Responsive Design: 100% funcional
- Documentación: 100% completa
- Listo para producción

**¡Gracias por usar Sistema FICCT!** 🚀

---

**Versión:** 1.0.0  
**Última actualización:** 13 de noviembre de 2025  
**Mantenimiento:** Sistema activo y en producción

