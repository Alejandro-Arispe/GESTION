# 🚀 INSTRUCCIONES PARA RENDER

## Problemas Solucionados
✅ Permisos de `storage/` y `bootstrap/cache`
✅ Dockerfile crea directorios antes de instalar Composer
✅ Script de inicialización ejecuta migraciones automáticamente
✅ Configuración de producción (`APP_ENV=production`, `APP_DEBUG=false`)

---

## PASOS EN RENDER

### 1. Conectar tu repositorio GitHub
1. Ir a [render.com](https://render.com)
2. Click en "New" → "Web Service"
3. Conectar tu repositorio GitHub
4. Seleccionar la rama `main`

### 2. Configurar el servicio
**Name:** ficct-app  
**Environment:** Docker  
**Build Command:** (Render lo detecta automáticamente)  
**Start Command:** (Render lo detecta automáticamente)

### 3. Variables de Entorno (IMPORTANTE)
Agregar en Render las siguientes variables:

| Variable | Valor |
|----------|-------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | `base64:kLFZv26mHIkWnQt9CE6qNhpD5Lojem9+FyuNVcfwiEM=` |
| `APP_URL` | `https://tu-app.onrender.com` (pon tu URL) |
| `DB_HOST` | `db.lobzlococoykiwesfplm.supabase.co` |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | `postgres` |
| `DB_PASSWORD` | `Alejandro2024` |

### 4. Deploy
Click en "Create Web Service"

Render automáticamente:
- ✅ Buildea el Docker
- ✅ Ejecuta migraciones
- ✅ Cachea configuración y rutas
- ✅ Inicia la app

---

## Verificar que funcionó

Cuando Render termine (2-3 minutos):

1. **Ir a tu URL:** `https://tu-app.onrender.com`
2. **Intenta hacer login**
3. **Prueba crear/editar datos**
4. **Verifica que los QR se generan**

---

## Si sale error todavía

### Error: "Invalid cache path"
```bash
# Render está recreando los directorios
# Espera 1-2 minutos más
# Si persiste, haz redeploy
```

### Error: "Connection refused" a BD
```bash
# Verificar que APP_URL tenga https://
# Verificar credenciales Supabase en .env
# Verificar que Supabase acepta conexiones desde Render
```

### Ver logs de Render
1. Ir a tu servicio en Render
2. Click en "Logs"
3. Ver qué error sale

---

## ESTRUCTURA FINAL

```
GitHub (main branch)
        ↓
Render detecta cambios
        ↓
Docker build (Dockerfile)
        ↓
Crear directorios + permisos
        ↓
Composer install
        ↓
Run migrations (migrate --force)
        ↓
Cache config + routes
        ↓
Apache start
        ↓
App disponible en https://tu-app.onrender.com
```

---

## Próximas veces que hagas cambios

1. Commit a GitHub
2. Push a `main`
3. Render automáticamente redeploya en 1-2 minutos

---

## URL de tu app en Render
Será algo como:
- `https://ficct-app.onrender.com` (si es gratis)
- O tu dominio personalizado

**¿Cuál es tu URL en Render?** Actualiza `APP_URL` en el .env de Render.
