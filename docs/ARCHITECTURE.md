# Arquitectura (MVC)

Este proyecto sigue una estructura MVC incremental manteniendo estables las rutas URL existentes.

## Mapa de Carpetas

- `public/`
  - Puntos de entrada web y rutas de API (`/api/*.php`).
  - Mantén estos archivos delgados: parsean la petición y llaman a un controlador.
- `app/Core/`
  - Infraestructura transversal: conexión a BD, auth/sesión, helpers de respuesta API, registro de módulos.
  - Clases actuales: `Database`, `Auth`, `ApiResponse`, `Router`, `Controller`, `View`, `ModuleRegistry`.
  - Base técnica actual mientras avanza la migración hacia `app/Infrastructure/`.
- `app/Infrastructure/`
  - Capa de infraestructura al estilo ASP.NET para servicios técnicos y adaptadores.
- `app/Controllers/`
  - Lógica de orquestación de peticiones (entrada/salida HTTP).
  - `app/Controllers/Api/` para controladores de API.
  - `app/Controllers/Pages/` para controladores de página; `BasePageController` es el renderizador compartido del layout autenticado (`renderPage()`).
  - `app/Controllers/SetupController.php` para el flujo de setup.
- `app/Views/`
  - Capa de vistas al estilo ASP.NET.
  - `app/Views/Pages/<Feature>/Index.php`: markup de cada página migrada.
  - `app/Views/Shared/`: fragmentos de layout compartidos (header, footer) y modales en `app/Views/Shared/Modals/`.
- `app/ViewModels/`
  - DTOs específicos de vista preparados por los controladores.
- `app/Services/`
  - Servicios de lógica de negocio y orquestación más allá del CRUD simple.
  - Incluye `app/Services/Admin/` y `app/Services/PrintService.php`.
- `app/Interfaces/`
  - Contratos de repositorios y servicios para desacoplar implementaciones.
- `app/Domain/`
  - Carpeta destino (estilo ASP.NET) para clases centradas en el dominio.
- `app/Repositories/`
  - Clases de repositorio que encapsulan el acceso a datos (SQL con PDO). Aquí viven todos los repositorios, incluido `SetupRepository`.
- `src/`
  - Wrappers de compatibilidad legados usados por páginas/scripts antiguos.
  - Están obsoletos: las páginas públicas ahora usan clases OOP directas bajo `app/`.
- `app/Helpers/`
  - Clases helper reutilizables para auth, paciente y chat.
- `config/`
  - Bootstrap de configuración por compatibilidad (`db.php` delega en `app/Core/Database.php`).
- `templates/`
  - Wrappers de compatibilidad que redirigen a `app/Views/Shared`.
- `public/modal/`
  - Wrappers de compatibilidad que redirigen a `app/Views/Shared/Modals`.
- `public/backend/`
  - Endpoints legados del módulo de alergias de pacientes.
- `public/wwwroot/`
  - Raíz estática destino al estilo ASP.NET.
  - Los assets estáticos existentes siguen en `public/assets` durante la migración.

## Flujo de Petición

1. El navegador llama al endpoint en `public/api/...`.
2. El endpoint carga `app/bootstrap.php` y llama al método del controlador.
3. El controlador usa servicio y/o repositorio para el acceso a datos.
4. El controlador devuelve JSON vía `App\Core\ApiResponse`.

Para páginas migradas:

1. El navegador solicita `public/<pagina>.php`.
2. El entrypoint carga el bootstrap e invoca `<Feature>PageController::index()`.
3. `BasePageController::renderPage()` renderiza la vista dentro del layout autenticado.

## Convenciones

- Mantén el SQL en los repositorios, no en los endpoints ni controladores.
- Mantén los archivos endpoint por debajo de 10 líneas cuando sea posible.
- Los controladores de página se enfocan en orquestación; usa `BasePageController::renderPage()` para layouts estándar.
- Usa `App\Core\Auth` para todas las verificaciones de auth/roles.
- Usa `App\Core\ApiResponse` para respuestas JSON consistentes.
- Mantén los módulos front-end limpios: capa cliente de API, estado de modal UI, y renderizador/controlador de página.
- Los modales se definen una sola vez en `app/Views/Shared/Modals/`; `public/modal/*.php` son solo wrappers de compatibilidad.
- La creación/edición de entidades se hace con modales sobre la página de listado (patrón actual de pacientes), evitando páginas editor independientes.

## Áreas Migradas

