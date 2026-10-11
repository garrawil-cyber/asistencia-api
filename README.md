# Sistema de Control de Asistencia Docente — Backend

Backend completo en PHP puro (MVC) + MySQL para el sistema de control de asistencia docente.

Incluye 3 componentes en un mismo proyecto:
- **API REST** (JSON) para la app Android
- **Panel Admin** (HTML + Bootstrap) para gestión
- **Kiosko web** (HTML) que muestra QR dinámico

---

## 📁 Estructura del proyecto
C:\wamp64\www\asistencia-api
├── app/ ← Código PHP compartido
│ ├── Core/ ← Clases base
│ │ ├── Router.php ← Enrutador con detección de basePath
│ │ ├── Database.php ← Singleton PDO
│ │ ├── Request.php ← Wrapper de 
G
E
T
/
G
​
 ET/_POST/json
│ │ ├── Response.php ← Respuestas JSON estandarizadas
│ │ ├── Session.php ← Sesiones PHP (para panel admin)
│ │ ├── JWT.php ← Tokens JWT (para API)
│ │ └── DateHelper.php ← Fechas en español sin strftime
│ │
│ ├── Controllers/ ← Controllers de la API
│ │ ├── AuthController.php
│ │ ├── AsistenciaController.php
│ │ └── QrController.php
│ │
│ ├── Models/ ← Modelos compartidos
│ │ ├── Usuario.php ← Login, tokens, logs
│ │ ├── Docente.php ← CRUD de docentes
│ │ ├── Asistencia.php ← Marcación y reportes
│ │ └── QrToken.php ← Tokens QR dinámicos
│ │
│ ├── Middleware/
│ │ └── AuthMiddleware.php ← Validación JWT + API Key
│ │
│ └── Admin/ ← Código exclusivo del panel admin
│ ├── Controllers/
│ │ ├── AdminAuthController.php ← Login con sesiones
│ │ ├── DashboardController.php ← KPIs + gráfico
│ │ ├── DocenteController.php ← CRUD docentes
│ │ ├── ReporteController.php ← Reportes + exportar Excel/PDF
│ │ ├── PerfilController.php ← Mi perfil + cambio password
│ │ └── LogController.php ← Logs de actividad
│ └── Middleware/
│ └── SessionMiddleware.php ← Protección de rutas admin
│
├── public/ ← API REST (JSON)
│ ├── index.php ← Front controller de la API
│ └── .htaccess
│
├── admin/ ← Panel Admin
│ ├── index.php ← Front controller del panel
│ ├── .htaccess
│ ├── views/
│ │ ├── layouts/ ← header.php, footer.php
│ │ ├── auth/ ← login.php
│ │ ├── dashboard/ ← index.php
│ │ ├── docentes/ ← index, crear, editar
│ │ ├── reportes/ ← index.php
│ │ ├── perfil/ ← index.php
│ │ └── logs/ ← index.php
│ └── assets/
│ ├── css/admin.css ← Estilos del panel
│ └── js/admin.js ← SweetAlert, validaciones, toasts
│
├── kiosko/ ← Kiosko HTML
│ └── index.html ← QR dinámico con auto-refresh
│
├── config/
│ └── config.php ← Credenciales BD, JWT, timezone
│
├── scripts/ ← Scripts de utilidad
│ ├── crear_usuarios_prueba.php
│ ├── fix_passwords.php
│ └── debug_login.php
│
├── database/
│ └── control_asistencia_docentes.sql
│
├── vendor/ ← Dependencias Composer
├── composer.json
├── composer.lock
├── .htaccess ← Rewrite global
└── README.md ← Este archivo


---

## 🚀 URLs del sistema

| Componente | URL | Descripción |
|-----------|-----|-------------|
| **API REST** | `http://192.168.0.19/asistencia-api/api/v1/...` | Endpoints JSON |
| **Panel Admin** | `http://192.168.0.19/asistencia-api/admin/` | Interfaz web |
| **Kiosko** | `http://192.168.0.19/asistencia-api/kiosko/` | QR dinámico |

---

## 👥 Usuarios y credenciales

### Panel Admin
| Usuario | Password | Rol |
|---------|----------|-----|
| `admin` | `admin123` | Administrador total |
| `coordinador` | `coord123` | Aprueba justificaciones |

