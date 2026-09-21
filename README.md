# Sistema de Gestión Hospitalaria (PHP + MySQL)

Aplicación web hospitalaria construida con PHP, PDO, Bootstrap y una arquitectura modular de controlador/servicio/repositorio.

## Empieza Aquí (Para Principiantes)

Si es tu primera vez en este código, sigue este orden exacto:

1. Ejecuta la aplicación localmente (ver Inicio Rápido más abajo).
2. Abre una página en el navegador, por ejemplo `/patients.php`.
3. Encuentra su punto de entrada en `public/` y léelo de arriba a abajo.
4. Encuentra el endpoint de API que usa esa página en `public/api/`.
5. Sigue la cadena de llamadas hacia:
   - `app/Controllers/Api/`
   - `app/Services/` (si se usa)
   - `app/Repositories/`

Esta es la forma más rápida de entender "cómo funcionan las cosas" en este proyecto.

## Inicio Rápido

### Requisitos

- PHP 8.0+
- MySQL / MariaDB
- Extensiones de PHP: `pdo`, `pdo_mysql`

### Ejecutar

```powershell
.\run.ps1
```

Alternativa:

```cmd
run.cmd
```

Linux/macOS:

```bash
./run.sh
```

`run.sh` busca automáticamente un puerto libre (por defecto 8000), arranca el servidor
PHP en segundo plano, guarda el PID en `.run.sh.pid` y abre el navegador.

Opciones:

```bash
./run.sh -p 8080      # puerto específico
./run.sh --no-open    # no abrir el navegador
./run.sh stop         # detener el servidor iniciado por el script
```

Luego abre:

- `http://localhost:8000/setup.php`

Usa setup para inicializar el esquema y los datos semilla.

> Nota: si alguna página falla por un error de conexión a la base de datos
> (por ejemplo, `Unknown database`), la aplicación redirige automáticamente a
> `setup.php` para que puedas crear/configurar la base de datos.

### Desplegar a GitHub (push)

```bash
./push.sh -m "mensaje del commit"       # commit y push a origin/main
./push.sh --force                       # fuerza el push reemplazando el historial remoto
./push.sh --no-pull                     # no hacer pull antes de push
```

Si `.git` está roto o ausente (p. ej. en discos exFAT), `push.sh` lo re-inicializa,
mueve el `.git` previo a `.git.broken` y configura el remoto por defecto. También
verifica que `.env` esté ignorado antes de pushear. Equivalente en PowerShell: `.\push.ps1`.

## Qué Editar (Regla General)

- Comportamiento de UI de páginas: `public/assets/js/`
- Nuevo wrapper de ruta de API: `public/api/`
- Validación de petición y orquestación: `app/Controllers/Api/`
- Reglas de negocio: `app/Services/`
- SQL/acceso a datos: `app/Repositories/`
- Partials/layout HTML compartidos: `app/Views/Shared/` y `templates/`
- Markupp de páginas migradas: `app/Views/Pages/<Caracteristica>/Index.php`
- Modales compartidos: `app/Views/Shared/Modals/`

Mantén delgados `public/*.php` y `public/api/*.php`. Pon la lógica real en controladores, servicios y repositorios.

## Mapa del Proyecto

