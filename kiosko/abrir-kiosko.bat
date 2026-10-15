@echo off
title Kiosko de Asistencia
echo.
echo ========================================
echo   Iniciando Kiosko de Asistencia
echo ========================================
echo.
echo Cerrando Chrome si esta abierto...
taskkill /F /IM chrome.exe >nul 2>&1
timeout /t 2 /nobreak >nul
echo Abriendo el kiosko en modo pantalla completa...
start "" "C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk "http://192.168.0.19/asistencia-api/kiosko/" --no-first-run --disable-pinch --disable-session-crashed-bubble --disable-infobars
echo.
echo Kiosko iniciado. Para salir presione ALT+F4
exit