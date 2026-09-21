# Comandos rápidos de ejecución/compilación

Este proyecto incluye scripts de conveniencia para Windows (PowerShell y CMD) para instalar dependencias del frontend y ejecutar un servidor PHP local para desarrollo.

Archivos:

- `build.ps1` - Instala paquetes npm (ejecuta `npm install`).
- `run.ps1` - Helper de PowerShell para iniciar el servidor integrado de PHP. Intenta con `php` en PATH, luego con `C:\xampp\php\php.exe`.
- `run.cmd` - Versión CMD que se comporta de forma similar y corre en la consola.

Uso (PowerShell):

1. Abre PowerShell en la raíz del proyecto.
2. Ejecuta el paso de compilación (la primera vez o cuando cambie package.json):

   .\build.ps1

3. Inicia el servidor de desarrollo PHP y abre el sitio en tu navegador:

   .\run.ps1

   Flags opcionales:
   - `-Port <puerto>`  ej. `.\run.ps1 -Port 8080`
   - `-NoOpen` para evitar que se abra el navegador automáticamente.
   - `-UseXamppPhp` para preferir el PHP de XAMPP en `C:\xampp\php\php.exe`.

Uso (cmd.exe):

1. Abre cmd.exe en la raíz del proyecto.
2. Ejecuta:

   run.cmd

Esto intentará iniciar el servidor integrado de PHP en `http://localhost:8000` sirviendo la carpeta `public/`.

Notas:
- Estos scripts son intencionalmente mínimos y seguros. No alteran tu base de datos ni inician/detienen servicios de XAMPP.
- Si prefieres Apache vía XAMPP, abre el Panel de Control de XAMPP y accede al sitio en `http://localhost/hospital/public` (o mueve el proyecto bajo `htdocs`).