### API para docentes (app Android)
| Usuario | Password | Rol |
|---------|----------|-----|
| `jperez` | `docente123` | Docente |
| `mlopez` | `docente123` | Docente |

> ⚠️ **IMPORTANTE:** cambiar todas las contraseñas en producción.

---

## 🔌 Endpoints de la API

### Autenticación

| Método | Endpoint | Auth | Descripción |
|--------|----------|------|-------------|
| POST | `/api/v1/auth/login` | Público | Login → devuelve JWT |
| POST | `/api/v1/auth/logout` | JWT | Revoca el token |
| GET | `/api/v1/auth/me` | JWT | Datos del usuario actual |

### Asistencia

| Método | Endpoint | Auth | Descripción |
|--------|----------|------|-------------|
| POST | `/api/v1/asistencia/marcar` | JWT docente | Registra entrada/salida con QR |
| GET | `/api/v1/asistencia/hoy` | JWT docente | Estado de hoy |
| GET | `/api/v1/asistencia/historial` | JWT | Historial (filtrable) |

### Justificaciones (Panel Admin)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/admin/justificaciones` | Lista con filtros |
| GET | `/admin/justificaciones/crear` | Formulario nueva |
| POST | `/admin/justificaciones/crear` | Guardar |
| GET | `/admin/justificaciones/ver/{id}` | Ver detalle |
| POST | `/admin/justificaciones/responder/{id}` | Aprobar o rechazar |

### QR dinámico

| Método | Endpoint | Auth | Descripción |
|--------|----------|------|-------------|
| POST | `/api/v1/qr/actual` | API Key | Devuelve el QR vigente |
| GET | `/api/v1/qr/imagen` | Público | Renderiza el PNG del QR |

**Header de autenticación JWT:** `Authorization: Bearer <token>`
**Header de API Key (kiosko):** `X-API-Key: <clave>`

---

## 🎯 Funcionalidades

### Backend API
- ✅ Autenticación con JWT (login/logout)
- ✅ Autorización por roles (admin / coordinador / docente)
- ✅ API Key para kioskos
- ✅ QR dinámico con expiración de 60 segundos
- ✅ Marcación de entrada/salida
- ✅ Cálculo automático de tardanza (tolerancia 5 min)
- ✅ Zona horaria `America/Lima` configurada

### Panel Admin
- ✅ Login con sesiones PHP (`httpOnly`, `SameSite`)
- ✅ Dashboard con KPIs + gráfico Chart.js
- ✅ CRUD completo de docentes
- ✅ Filtros colapsables + paginación
- ✅ SweetAlert para confirmaciones + preview de datos
- ✅ Validaciones JS (DNI, email, teléfono, username)
- ✅ Reportes filtrables (fecha, docente, estado)
- ✅ Exportar Excel (.xls formateado con estilos)
- ✅ Exportar PDF (Dompdf)
- ✅ Mi Perfil + cambio de contraseña con barra de fortaleza
- ✅ Logs de actividad (auditoría completa)
- ✅ Toast de notificaciones (Toastify)
- ✅ Módulo de Justificaciones (crear, ver detalle, aprobar, rechazar)

### Kiosko
- ✅ QR dinámico rotando cada 60 segundos
- ✅ Barra de progreso visual
- ✅ Auto-refresh
- ✅ Diseño responsive

---

## 🔧 Instalación desde cero

### Requisitos previos
- **PHP** 8.0 o superior
- **MySQL** 5.7 / 8.0 / 9.0+
- **Apache** con `mod_rewrite` habilitado
- **Composer** instalado
- **WampServer** (o XAMPP, Laragon, etc.)

### Pasos

#### 1. Copiar el proyecto


