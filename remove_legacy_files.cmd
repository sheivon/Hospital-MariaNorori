@echo off
setlocal enabledelayedexpansion
set files=public\patients.php public\patient.php public\allergis.php public\401.php public\404.php public\500.php
for %%F in (%files%) do (
  if exist "%%~fF" (
    del /f /q "%%~fF"
    echo Deleted: %%~fF
  ) else (
    echo Missing: %%~fF
  )
)