```text
hospital/
|- app/
|  |- bootstrap.php            (autoload y arranque de la app)
|  |- Core/                    (Database, Auth, Router, ApiResponse, ModuleRegistry)
|  |- Controllers/             (orquestación HTTP)
|  |  |- Api/
|  |  `- Pages/                (controladores de página, BasePageController)
|  |- Services/                (flujos de negocio; incluye Admin/)
|  |- Repositories/            (SQL y persistencia)
|  |- Interfaces/              (contratos)
|  |- Helpers/                 (helpers compartidos)
|  |- Modules/                 (registro de módulos/navegación lateral)
|  |- Views/                   (vistas)
|  |  |- Pages/                (una carpeta por página migrada)
|  |  `- Shared/               (header, footer, modales compartidos)
|  |- ViewModels/              (modelado específico para vistas)
|  |- Domain/                  (clases centradas en el dominio)
|  `- Infrastructure/          (adaptadores técnicos)
|- public/
|  |- *.php                    (puntos de entrada web)
|  |- api/                     (wrappers de API)
|  |- admin/
|  |- assets/                  (css, js, i18n)
|  |- backend/                 (endpoints legados de alergias)
|  |- modal/                   (wrappers de compatibilidad hacia app/Views/Shared/Modals)
|  `- wwwroot/
|- migrations/
|  `- init.sql                 (esquema base consolidado)
|- database/
|  `- hospital_schema.sql      (volcado de esquema de referencia)
|- scripts/                    (scripts de mantenimiento)
|- templates/                  (wrappers de compatibilidad)
|- docs/
|- config/
|  `- db.php
|- src/                       (helpers PHP legados: auth, chat, patient)
|- package.json               (dependencias frontend: Bootstrap, DataTables, etc.)
|- .env.example               (plantilla de configuración; copiar a .env para personalizar)
|- run.ps1 / run.cmd / run.sh
|- build.ps1
`- push.ps1 / push.sh
```

## Flujo de Petición (Versión Simple)

Para llamadas de API:

1. El navegador llama a `/api/<caracteristica>_<accion>.php`.
2. El endpoint carga `app/bootstrap.php`.
3. El endpoint llama al controlador en `app/Controllers/Api/`.
4. El controlador valida/authentica y llama al servicio/repositorio.
5. El controlador devuelve JSON usando `App\Core\ApiResponse`.

Para cargas de página:

1. El navegador solicita `public/<pagina>.php`.
2. La página verifica autenticación con `App\Core\Auth` o delega en el controlador de página.
3. Las páginas migradas renderizan con `BasePageController::renderPage()`; las demás incluyen fragmentos de layout compartidos.
4. JavaScript llama a los endpoints de API para datos dinámicos.

## Convenciones de Nombres

- Endpoint de API: `<caracteristica>_<accion>.php`
  - Ejemplo: `encounters_list.php`
- Controlador de API: `<FeaturePlural>Controller`
  - Ejemplo: `PatientsController`
- Controlador de página: `<Feature>PageController`
  - Ejemplo: `ReportsPageController`
- Repositorio: `<Feature>Repository`
  - Ejemplo: `EncounterRepository`
- Servicio: `<Feature>Service`
  - Ejemplo: `PatientService`
- Vista de página: `app/Views/Pages/<Feature>/Index.php`
- Modal compartido: `app/Views/Shared/Modals/<entidad>_modal.php`

## Tareas Comunes para Principiantes

### Agregar una Nueva Acción de API

1. Crea el archivo wrapper en `public/api/`.
2. Agrega el método al controlador correspondiente en `app/Controllers/Api/`.
3. Agrega/actualiza la lógica de servicio en `app/Services/` (si es necesario).
4. Agrega/actualiza el acceso SQL en `app/Repositories/`.
5. Devuelve JSON consistente (`success`, `data`, `message`) vía `ApiResponse`.

### Agregar un Nuevo Campo de Página

1. Actualiza el HTML en la vista o parcial compartido.
2. Actualiza el payload de la petición JS en `public/assets/js/`.
3. Actualiza la validación del controlador.
4. Actualiza el SQL del repositorio y el esquema si es necesario.

## Notas de Seguridad

- No expongas las herramientas de setup en producción.
- Exige HTTPS y credenciales fuertes.
- Agrega CSRF y validación más estricta antes del despliegue en producción.

## Documentación Adicional

- Guía para principiantes: `docs/BEGINNER_GUIDE.md`
- Arquitectura detallada: `docs/ARCHITECTURE.md`
- Roadmap de migración MVC: `docs/MVC_MIGRATION_ROADMAP.md`
- Comandos de ejecución: `scripts/RUN.md`
