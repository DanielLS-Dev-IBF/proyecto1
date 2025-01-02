<!-- views/admin/index.php -->
<?php
    include_once "views/TopNav.php";
?>
<link rel="stylesheet" href="css/Admin.css">
<div class="container my-5">
    <h2 class="text-center">Panel de Administración</h2>
    <hr>

    <!-- Selector de Moneda -->
    <div class="mb-4 d-flex justify-content-end">
        <label for="select-moneda-admin" class="me-2 align-self-center">Moneda:</label>
        <select id="select-moneda-admin" class="form-select" style="width: 150px;">
            <option value="EUR" selected>€ EUR</option>
            <option value="USD">$ USD</option>
            <option value="CAD">C$ CAD</option>
            <!-- Agrega más monedas según necesidad -->
        </select>
    </div>

    <!-- Botones principales centrados -->
    <div class="mb-4 d-flex justify-content-center gap-2">
        <button class="btn-hover btn btn-secondary" id="btn-usuarios">Usuarios</button>
        <button class="btn-hover btn btn-secondary" id="btn-pedidos">Pedidos</button>
        <button class="btn-hover btn btn-secondary" id="btn-productos">Productos</button>
        <button class="btn-hover btn btn-secondary" id="btn-logs">Logs</button>
    </div>

    <!-- Contenedor dinámico -->
    <div id="admin-content"></div>
</div>
<script src="assets/js/main.js"></script>
<?php
    include_once "views/Footer.php";
?>
