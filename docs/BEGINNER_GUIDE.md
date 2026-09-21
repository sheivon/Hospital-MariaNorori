# Guía para Principiantes

Esta guía es para desarrolladores nuevos en este proyecto.

## 1) Entiende el Panorama General

La aplicación sigue un flujo en capas:

1. `public/*.php` punto de entrada de página
2. `public/api/*.php` wrapper de API
3. `app/Controllers/Api/*Controller.php` manejo de la petición
4. `app/Services/*Service.php` reglas de negocio
5. `app/Repositories/*Repository.php` SQL y acceso a base de datos

Para páginas migradas, el entrypoint llama a `app/Controllers/Pages/<Feature>PageController.php`, que renderiza la vista desde `app/Views/Pages/<Feature>/Index.php`.

Si solo recuerdas una cosa: mantén delgados los archivos de página y API, y pon la lógica en servicios/repositorios.

## 2) Primeros 30 Minutos

1. Inicia la app:
   - PowerShell: `./run.ps1`
   - CMD: `run.cmd`
2. Abre `http://localhost:8000/setup.php` e inicializa la base de datos.
3. Abre una página de característica (ejemplo: pacientes `/patients.php`).
4. Encuentra los archivos de página y API correspondientes.
5. Sigue la ruta controlador -> servicio -> repositorio.

## 3) Dónde Hacer Cambios

- Comportamiento de UI (JS frontend): `public/assets/js/`
- Archivo de URL de API: `public/api/`
- Validación de entrada y forma de respuesta: `app/Controllers/Api/`
- Lógica de negocio: `app/Services/`
- Consultas SQL: `app/Repositories/`
- Piezas de UI compartidas: `app/Views/Shared/`, `templates/`
- Markup de páginas migradas: `app/Views/Pages/<Feature>/`
- Modales compartidos: `app/Views/Shared/Modals/`

## 4) Lista de Verificación para Cambios Seguros

Antes de entregar cambios, verifica:

1. No pusiste SQL en archivos wrapper de página/API.
2. Reutilizaste `App\Core\Auth` para las verificaciones de autorización.
3. Las respuestas API se mantienen consistentes usando `App\Core\ApiResponse`.
4. El comportamiento nuevo está en servicio/repositorio cuando es posible.
5. La página de setup sigue funcionando en una BD local limpia.

## 5) Ejemplo: Agregar un Campo a un Formulario

1. Agrega el campo en el HTML/parcial (vista o modal).
2. Incluye el campo en el payload de la petición frontend.
3. Valida el campo en el controlador.
4. Aplica reglas de negocio en el servicio (si es necesario).
5. Persiste el campo en el SQL del repositorio.
6. Devuelve el campo nuevo en la respuesta API.

## 6) Errores Comunes

- Agregar lógica pesada directamente en `public/*.php`.
- Duplicar validación en varios lugares en vez de centralizarla.
- Devolver formas JSON inconsistentes entre acciones de API.
- Mezclar lógica de acceso a datos en los controladores.
- Crear páginas editor independientes en lugar de reutilizar el modal sobre el listado.

## 7) Lecturas Siguientes

- Detalles de arquitectura: `docs/ARCHITECTURE.md`
- Roadmap de migración MVC: `docs/MVC_MIGRATION_ROADMAP.md`
- Onboarding raíz: `README.md`
