<!-- TopNav.php -->
<link rel="stylesheet" href="css/Topbar.css">
<!-- Barra Superior -->
<header>
    <nav class="flex-container" id="barra_superior">
        <div id="flex-item-left" class="flex-item">
            <!-- Logo -->
            <a id="logo" href="?controller=Producto&action=index">
                <img src="/DAW2/Proyecto1/img/Iconos/Greeny-Logo.png" alt="Logo Barra Superior" id="logo_topnav">
            </a>
            <!-- Enlaces de Navegación (Desktop) -->
            <div class="nav-links">
                <a href="?controller=Producto&action=index">Acerca de nosotros</a>
                <a href="?controller=Producto&action=carta">Carta</a>
                
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <a href="?controller=admin&action=index">Admin</a>
                <?php endif; ?>
            </div>
        </div>

        <div id="flex-item-right" class="flex-item">
            <!-- Botón Cuenta con menú desplegable (Desktop) -->
            <div class="dropdown">
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <!-- Botón Cuenta -->
                    <div id="boton_cuenta" class="dropdown-toggle" tabindex="0">
                        Mi Cuenta
                        <img src="/DAW2/Proyecto1/img/Iconos/user-black.svg" alt="Icono Usuario">
                        <!-- SVG de Flecha hacia Abajo -->
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <ul class="dropdown-menu" id="menu_cuenta">
                        <li><a href="?controller=usuario&action=show">Ver cuenta</a></li>
                        <li><a href="?controller=usuario&action=logout">Cerrar sesión</a></li>
                    </ul>
                <?php else: ?>
                    <a href="?controller=usuario&action=login" id="boton_cuenta" class="dropdown-toggle">
                        Iniciar Sesión
                        <img src="/DAW2/Proyecto1/img/Iconos/user-black.svg" alt="Icono Usuario" class="icono-cuenta">
                        <!-- SVG de Flecha hacia Abajo -->
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
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
        <div class="menu-icon" id="menu-icon" aria-label="Abrir menú" aria-expanded="false" aria-controls="mobile-menu">
            <!-- Icono manejado por CSS ::before -->
        </div>
    </nav>
</header>

<!-- Menú desplegable en móviles -->
<div class="mobile-dropdown-menu" id="mobile-menu" aria-hidden="true">
    <nav>
        <ul>
            <li><a href="?controller=Producto&action=index">Acerca de nosotros</a></li>
            <li><a href="?controller=Producto&action=carta">Carta</a></li>
            <li><a href="?controller=carrito&action=index">Carrito</a></li>
            <!-- Separador -->
            <li class="separator"></li>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <li><a href="?controller=admin&action=index">Admin</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['id_usuario'])): ?>
                <li>
                    <button class="submenu-toggle" aria-expanded="false" aria-controls="submenu-cuenta">
                        Mi Cuenta
                        <!-- SVG de Flecha hacia Abajo -->
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <ul class="submenu" id="submenu-cuenta">
                        <li><a href="?controller=usuario&action=show">Ver cuenta</a></li>
                        <li><a href="?controller=usuario&action=logout">Cerrar sesión</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="?controller=usuario&action=login">Iniciar Sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Scripts -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const botonCuenta = document.getElementById("boton_cuenta");
        const menuCuenta = document.getElementById("menu_cuenta");

        // Mostrar/ocultar el menú desplegable de la cuenta (Desktop)
        if (menuCuenta && botonCuenta) {
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

        // Elementos del menú móvil
        const mobileMenu = document.getElementById("mobile-menu");
        const menuIcon = document.getElementById("menu-icon");
        const header = document.querySelector("header"); // Selecciona el header

        if (menuIcon && mobileMenu) {
            menuIcon.addEventListener("click", function (e) {
                e.stopPropagation();
                toggleMenu();
            });

            // Cerrar el menú móvil al hacer clic en un enlace
            document.querySelectorAll('.mobile-dropdown-menu a').forEach(function (link) {
                link.addEventListener('click', function () {
                    closeMenu();
                });
            });

            // Cerrar el menú móvil al hacer clic fuera del menú y del icono
            document.addEventListener("click", function (e) {
                if (!mobileMenu.contains(e.target) && !menuIcon.contains(e.target)) {
                    closeMenu();
                }
            });
        }

        // Manejar los submenús
        const submenuToggles = document.querySelectorAll('.submenu-toggle');

        submenuToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                const isActive = submenu.classList.toggle('active');
                this.classList.toggle('active');

                // Actualizar atributo aria-expanded
                this.setAttribute('aria-expanded', isActive);
            });
        });

        // Función para abrir/cerrar el menú
        function toggleMenu() {
            mobileMenu.classList.toggle("active");
            menuIcon.classList.toggle("active");
            header.classList.toggle("fixed"); // Agrega o elimina la clase 'fixed'

            if (mobileMenu.classList.contains("active")) {
                menuIcon.setAttribute('aria-expanded', 'true');
                mobileMenu.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden'; // Previene el scroll
                console.log("Menú móvil abierto");
            } else {
                menuIcon.setAttribute('aria-expanded', 'false');
                mobileMenu.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = ''; // Restaura el scroll
                console.log("Menú móvil cerrado");
            }
        }

        // Función para cerrar el menú
        function closeMenu() {
            mobileMenu.classList.remove("active");
            menuIcon.classList.remove("active");
            header.classList.remove("fixed"); // Elimina la clase 'fixed'
            menuIcon.setAttribute('aria-expanded', 'false');
            mobileMenu.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = ''; // Restaura el scroll
            console.log("Menú móvil cerrado mediante enlace o clic fuera");
        }
    });
</script>