#### 2. Instalar dependencias de Composer
```powershell
cd C:\wamp64\www\asistencia-api
composer install

CREATE DATABASE control_asistencia_docentes 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

& "C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe" -u root control_asistencia_docentes < "database\control_asistencia_docentes.sql"

return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'control_asistencia_docentes',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4'
    ],
    'jwt' => [
        'secret' => 'CAMBIA_ESTA_CLAVE_POR_UNA_LARGA_Y_ALEATORIA',
        'expira' => 60 * 60 * 8  // 8 horas
    ],
    'app' => [
        'debug'    => true,
        'timezone' => 'America/Lima'
    ]
];

<VirtualHost _default_:80>
    ServerName localhost
    ServerAlias localhost
    DocumentRoot "${INSTALL_DIR}/www"
    <Directory "${INSTALL_DIR}/www/">
        Options +Indexes +Includes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted          ← ⚠️ Cambiar de "Require local"
    </Directory>
</VirtualHost>

'db' => [
    'host' => 'localhost',
    'name' => 'control_asistencia_docentes',
    'user' => 'root',
    'pass' => ''    // ⚠️ Cambiar en producción
],
'jwt' => [
    'secret' => 'CAMBIA_ESTO',    // ⚠️ Cambiar
    'expira' => 60 * 60 * 8
],
'app' => [
    'timezone' => 'America/Lima'
]

kiosko/index.html
javascript
const API_BASE = 'http://192.168.0.19/asistencia-api';
const API_KEY = 'AQUI_VA_TU_API_KEY_DEL_KIOSKO';
La API Key se genera en la tabla api_keys:

sql
SELECT api_key FROM api_keys WHERE activo = 1;
🗄️ Base de datos
Tablas
Tabla	Propósito
usuarios	Usuarios del sistema (admin, coordinador, docente)
roles	Roles disponibles
docentes	Datos personales de docentes
asistencias	Registros de entrada/salida
estados_asistencia	presente, tarde, ausente, justificado, falta_salida
qr_tokens	Tokens QR temporales con expiración
api_keys	Claves de kioskos
logs_actividad	Auditoría del sistema
configuracion	Configuración dinámica
horarios	Horarios de docentes
cursos	Cursos
secciones	Secciones (1A, 1B, etc.)
grados	Grados académicos
justificaciones	Ausencias justificadas
anio_escolar	Año escolar
tokens_sesion	Tokens JWT emitidos
Backup
powershell
& "C:\wamp64\bin\mysql\mysql9.1.0\bin\mysqldump.exe" -u root control_asistencia_docentes | Out-File -Encoding UTF8 "backup.sql"
Restaurar
powershell
& "C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe" -u root control_asistencia_docentes < "backup.sql"
⏰ Cron jobs recomendados
Marcar faltas de salida (23:00 cada día)
sql
UPDATE asistencias a
JOIN estados_asistencia e ON e.codigo = 'falta_salida'
SET a.estado_id = e.id,
    a.observacion = 'No marcó salida'
WHERE a.fecha = CURDATE()
  AND a.hora_entrada IS NOT NULL
  AND a.hora_salida IS NULL;
Programar en el Programador de Tareas de Windows o crontab.

Limpiar tokens QR expirados (cada hora)
sql
DELETE FROM qr_tokens 
WHERE expira_en < DATE_SUB(NOW(), INTERVAL 1 DAY);
🐛 Problemas comunes
Problema	Causa	Solución
403 Forbidden desde red local	Apache restrictivo	Require all granted en httpd-vhosts.conf
Class not found	Namespace no coincide	composer dump-autoload
Cannot redeclare...	Método duplicado	Verificar el archivo
Warning strftime() deprecated	PHP 8.1+	Ya resuelto con DateHelper
La app Android no conecta	IP incorrecta	Verificar RetrofitClient.kt
QR expira muy rápido	Duración en config	qr_duracion_segundos en tabla configuracion
📞 Contacto
Desarrollador: Wilmer Ramírez García
Email: garrawil@gmail.com

📅 Historial
Octubre 2026 — Versión 1.0 completa

Backend API REST con JWT

Panel Admin con Bootstrap + Chart.js

Kiosko HTML con QR dinámico

Logs de actividad y auditoría

© 2026 · Sistema de Control de Asistencia Docente · v1.0

text

---

## 📄 README #2 — App Android

**Ubicación:** `C:\AsistenciaDocentes\README.md`

```markdown
# AsistenciaDocentes — App Android

App Android nativa para que los docentes marquen su asistencia escaneando el QR del kiosko.

Desarrollada en **Kotlin** + **Jetpack Compose**.

---

## 🚀 Quick Start

