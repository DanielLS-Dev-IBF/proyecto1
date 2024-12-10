<!-- views/registro.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <!-- Incluir Bootstrap CSS para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Registro de Usuario</h2>

    <!-- Mostrar mensajes de error si existen -->
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulario de Registro -->
    <form action="index.php?controller=usuario&action=store" method="POST">
        <div class="mb-3">
            <label for="nombre_completo" class="form-label">Nombre Completo</label>
            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" value="<?= htmlspecialchars($nombre_completo ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña (mínimo 6 caracteres)</label>
            <input type="password" class="form-control" id="password" name="password" minlength="6" required>
        </div>
        <div class="mb-3">
            <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" minlength="6" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($direccion ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="codigo_postal" class="form-label">Código Postal</label>
            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" value="<?= htmlspecialchars($codigo_postal ?? '') ?>" required pattern="\d{4,5}" title="El código postal debe tener entre 4 y 5 dígitos.">
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono ?? '') ?>" required pattern="\d{7,15}" title="El teléfono debe tener entre 7 y 15 dígitos.">
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
        <a href="index.php?controller=usuario&action=login" class="btn btn-link">¿Ya tienes una cuenta? Inicia sesión</a>
    </form>
</div>
<!-- Incluir Bootstrap JS para funcionalidades interactivas -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
