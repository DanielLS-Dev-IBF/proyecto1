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
            <!-- Las opciones serán cargadas dinámicamente -->
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
<script>
    document.addEventListener("DOMContentLoaded", async function () {
        // Obtener la lista de monedas y sus símbolos
        const currencies = await CurrencyConverter.fetchCurrenciesList();
        const select = document.getElementById("select-moneda-admin");

        if (currencies) {
            currencies.forEach(function (currency) {
                const option = document.createElement("option");
                option.value = currency.code;
                option.textContent = `${currency.symbol} ${currency.code}`;
                select.appendChild(option);
            });
        }

        // Obtener las tasas de cambio
        await CurrencyConverter.fetchCurrencyRates();

        // Evento al cambiar la moneda
        select.addEventListener("change", function () {
            const selectedCurrency = this.value;
            CurrencyConverter.actualizarPrecios(".precio-elemento", selectedCurrency);
        });

        // Inicializar precios con EUR por defecto
        CurrencyConverter.actualizarPrecios(".precio-elemento", "EUR");
    });
</script>