@echo off
REM run.cmd - start PHP built-in server (Windows cmd)
SETLOCAL
SET PORT=8000
SET PHP_EXE=

IF EXIST "%~dp0\..\xampp\php\php.exe" (
  SET PHP_EXE=%~dp0\..\xampp\php\php.exe
) ELSE IF EXIST "C:\xampp\php\php.exe" (
  SET PHP_EXE=C:\xampp\php\php.exe
) ELSE (
  REM try php on PATH
  where php >nul 2>&1
  IF %ERRORLEVEL% EQU 0 (
    SET PHP_EXE=php
  ) ELSE (
    ECHO php executable not found. Install PHP or XAMPP and re-run.
    EXIT /B 1
  )
)

ECHO Starting PHP built-in server on http://localhost:%PORT% (serving public) ...
"%PHP_EXE%" -S localhost:%PORT% -t public
ENDLOCAL
