# Configuración del Kiosko

## Ubicación del archivo BAT
C:\AsistenciaDocentes\kiosko\abrir-kiosko.bat

## URL del kiosko
http://192.168.0.19/asistencia-api/kiosko/

## Auto-inicio
Método: Carpeta Startup de Windows
Ruta: %APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\abrir-kiosko.bat

## Cómo salir del modo kiosko
- Alt + F4 → Cerrar Chrome
- F11 → Alternar pantalla completa

## Configuración de energía
- Apagado de pantalla: Nunca
- Suspensión: Nunca
- Salvapantallas: Ninguno
- Bloqueo automático: Nunca

## Para reiniciar el kiosko manualmente
Ejecutar: C:\AsistenciaDocentes\kiosko\abrir-kiosko.bat

## Notas
- La PC debe estar siempre encendida durante el horario escolar
- La red WiFi debe ser estable
- Si se reinicia, el kiosko arranca automáticamente
- Monitorear que el QR siga rotando (cada 60 seg)