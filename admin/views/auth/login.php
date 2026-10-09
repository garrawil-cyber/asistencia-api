<?php
use App\Core\Session;
Session::start();
$error    = Session::get('login_error');
$username = Session::get('login_username', '');
Session::remove('login_error');
Session::remove('login_username');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/asistencia-api/admin/assets/css/admin.css" rel="stylesheet">
</head>
<body>

<div class="login-container">
    <div class="card login-card">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-mortarboard-fill text-primary" style="font-size: 3rem;"></i>
                <h3 class="mt-2">Panel Admin</h3>
                <p class="text-muted small">Control de Asistencia Docente</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/asistencia-api/admin/login">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($username) ?>"
                               required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                </button>
            </form>

            <p class="text-center text-muted small mt-4 mb-0">
                Solo administradores y coordinadores
            </p>
        </div>
    </div>
</div>

</body>
</html>