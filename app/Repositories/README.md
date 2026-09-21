# Repositorios

Equivalente ASP.NET MVC: `Repositories/`.

Esta carpeta contiene las clases de repositorio que encapsulan la persistencia (SQL con PDO vía `App\Core\Database`).

Convenciones:

- Una clase por entidad: `<Feature>Repository`.
- Implementa `RepositoryInterface` (ver `app/Interfaces/`) cuando aplique.
- Extiende `BaseRepository` para reutilizar el acceso al PDO.
- Ningún SQL fuera de esta carpeta.
