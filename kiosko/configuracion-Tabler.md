📱 Configuración de la tablet como kiosko
PASO 1 — Instalar "Fully Kiosk Browser"
Fully Kiosk Browser es la app de kiosko más popular y funcional.

¿Por qué Fully Kiosk?
✅ Modo kiosko real (bloquea el acceso a todo)

✅ Auto-arranque al encender la tablet

✅ Auto-recarga si la página se cuelga

✅ Bloqueo de botones (no pueden salir)

✅ Programación de encendido/apagado

✅ Gratis (con limitaciones)

Instalación
Abre Google Play Store en la tablet

Busca: Fully Kiosk Browser

Instala (es gratis)

Versión de pago (~$10 USD): sin publicidad y con más funciones. Para producción vale la pena, pero para empezar la gratuita sirve.

PASO 2 — Configurar Fully Kiosk Browser
Abre la app y configurá:

2.1 — URL de inicio
Menú principal → Web Content Settings

Start URL:

text
http://192.168.0.19/asistencia-api/kiosko/
Guardar

2.2 — Modo pantalla completa
Menu → Display Settings

Fullscreen Mode: ✅ Activado

Hide Status Bar: ✅ Activado

Hide Navigation Bar: ✅ Activado

Screen Orientation: Landscape o Portrait (según cómo la montes)

2.3 — Auto-arranque
Menu → Device Management

Start on Boot: ✅ Activado

Launch App on Boot: ✅ Activado

Con esto, cuando la tablet se encienda, Fully Kiosk se abre automáticamente en modo pantalla completa.

2.4 — Mantener la pantalla encendida
Menu → Display Settings

Keep Screen On: ✅ Activado

Screen Brightness: 100% (o Auto si la tablet lo soporta)

2.5 — Auto-recarga
Menu → Web Content Settings

Auto Reload on Idle: ✅ Activado

Idle Time before Reload: 5 minutos

Esto hace que si la página se congela, se recargue sola.

2.6 — Bloqueo de salida
Menu → Device Management

Kiosk Mode (Lock Task Mode): ✅ Activado

Admin PIN: (crea un PIN que solo vos sepas)

⚠️ Con Kiosk Mode activado, los usuarios no pueden salir de la app sin el PIN. Es lo que querés para un kiosko público.

PASO 3 — Configuración del sistema Android
3.1 — Desactivar bloqueo de pantalla
Ajustes → Seguridad → Bloqueo de pantalla

Elegir: Ninguno

O si querés mantener seguridad:

Ajustes → Pantalla → Bloqueo automático

Cambiar a: Nunca o 30 minutos

3.2 — Desactivar ahorro de energía
Ajustes → Batería → Ahorro de energía

Desactivar

O agregar Fully Kiosk a las apps protegidas:

Ajustes → Batería → Uso de batería por app

Buscar Fully Kiosk

Elegir: Sin restricciones

3.3 — Desactivar WiFi sleep
En algunos Android la WiFi se apaga cuando la pantalla está apagada:

Ajustes → WiFi → Avanzado

"Mantener WiFi activada durante la suspensión" → Siempre

PASO 4 — Configuración de red
4.1 — IP fija para la tablet (recomendado)
Asigná una IP fija al kiosko para que siempre sea accesible:

Ajustes → WiFi → [tu red] → Modificar

Configuración IP: Estática

IP: 192.168.0.30 (una IP libre)

Puerta de enlace: 192.168.0.1 (tu router)

DNS: 8.8.8.8

Con esto, si mañana necesitás acceder a la tablet remotamente, siempre estará en la misma IP.

4.2 — Verificar conexión
Abrí Chrome en la tablet

Visitá: http://192.168.0.19/asistencia-api/kiosko/

Verificá: que muestra el QR

PASO 5 — Ubicación física
5.1 — Soporte
Opciones:

Opción	Precio	Recomendación
Soporte de pared	S/ 50-80	✅ Para entrada
Soporte de mesa	S/ 30-50	✅ Para escritorio
Caja de seguridad	S/ 150-200	⚠️ Si hay riesgo de robo
5.2 — Ubicación
Ideal:

Entrada principal del colegio

Altura de los ojos (1.5m aprox.)

Visible al entrar

Cerca de un enchufe

Zona con buena señal WiFi

5.3 — Carga
Cable USB de 2 metros (para llegar al enchufe)

Cargador de 2A (la tablet debe estar siempre conectada)

Fijar el cable con cinta o canaleta para que no esté colgando

PASO 6 — Pruebas
6.1 — Prueba de reinicio
Reiniciá la tablet

Esperado:

La tablet arranca

Fully Kiosk se abre automáticamente

Chrome carga el kiosko

Se muestra el QR

6.2 — Prueba de conexión
Con la tablet encendida, apuntá tu celular al QR

Escanear con la app Android

Verificar que registra la asistencia

6.3 — Prueba de estabilidad
Dejar la tablet encendida durante 24 horas

Verificar:

¿El QR sigue rotando?

