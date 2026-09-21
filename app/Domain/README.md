# Dominio

Equivalente ASP.NET MVC: `Domain/`.

Usa esta carpeta para objetos y reglas del dominio de negocio que no deben depender de HTTP ni de detalles de persistencia.

Estado actual: carpeta destino reservada; la lógica de negocio vive principalmente en `app/Services` y los helpers en `app/Helpers`. Mueve aquí gradualmente las reglas puras de dominio cuando se desacoplen de la persistencia.
