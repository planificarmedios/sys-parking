@echo off
title Sistema Parking

echo =====================================
echo     Iniciando Sistema Parking
echo =====================================
echo.

REM ---------------------------------
REM 1. Cerrar navegadores abiertos
REM ---------------------------------

echo Cerrando navegadores...

taskkill /F /IM chrome.exe >nul 2>&1
taskkill /F /IM msedge.exe >nul 2>&1
taskkill /F /IM firefox.exe >nul 2>&1

echo Navegadores cerrados.
echo.

REM ---------------------------------
REM 2. Verificar Apache
REM ---------------------------------

echo Verificando Apache...

sc query Apache2.4 | find "RUNNING" >nul

if %errorlevel%==0 (
    echo Apache ya esta ejecutandose.
) else (
    echo Iniciando Apache...
    start "" "C:\xampp\apache_start.bat"
)

timeout /t 4 >nul
echo.

REM ---------------------------------
REM 3. Verificar MySQL
REM ---------------------------------

echo Verificando MySQL...

sc query mysql | find "RUNNING" >nul

if %errorlevel%==0 (
    echo MySQL ya esta ejecutandose.
) else (
    echo Iniciando MySQL...
    start "" "C:\xampp\mysql_start.bat"
)

timeout /t 5 >nul
echo.

REM ---------------------------------
REM 4. Abrir sistema normalmente
REM ---------------------------------

echo Abriendo sistema...

start http://localhost/sys_parking

echo.
echo Sistema listo.
echo.

exit