¿La pantalla no se apagó?

¿La conexión WiFi se mantiene?

📋 Checklist de configuración
text
Tablet:
[ ] Comprar tablet (10 pulgadas, Android 10+)
[ ] Instalar Fully Kiosk Browser
[ ] Configurar URL: http://192.168.0.19/asistencia-api/kiosko/
[ ] Activar modo pantalla completa
[ ] Activar auto-arranque al encender
[ ] Activar "mantener pantalla encendida"
[ ] Activar auto-recarga
[ ] Activar Kiosk Mode con PIN

Sistema Android:
[ ] Desactivar bloqueo de pantalla
[ ] Desactivar ahorro de energía
[ ] Configurar IP fija en WiFi
[ ] Verificar que Chrome carga el kiosko

Ubicación:
[ ] Comprar soporte (pared o mesa)
[ ] Fijar la tablet en la entrada
[ ] Conectar el cargador permanentemente
[ ] Verificar buena señal WiFi

Pruebas:
[ ] Reiniciar la tablet → verificar auto-arranque
[ ] Escanear QR con la app → verificar registro
[ ] Dejar 24 horas encendida → verificar estabilidad
💰 Presupuesto estimado
Opción económica (S/ 650)
text
Tablet Lenovo Tab M10         S/ 550
Soporte de mesa               S/  30
Cable USB de 2m               S/  15
Cargador 2A                   S/  25
Protector de pantalla         S/  30
─────────────────────────────
Total                         S/ 650
Opción recomendada (S/ 900)
text
Tablet Samsung Galaxy Tab A8  S/ 700
Soporte de pared              S/  70
Cable USB de 2m               S/  15
Cargador 2A                   S/  25
Protector de pantalla         S/  30
Caja de seguridad             S/ 150
─────────────────────────────
Total                         S/ 990
Opción premium (S/ 1400)
text
Tablet Xiaomi Pad 6          S/ 1200
Soporte de pared profesional  S/  100
Cable USB de 2m               S/   15
Cargador 2A                   S/   25
Caja antivandálica            S/  250
─────────────────────────────
Total                         S/ 1590
📄 Alternativa: Kiosko en tablet SIN Fully Kiosk
Si no querés depender de la app Fully Kiosk, hay alternativas:

Alternativa A — Chrome normal + Auto-inicio
Instalar Chrome en la tablet

Agregar el kiosko como "acceso directo" en la pantalla de inicio

Configurar el lanzador para abrir el acceso directo automáticamente

Desactivar el bloqueo de pantalla, suspensión, etc.

Ventajas: no requiere app extra
Desventajas: usuarios pueden salir fácilmente

Alternativa B — Chrome + Tasker
Usar Tasker para automatizar:

Auto-abrir Chrome al encender

Bloquear navegación

Auto-recargar cada X tiempo

Ventajas: muy personalizable
Desventajas: requiere comprar Tasker (~$3 USD) y saber configurarlo

Mi recomendación: Fully Kiosk es la opción más simple y completa.

❓ Contéstame
¿Vas a comprar una tablet o ya tenés una disponible?

¿Qué tamaño de pantalla preferís? (10 u 11 pulgadas)

¿Vas a fijarla en pared o ponerla sobre una mesa?

📌 Plan sugerido
Si comprás la tablet hoy:
Semana 1:

Comprar tablet

Configurar Fully Kiosk

Instalar en la entrada del colegio

Probar con 3-5 docentes

Semana 2:

Recolectar feedback

Ajustar configuración

Probar con más docentes

Semana 3:

Rollout completo a los 22 docentes

Capacitación al personal administrativo

Mes 2:

Sistema en producción estable

Agregar mejoras según feedback

💭 Reflexión
La tablet es la decisión correcta. Las ventajas son enormes:

Económica: S/ 650-900 vs S/ 1500+ de una PC

Compacta: no ocupa media entrada

Silenciosa: no hay ventiladores

Eficiente: 10W vs 200W de la PC

Profesional: se ve moderna y limpia

Confiable: Android es estable para este uso

Con S/ 650 tenés el kiosko funcionando profesionalmente. 🎯

📌 Cierre de la sesión
Son las 4:40 PM del lunes. Hoy lograste:

Mañana:

Card "Estado del día"

Botón dinámico

Sonido + vibración

Endpoint nuevo

Tarde:

Commit + push del Android

Commit + push del backend

Kiosko en modo pantalla completa

Decisión de hardware: tablet

Todo el sistema está 100% completo y en producción. 🏆

🌙 Descansa
Cerrá el kiosko de Chrome con Alt + F4. Cerrá Android Studio. Cerrá PowerShell.

Todo está respaldado y funcionando.

Mañana:

Si tenés la tablet: configurar Fully Kiosk

Si vas a comprarla: preparar los 22 docentes en el panel admin

Distribuir el APK a los primeros 3-5 docentes

¡Excelente trabajo hoy, Wilmer! 🏆✨

— Nos vemos mañana para el rollout. 👋

