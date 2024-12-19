<?php include_once "views/TopNav.php"; ?>

<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el carrito no esté vacío
if (empty($_SESSION['carrito'])) {
    $_SESSION['error'] = 'Tu carrito está vacío. Agrega productos antes de proceder al pago.';
    header('Location: index.php?controller=carrito&action=index');
    exit();
}

// Obtener los datos necesarios del carrito y descuentos
$subtotal = isset($_SESSION['subtotal']) ? $_SESSION['subtotal'] : 0;
$descuento = isset($_SESSION['descuento']) ? $_SESSION['descuento'] : 0;
$gastos_envio = isset($gastos_envio) ? $gastos_envio : 0;
$total = $subtotal - $descuento + $gastos_envio;

// Manejar mensajes de éxito o error
if (isset($_SESSION['mensaje'])) {
    echo '<div class="alert alert-success alert-dismissible fade show text-start" role="alert">';
    echo htmlspecialchars($_SESSION['mensaje']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['mensaje']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show text-start" role="alert">';
    echo htmlspecialchars($_SESSION['error']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['error']);
}

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<link rel="stylesheet" href="css/Carrito.css">

<div class="container my-5 page-pago">
    <h1 class="mb-4 text-center">Confirmar Pedido</h1>
    <div class="row">
        <!-- Información del Perfil y Método de Pago -->
        <div class="col-lg-6">
            <div class="pago-summary p-4">
                <h4 class="mb-3 text-start">Información del Pedido</h4>
                <form action="index.php?controller=carrito&action=procesarPago" method="post">
                    <div class="row">
                        <!-- Campo de Nombre Completo -->
                        <div class="col-12 mb-3">
                            <label for="nombre_completo" class="form-label text-start d-block">Nombre Completo</label>
                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" placeholder="Juan Pérez" required>
                        </div>
                        
                        <!-- Campo de Dirección -->
                        <div class="col-12 mb-3">
                            <label for="direccion" class="form-label text-start d-block">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Calle Falsa 123, Ciudad, País" required>
                        </div>
                        
                        <!-- Campo de Teléfono -->
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label text-start d-block">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="1234567890" pattern="\d{10}" required>
                        </div>
                        
                        <!-- Campo de Correo Electrónico -->
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label text-start d-block">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="juan.perez@example.com" required>
                        </div>
                        
                        <!-- Método de Pago -->
                        <div class="col-12 mb-3">
                            <label for="metodo_pago" class="form-label text-start d-block">Método de Pago</label>
                            <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                <option value="" disabled selected>Selecciona un método de pago</option>
                                <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                            </select>
                        </div>

                        <!-- Detalles de Pago Dinámicos -->
                        <div id="detalles_pago" class="col-12"></div>
                    </div>
                    
                    <!-- Campo de Código CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    
                    <!-- Botón de Confirmar Pedido -->
                    <button type="submit" class="btn-hover mt-3 w-100 text-center">Confirmar Pedido</button>
                </form>
            </div>
        </div>
        
        <!-- Resumen del Pedido -->
        <div class="col-lg-6">
            <div class="pago-summary p-4">
                <h4 class="mb-3 px-3 text-start">Resumen del Pedido</h4>
                <ul class="list-group list-group-flush text-start">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span><?= number_format($subtotal, 2) ?>€</span>
                    </li>
                    <?php if ($descuento > 0): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Descuento:</span>
                            <span>-<?= number_format($descuento, 2) ?>€</span>
                        </li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Gastos de envío:</span>
                        <span><?= number_format($gastos_envio, 2) ?>€</span>
                    </li>
                    <li class="list-group-item text-success fw-bold d-flex justify-content-between">
                        <span>TOTAL:</span>
                        <span><?= number_format($total, 2) ?>€</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include_once "views/Footer.php"; ?>

<!-- Scripts necesarios -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const metodoPagoSelect = document.getElementById('metodo_pago');
        const detallesPagoDiv = document.getElementById('detalles_pago');

        metodoPagoSelect.addEventListener('change', function() {
            const metodo = this.value;
            detallesPagoDiv.innerHTML = ''; // Limpiar campos anteriores

            if (metodo === 'Tarjeta de Crédito') {
                detallesPagoDiv.innerHTML = `
                    <div class="mb-3">
                        <label for="numero_tarjeta" class="form-label text-start d-block">Número de Tarjeta</label>
                        <input type="text" class="form-control" id="numero_tarjeta" name="numero_tarjeta" placeholder="1234 5678 9012 3456" pattern="\\d{16}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mes_expiracion" class="form-label text-start d-block">Mes de Expiración</label>
                            <select class="form-select" id="mes_expiracion" name="mes_expiracion" required>
                                <option value="" disabled selected>Mes</option>
                                ${Array.from({length: 12}, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ano_expiracion" class="form-label text-start d-block">Año de Expiración</label>
                            <select class="form-select" id="ano_expiracion" name="ano_expiracion" required>
                                <option value="" disabled selected>Año</option>
                                ${Array.from({length: 10}, (_, i) => `<option value="${new Date().getFullYear() + i}">${new Date().getFullYear() + i}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="cvv" class="form-label text-start d-block">CVV</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" pattern="\\d{3}" required>
                    </div>
                `;
            } else if (metodo === 'PayPal') {
                detallesPagoDiv.innerHTML = `
                    <div class="mb-3">
                        <label for="paypal_email" class="form-label text-start d-block">Cuenta de PayPal</label>
                        <input type="email" class="form-control" id="paypal_email" name="paypal_email" placeholder="correo@example.com" required>
                    </div>
                `;
            } else if (metodo === 'Transferencia Bancaria') {
                detallesPagoDiv.innerHTML = `
                    <div class="mb-3">
                        <label for="numero_cuenta" class="form-label text-start d-block">Número de Cuenta</label>
                        <input type="text" class="form-control" id="numero_cuenta" name="numero_cuenta" placeholder="ES00 0000 0000 0000 0000 0000" pattern="[A-Z]{2}\\d{22}" required>
                    </div>
                `;
            }
        });
    });
</script>
