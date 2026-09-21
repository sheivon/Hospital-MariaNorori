# Roadmap de Migración MVC

## Patrón Completado

Usa esta forma para las características migradas:

1. `public/<ruta>.php` carga el bootstrap e invoca un método del controlador de página.
2. `app/Controllers/Pages/<Feature>PageController.php` posee autorización, orquestación de petición y selección de layout.
3. `app/Views/Pages/<Feature>/Index.php` posee el markup, la inclusión de parciales de modal y el arranque del cliente de la característica.
4. `app/Controllers/Api`, `app/Services` y `app/Repositories` poseen API, reglas y persistencia.

`BasePageController::renderPage()` es el renderizador estándar del layout autenticado.

Para entidades con formulario, usa el patrón actual de pacientes: un único `<entidad>_modal.php` en `app/Views/Shared/Modals/` sirve para crear y editar desde el listado (sin página editor independiente).

## Páginas Pendientes de Extracción

### Flujos Medianos

- `public/medications.php`: tabla de catálogo de medicamentos y modal; las APIs del catálogo ya están respaldadas por MVC.
- `public/vitals.php`: tabla de signos vitales y modal; controlador de API ya existe.
- `public/diagnostics.php`: listado de diagnósticos y modal; controlador de API ya existe.
- `public/encounters.php`: tabla de encuentros y flujo de modales (`encounter_modal.php`, `patient_list_modal.php`).
- `public/encounter.php`: editor de encuentro independiente.

### Parciales (autorización vía controlador, vista inline)

- `public/seguimiento_pediatric.php`: usa `SeguimientoPediatricPageController::authorize()`; extraer la vista a `app/Views/Pages/`.
- `public/treatments.php`: usa `TreatmentsPageController::authorize()`; extraer la vista a `app/Views/Pages/`.
- `public/setup.php`: preparación de petición en `SetupPageController`; solo queda la vista legado grande.

### Deuda Técnica Conocida

- El backend de solicitudes de examen está completo: `ExamRequestsController` -> `ExamRequestService` -> `ExamRequestRepository`/`ExamTypeRepository` (endpoints `exam_requests_*.php`, `exam_types_list.php`).
- `public/modal/exam_request_modal.php` contiene markup real (no es wrapper) y no está incluido por ninguna página; moverlo a `app/Views/Shared/Modals/` al reconectar el flujo de solicitudes de examen.
- `public/modal/test_modal.php` apunta a una vista eliminada; eliminarlo.
- Corregido: `public/api/patient_get.php` llamaba a un método inexistente (`get()` en vez de `show()`) y rompía la edición de pacientes vía modal.
- Corregido: `public/api/chat_health.php` llamaba a `check()` en vez de `health()`.

## Características Eliminadas

- Eliminado: `public/patient.php` (editor independiente); creación/edición de pacientes vía `patient_modal.php` en el listado.
- Eliminado: `public/radiologia.php` + APIs/controlador/servicio/repositorio de radiología; los campos de radiología viven en `exam_requests` (tipo `RAD`).
- Eliminado: `public/solicitud_de_examen.php` y `public/solicitud_de_radiologia.php`; sustituidos por el flujo unificado de solicitudes de examen.
- Eliminado: `public/examen.php` (listado de exámenes) y su stack asociado.
- Eliminado: `public/tests.php` y todo su stack (`TestsController`, `TestRepository`, `TestService`, `tests.js`, vista del modal).
- Eliminado: `public/adolescent_history.php` y su stack.

## Reglas Para Cada Extracción Completa

- Preserva URLs de rutas, DOM IDs, IDs de modales y URLs de API en el primer movimiento.
- Mueve el markup y el código cliente inline juntos a la vista de la característica antes de refactorizar el JavaScript a un módulo separado.
- Valida la redirección de la ruta sin autenticar, luego valida la UI renderizada con sesión autenticada de navegador cuando esté disponible.
- No mezcles rediseño de UI ni cambios de contrato de API en el commit de extracción.
