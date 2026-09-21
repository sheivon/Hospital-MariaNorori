# Infraestructura

Equivalente ASP.NET MVC: `Infrastructure/`.

Usa esta carpeta para componentes técnicos transversales (logging, adaptadores de caché, clientes externos, almacenamiento de archivos, integración con frameworks).

Estado actual: los componentes de bajo nivel viven en `app/Core` (`Database`, `Auth`, `ApiResponse`, `Router`, `View`, `ModuleRegistry`) y pueden moverse aquí gradualmente.