- API de pacientes (`patients_list/create/update/delete.php`)
- Backend de alergias de pacientes (`public/backend/patient_allergies_*.php`, `public/backend/allergis.php` -> `PatientAllergiesController` -> `PatientAllergyService` -> `PatientAllergyRepository`)
- Página de listado de pacientes (`public/patients.php` -> `PatientsPageController::index()` -> `app/Views/Pages/Patients/Index.php`); la creación y edición de pacientes se hace con el modal compartido `patient_modal.php`; el editor independiente (`public/paciente.php`/`public/patient.php`, `PatientPageController`) fue eliminado.
- Modal selector de pacientes (`patient_list_modal.php`) usado por encuentros y seguimiento pediátrico.
- Página de alergias (`public/alergias.php` -> `PatientAllergiesPageController::index()` -> `app/Views/Pages/PatientAllergies/Index.php`)
- Catálogo de medicamentos usa `MedicationsPageController` para la preparación protegida; su vista grande inline permanece en `public/medications.php` pendiente de extracción.
- API de chat (`chat_list.php`, `chat_send.php`)
- API de usuarios (`users_list.php` y endpoints CRUD de admin)
- Página de dashboard (`public/index.php` -> `DashboardPageController::index()` -> `app/Views/Pages/Dashboard/Index.php`)
- Alias de dashboard (`public/dashboard.php` -> `DashboardPageController::redirectLegacy()` -> `/`)
- Página de reportes (`public/reports.php` -> `ReportsPageController::index()` -> `app/Views/Pages/Reports/Index.php`)
- Seguimiento pediátrico (`public/seguimiento_pediatric.php` -> `SeguimientoPediatricPageController::authorize()`; vista inline pendiente de extracción)
- Tratamientos (`public/treatments.php` -> `TreatmentsPageController::authorize()`; vista inline pendiente de extracción)
- Página de citas (`public/appointments.php` -> `AppointmentsPageController::index()` -> `app/Views/Pages/Appointments/Index.php`)
- Página de altas (`public/altas.php` -> `AltasPageController::index()` -> `app/Views/Pages/Altas/Index.php`); tarjetas de encuentros abiertos (`encounters_list.php?status_not=closed`) con botón de alta que cierra vía `encounters_update.php`.
- Página de salud del chat (`public/chat_health.php` -> `ChatHealthPageController::index()` -> `app/Views/Pages/ChatHealth/Index.php`)
- Vista de respuesta de error (`ErrorController` -> `app/Views/Pages/Error/Index.php`)
- Páginas de impresión (`public/print.php`, `public/print_followup.php` -> `PrintPageController` -> `app/Views/Pages/Print/`)
- Registro (`public/register.php` -> `RegistrationPageController` -> `RegistrationService` -> `UserRepository`)
- Login (`public/login.php` -> `LoginPageController` -> `Auth`/`UserRepository`)
- Logout (`public/logout.php` -> `LogoutController` -> `Auth`)
- Perfil (`public/profile.php` -> `ProfilePageController` -> `ProfileService` -> `UserRepository`)
- Wrappers legados (`src/auth.php`, `src/patient.php`, `src/chat.php`) están obsoletos y ya no son usados por las páginas públicas actuales.
- Flujo de setup movido a MVC (`SetupController`, `SetupPageController`, `SetupRepository` en `app/Repositories/`) manteniendo la UI en `public/setup.php`.

## Características Eliminadas

- Radiología independiente (`radiologia.php`, APIs `radiologia_*`, `RadiologyRequestRepository`, tabla `radiology_requests`): integrada como tipo de examen `RAD` en la tabla unificada `exam_requests`. Las páginas `solicitud_de_radiologia.php`, `solicitud_de_examen.php` y `examen.php` fueron eliminadas.
- Tests (`tests.php`, `TestsController`, `TestRepository`, `TestService`, `tests.js`): eliminados; la tabla `tests` persiste en el esquema base.
- Historal de adolescentes (`adolescent_history.php` y su stack): eliminado.
- Editor de paciente independiente (`patient.php`): reemplazado por `patient_modal.php`.

## Base de Datos

- Las migraciones están consolidadas en un único archivo base: `migrations/init.sql`.
- El script base recrea el esquema desde cero e incluye:
  - Tablas de compatibilidad usadas por la UI/API actual (`users`, `patients`, `diagnostics`, `chat_messages`, `exam_types`, `exam_requests`), y
  - Entidades clínicas extendidas para historial hospitalario longitudinal y reportes.
- `database/hospital_schema.sql` es un volcado de referencia del esquema completo.

## Entidades Principales y Relaciones

- `patients` 1-* `encounters`
- `patients` 1-* `diagnostics`, `vitals`, `clinical_notes`
- `patients` 1-* `patient_conditions`, `patient_allergies`, `immunizations`
- `patients` 1-* `appointments`, `admissions`
- `admissions` 1-* `bed_movements`
- `encounters` 1-* `diagnostics`, `clinical_notes`, `treatment_plans`, `clinical_procedures`
- `exam_types` 1-* `exam_requests` (tipos: GENERAL, LAB, RAD)
- `diagnostics` 1-* `tests` (tabla conservada en esquema) y enlace opcional a `treatment_plans`
- `medications_catalog` 1-* `prescriptions` 1-* `treatment_administration`
- `users` actúa como actor clínico/administrativo en creación, actualización, asignación y auditoría

## Cobertura Estadística/Histórica

- Historia longitudinal del paciente: condiciones, alergias, diagnósticos, signos vitales, notas, tratamientos.
- Controles operativos del hospital: encuentros, citas, admisiones, movimientos de sala/cama.
- Ciclo de vida de medicación: prescripciones y dosis administradas.
- Gobernanza: `audit_logs` para trazabilidad de acciones sobre entidades.

## Próximas Migraciones Recomendadas

- Sigue `docs/MVC_MIGRATION_ROADMAP.md` para mover gradualmente la lógica restante a nivel de página desde `public/*.php` hacia controladores y vistas.
- Introduce un router simple (opcional) para reducir la cantidad de archivos endpoint.
- Agrega servicios en `app/Services/` si la lógica de negocio crece más allá de controladores/repositorios.
