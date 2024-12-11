<!-- Barra superior -->
<header class="sticky-top">
    <nav class="flex-container" id="barra_superior">
        <div id="flex-item-left">
            <!-- Logo -->
             <a id="logo" href="?producto=Producto&action=index">
                <img src="/DAW2/Proyecto1/img/Iconos/Greeny-Logo.png" alt="Logo Barra Superior" id="logo_topnav">
            </a>
            
            <!-- Redirecciones About us & Explorar -->
            <a href="">Acerca de nosotros</a>
            <a href="?controller=Producto&action=carta">Carta</a>
        </div>
        <!-- Buscador y Botones de Cuenta y Carrito -->
        <div id="flex-item-right">
            <a name="boton_cuenta">Cuenta</a>
            <a href="?controller=carrito&action=index" name="boton_carrito">Carrito</a>
        </div>
        <!-- Icono de menú para móviles -->
        <div class="menu-icon" onclick="toggleMenu()">
            &#9776; <!-- Ícono de hamburguesa -->
        </div>
    </nav>
</header>

<!-- Menú desplegable en móviles -->
<div class="dropdown-menu" id="dropdown-menu">
    <a href="">Acerca de nosotros</a>
    <a href="">Explorar</a>
    <a name="boton_cuenta">Cuenta</a>
    <a name="boton_carrito">Carrito</a>
</div>
<script>
function toggleMenu() {
    const dropdownMenu = document.getElementById('dropdown-menu');
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
}

// Opcional: Cerrar el menú cuando se hace clic fuera de él
window.onclick = function(event) {
    const dropdownMenu = document.getElementById('dropdown-menu');
    const menuIcon = document.querySelector('.menu-icon');
    if (event.target !== dropdownMenu && event.target !== menuIcon && dropdownMenu.style.display === 'block') {
        dropdownMenu.style.display = 'none';
    }
};
</script>
