# 📊 GUÍA DE MIGRACIÓN DE DATOS A SUPABASE

## Situación Actual
- **BD Actual:** PostgreSQL Local (127.0.0.1:5432)
- **BD Nueva:** Supabase PostgreSQL en la nube
- **Acción:** Migrar datos locales a Supabase

---

## PASO 1: Configurar la Contraseña de Supabase en .env

Editar `.env` y reemplazar:
```bash
DB_PASSWORD=[INGRESA_LA_CONTRASEÑA_DE_SUPABASE_AQUI]
```

Con tu contraseña real de Supabase.

---

## PASO 2: Ejecutar Migraciones en Supabase

### Opción A: Desde tu máquina local (RECOMENDADO)

1. **Cambiar temporalmente a BD Supabase en .env:**
```bash
DB_HOST=db.lobzlococoykiwesfplm.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=tu_password_supabase
```

2. **Ejecutar migraciones:**
```bash
php artisan migrate --force
```

Esto crea todas las tablas en Supabase.

---

## PASO 3: Migrar Datos Existentes (Si tienes datos locales)

### Opción A: Exportar de Local e Importar en Supabase (RECOMENDADO)

**1. Hacer dump de la BD local:**
```bash
pg_dump -U postgres -h 127.0.0.1 -d gestion > backup_local.sql
```

**2. Restaurar en Supabase:**
```bash
psql -h db.lobzlococoykiwesfplm.supabase.co -U postgres -d postgres < backup_local.sql
```

### Opción B: Usar Laravel para migrar datos

Si tienes datos en BD local y quieres transferirlos:

```bash
# 1. Conectar a BD local
# (cambiar .env a BD local)

# 2. Exportar datos como JSON
php artisan tinker
>>> $usuarios = \App\Models\administracion\Usuario::get()->toJson();
>>> file_put_contents('usuarios.json', $usuarios);

# 3. Cambiar a BD Supabase en .env

# 4. Importar datos
php artisan tinker
>>> $usuarios = json_decode(file_get_contents('usuarios.json'), true);
>>> foreach($usuarios as $user) { \App\Models\administracion\Usuario::create($user); }
```

---

## PASO 4: Validar la Conexión

### Verificar que conecta correctamente:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

Debería retornar un objeto PDO sin errores.

### Ver el host actual:
```bash
php artisan tinker
>>> echo config('database.connections.pgsql.host');
```

Debería mostrar: `db.lobzlococoykiwesfplm.supabase.co`

---

## PASO 5: Usar con Docker

### Buildear la imagen:
```bash
docker build -t ficct-app .
```

### Ejecutar con Docker Compose:
```bash
# Parar servicios locales
docker-compose down

# Iniciar contenedor
docker-compose up -d

# Ver logs
docker-compose logs -f app
```

### Ejecutar migraciones dentro de Docker:
```bash
docker-compose exec app php artisan migrate --force
```

---

## CHECKLIST DE MIGRACIÓN

- [ ] Contraseña Supabase agregada en `.env`
- [ ] Migraciones ejecutadas (`php artisan migrate`)
- [ ] Datos migrados (exportar/importar o por Laravel)
- [ ] Conexión validada (`php artisan tinker`)
- [ ] Pruebas en navegador:
  - [ ] Login funciona
  - [ ] CRUD de usuarios funciona
  - [ ] QR se generan correctamente
  - [ ] Bitácora registra acciones
- [ ] Docker build completado sin errores
- [ ] Docker compose levanta sin problemas

---

## ARQUITECTURA FINAL (Con Docker + Supabase)

```
┌─────────────────────┐
│   Docker Container  │
│  (PHP + Apache)     │
├─────────────────────┤
│  FICCT Application  │
│  (Laravel 12.35.1)  │
└──────────┬──────────┘
           │
           │ TCP 5432
           ▼
┌─────────────────────────────────────┐
│      Supabase PostgreSQL            │
│  (En la nube - db.lobzlococo...)    │
│  - Todas las tablas                 │
│  - Todos los datos                  │
└─────────────────────────────────────┘
```

---

## ROLLBACK (Si algo sale mal)

Si necesitas volver a BD local:

1. **Cambiar .env:**
```bash
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gestion
DB_USERNAME=postgres
DB_PASSWORD=2024Alejandro
```

2. **Restaurar BD local desde backup:**
```bash
psql -U postgres -d gestion < backup_local.sql
```

---

## PRÓXIMOS PASOS

1. ✅ Configura la contraseña en `.env`
2. ✅ Ejecuta migraciones
3. ✅ Migra datos (si tienes)
4. ✅ Prueba en navegador
5. ✅ Buildea Docker
6. ✅ Sube a tu servidor/hosting
