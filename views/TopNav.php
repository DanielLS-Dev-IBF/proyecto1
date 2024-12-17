<link rel="stylesheet" href="css/Topbar.css">
<!-- Barra superior -->
<header class="sticky-top">
    <nav class="flex-container" id="barra_superior">
        <div id="flex-item-left">
            <!-- Logo -->
            <a id="logo" href="?controller=Producto&action=index">
                <img src="/DAW2/Proyecto1/img/Iconos/Greeny-Logo.png" alt="Logo Barra Superior" id="logo_topnav">
            </a>

            <!-- Redirecciones About us & Explorar -->
            <a href="?controller=Producto&action=index">Acerca de nosotros</a>
            <a href="?controller=Producto&action=carta">Carta</a>
        </div>

        <!-- Botón Cuenta con menú desplegable -->
        <div id="flex-item-right">
            <div class="dropdown">
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <!-- Botón Cuenta -->
                    <div id="boton_cuenta" id="boton_cuenta">
                        Mi Cuenta
                        <img src="/DAW2/Proyecto1/img/Iconos/user-black.svg" alt="Icono Usuario">
                    </div>
                    <ul class="dropdown-menu" id="menu_cuenta">
                        <li><a href="?controller=usuario&action=show">Ver cuenta</a></li>
                        <li><a href="?controller=usuario&action=logout">Cerrar sesión</a></li>
                    </ul>
                <?php else: ?>
                    <a href="?controller=usuario&action=login" id="boton_cuenta">
                        Iniciar Sesión
                        <img src="/DAW2/Proyecto1/img/Iconos/user-black.svg" alt="Icono Usuario" class="icono-cuenta">
                    </a>
                <?php endif; ?>
            </div>

            <!-- Botón Carrito -->
            <a href="?controller=carrito&action=index" id="boton_carrito" name="boton_carrito">
                Carrito
                <img src="/DAW2/Proyecto1/img/Iconos/shopping-cart-white.svg" alt="Icono Carrito">
            </a>
        </div>

        <!-- Icono de menú hamburguesa para móviles -->
        <div class="menu-icon" onclick="toggleMenu()">
            &#9776;
        </div>
    </nav>
</header>


<!-- Menú desplegable en móviles -->
<div class="dropdown-menu" id="dropdown-menu">
    <a href="?controller=Producto&action=index">Acerca de nosotros</a>
    <a href="?controller=Producto&action=carta">Carta</a>
    <?php if (isset($_SESSION['id_usuario'])): ?>
        <a href="?controller=usuario&action=show">Ver cuenta</a>
        <a href="?controller=usuario&action=logout">Cerrar sesión</a>
    <?php else: ?>
        <a href="?controller=usuario&action=login">Iniciar Sesión</a>
    <?php endif; ?>
    <a href="?controller=carrito&action=index">Carrito</a>
</div>

<!-- Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const botonCuenta = document.getElementById("boton_cuenta");
    const menuCuenta = document.getElementById("menu_cuenta");

    // Mostrar/ocultar el menú desplegable
    if (menuCuenta) {
        botonCuenta.addEventListener("click", function (e) {
            e.stopPropagation();
            menuCuenta.classList.toggle("active");
        });

        document.addEventListener("click", function (e) {
            if (!botonCuenta.contains(e.target) && !menuCuenta.contains(e.target)) {
                menuCuenta.classList.remove("active");
            }
        });
    }
});

// Menú hamburguesa
function toggleMenu() {
    const dropdownMenu = document.getElementById("dropdown-menu");
    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
}
</script>