### Requisitos
- **Android Studio** Hedgehog (2023.1.1) o superior
- **JDK** 17+
- **Celular Android** 7.0 (API 24) o superior
- Backend **`asistencia-api`** corriendo en la misma red WiFi

### Instalación

1. **Abrir el proyecto** en Android Studio:
C:\AsistenciaDocentes\

text

2. **Esperar** a que Gradle haga el primer sync (puede tardar 3-5 min)

3. **Editar la URL del backend** en:
app/src/main/java/pe/colegio/asistencia/data/api/RetrofitClient.kt

text
```kotlin
private const val BASE_URL = "http://192.168.0.19/asistencia-api/"
⚠️ Cambiar la IP por la de tu PC en la red local.

Conectar el celular por USB:

Activar Depuración USB en Opciones de Desarrollador

Aceptar el diálogo "Permitir depuración USB"

Ejecutar la app con Shift + F10 (o click en ▶️)

Login con:

Usuario: jperez

Contraseña: docente123

📱 Funcionalidades
### Implementadas ✅
- Login con JWT
- Persistencia de sesión (DataStore)
- Home con nombre del docente
- Escáner QR con CameraX + ML Kit
- Marcación de entrada/salida
- Snackbar con feedback al usuario
- Logout funcional
- Manejo de errores con mensajes amigables
- **Ver Historial de asistencias** con filtros de rango (Semana / Este mes / Mes anterior / Este año)
- **Auto-login** con verificación de sesión (splash screen)
- **Interceptor JWT automático** (el token se agrega desde el SessionManager en cada petición)

### Pendientes ⏳
- Modo offline

Modo offline

🏗️ Arquitectura
Patrón MVVM con separación por capas:

text
app/src/main/java/pe/colegio/asistencia/
│
├── data/                             # Capa de datos
│   ├── api/
│   │   ├── ApiService.kt             # Interfaz Retrofit
│   │   └── RetrofitClient.kt         # Configuración HTTP
│   │
│   ├── model/                        # DTOs (Data Transfer Objects)
│   │   ├── LoginRequest.kt
│   │   ├── LoginResponse.kt
│   │   ├── MarcarRequest.kt
│   │   └── MarcarResponse.kt
│   │
│   ├── repository/                   # Repositorios (lógica de negocio)
│   │   ├── AuthRepository.kt
│   │   └── AsistenciaRepository.kt
│   │
│   └── session/
│       └── SessionManager.kt         # DataStore para JWT
│
├── ui/                               # Capa de presentación
│   ├── screens/                      # Pantallas Compose
│   │   ├── LoginScreen.kt
│   │   ├── HomeScreen.kt
│   │   └── QrScannerScreen.kt
│   │
│   ├── viewmodel/                    # ViewModels
│   │   └── LoginViewModel.kt
│   │
│   └── theme/                        # Colores y temas Compose
│       ├── Color.kt
│       ├── Theme.kt
│       └── Type.kt
│
└── MainActivity.kt                   # Entry point + Navigation
Flujo de navegación
text
LoginScreen → HomeScreen → QrScannerScreen
    ↑            ↓              ↓
    └── Logout ──┘              │
                                │
                     (marcar entrada/salida)
                                │
                                ▼
                            HomeScreen
                         (con snackbar)
🔧 Configuración
RetrofitClient.kt
kotlin
object RetrofitClient {
    private const val BASE_URL = "http://192.168.0.19/asistencia-api/"
    // ... resto de configuración
}
Para cambiar la IP del backend: editar la constante BASE_URL.

AndroidManifest.xml
Permisos requeridos:

xml
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
<uses-permission android:name="android.permission.CAMERA" />

<uses-feature android:name="android.hardware.camera" android:required="true" />
Y en <application>:

xml
android:usesCleartextTraffic="true"
⚠️ usesCleartextTraffic es necesario porque el backend usa HTTP sin cifrar. En producción se debe usar HTTPS.

📦 Dependencias principales
Del app/build.gradle.kts:

kotlin
// Compose
implementation(platform("androidx.compose:compose-bom:2024.12.01"))
implementation("androidx.compose.ui:ui")
implementation("androidx.compose.material3:material3")
implementation("androidx.activity:activity-compose:1.9.3")

// Navigation
implementation("androidx.navigation:navigation-compose:2.8.5")

// Retrofit + OkHttp
implementation("com.squareup.retrofit2:retrofit:2.11.0")
implementation("com.squareup.retrofit2:converter-gson:2.11.0")
implementation("com.squareup.okhttp3:okhttp:4.12.0")
implementation("com.squareup.okhttp3:logging-interceptor:4.12.0")

// Coroutines
implementation("org.jetbrains.kotlinx:kotlinx-coroutines-android:1.8.0")

// ViewModel
implementation("androidx.lifecycle:lifecycle-viewmodel-compose:2.8.3")
implementation("androidx.lifecycle:lifecycle-runtime-compose:2.8.3")

// DataStore
implementation("androidx.datastore:datastore-preferences:1.1.1")

// CameraX
implementation("androidx.camera:camera-core:1.4.0")
implementation("androidx.camera:camera-camera2:1.4.0")
implementation("androidx.camera:camera-lifecycle:1.4.0")
implementation("androidx.camera:camera-view:1.4.0")

// ML Kit Barcode
implementation("com.google.mlkit:barcode-scanning:17.3.0")

// Permisos
implementation("com.google.accompanist:accompanist-permissions:0.36.0")
🐛 Troubleshooting
La app no conecta al backend
Verificar misma WiFi en PC y celular

Verificar IP en RetrofitClient.kt

Probar desde Chrome del celular:

text
http://192.168.0.19/asistencia-api/
Debe responder con JSON.

MIUI (Xiaomi) mata la app al usar la cámara
Ir a Ajustes → Aplicaciones → AsistenciaDocentes

Activar "Sin restricciones" en ahorro de batería

Activar "Inicio automático"

Permisos: Cámara y Almacenamiento → Permitir

Error "401 Unauthorized" después del login
Token JWT expirado (8 horas por defecto)

Cerrar sesión y volver a hacer login

El escáner no detecta el QR
Acercar el celular al QR (20-30 cm)

Subir el brillo de la pantalla del kiosko

Evitar reflejos

Verificar que el QR no esté expirado (se regenera cada 60 seg)

Error "Cleartext HTTP traffic not permitted"
Verificar android:usesCleartextTraffic="true" en <application> del AndroidManifest

🔨 Build de producción
Generar APK firmada
Android Studio → Build → Generate Signed Bundle/APK

Seleccionar APK

Crear keystore (o usar existente)

Completar datos de firma

Seleccionar release

Finish

El APK se genera en:

text
app/build/outputs/apk/release/app-release.apk
Distribuir a los docentes
Enviar el APK por WhatsApp/email

O publicar en Google Play Store (requiere cuenta de desarrollador ~$25 USD único)

📞 Contacto
Desarrollador: Wilmer Ramírez García
Email: garrawil@gmail.com

📅 Historial
Octubre 2026 — Versión 1.0 completa

Login con JWT

Escáner QR con CameraX + ML Kit

Marcación de entrada/salida

Persistencia de sesión con DataStore

© 2026 · Sistema de Control de Asistencia Docente · v1.0

text

---

## 📋 Pasos para crear los archivos

### 1. Backend

**En VS Code:**

1. Click derecho en la carpeta raíz `asistencia-api/` → **New File**
2. Nombre: `README.md`
3. Pega el **README #1** completo
4. Guarda con `Ctrl + S`

### 2. App Android

**En VS Code o Android Studio:**

1. Abre el proyecto `C:\AsistenciaDocentes\`
2. Click derecho en la carpeta raíz → **New File**
3. Nombre: `README.md`
4. Pega el **README #2** completo
5. Guarda con `Ctrl + S`

---

## ✅ Después de crear los README

Verifica:

```powershell
# Backend
dir C:\wamp64\www\asistencia-api\README.md

# App
dir C:\AsistenciaDocentes\README.md
Ambos deben existir.

🎯 Estado del backup + documentación
text
[✅] Backend respaldado
[✅] App Android respaldada
[✅] Base de datos respaldada
[⏳] README backend — crear
[⏳] README app — crear
[ ] (Opcional) Git init + commit
Crea los 2 README con el contenido de arriba y avísame.

Después, si quieres, hacemos el Git init + commit para versionar todo. 🚀

¡Vamos que ya casi cerramos el backup! 💾

