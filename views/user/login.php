<!-- Luego, el CSS personalizado -->
<link rel="stylesheet" href="css/Login.css">
<main class="position-relative d-flex align-items-center justify-content-center">
    <!-- SVG Izquierda -->
    <svg width="325" height="490" viewBox="0 0 325 490" fill="none" xmlns="http://www.w3.org/2000/svg" class="position-absolute svg-left d-none d-lg-block pointer-events-none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M170.35 62.1166L93.0755 41.411L66.3055 141.318L14.5898 51.7439L-54.6923 91.7438L-2.97645 181.318L-102.884 154.548L-123.589 231.822L-23.6822 258.592L-113.256 310.307L-73.2564 379.59L16.318 327.874L-10.4521 427.781L66.8219 448.487L93.5921 348.579L145.308 438.155L214.59 398.155L162.873 308.58L262.781 335.35L283.487 258.076L183.579 231.306L273.154 179.59L233.155 110.308L143.58 162.024L170.35 62.1166Z" fill="#96D347"></path>
    </svg>
    
    <!-- Contenido Principal -->
    <div class="d-flex flex-column align-items-center gap-2 text-center main-content" style="max-width: 450px; width: 100%;">
        <img class="logo-login" src="/DAW2/Proyecto1/img/Iconos/Greeny-Logo.svg" alt="Logo de Greeny sin texto" style="max-width: 75px;">

        <!-- Título con clase custom -->
        <h1 class="login-title mt-1 mb-0">Iniciar sesión</h1>
        <p class="login-subtitle text-muted mt-1 mb-4">para continuar con tu cuenta de Greeny.</p>

        <!-- Formulario de inicio de sesión -->
        <form action="index.php?controller=usuario&amp;action=authenticate" method="POST" class="w-100">
            <div class="mb-4">
                <input type="email" class="form-control input-custom" placeholder="Dirección de correo electrónico" name="email" required>
            </div>
            <!-- Contenedor con posición relativa para el input de contraseña -->
            <div class="mb-4 position-relative">
                <input type="password" class="form-control input-custom" placeholder="Contraseña" name="password" id="passwordInput" required>
                <!-- Botón de toggle dentro del input -->
                <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar contraseña">
                    <!-- SVG inicial (ícono de ocultar) -->
                    <svg id="hideIcon" width="28" height="28" viewBox="0 0 24 24" fill="none" 
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.59961 12C3.59961 12 3.59961 12.07 3.60961 12.1C4.60961 15.8 7.98961 18.53 11.9996 18.53C16.0096 18.53 19.3996 15.8 20.3996 12.1C20.3996 12.07 20.4096 12.03 20.4096 12M19.1296 12C18.1996 15.06 15.3596 17.28 12.0096 17.28C8.65961 17.28 5.81961 15.06 4.87961 12M12 20V18M19.4144 17.4142L18.0002 16M4.58594 17.4142L6.00015 16" 
                              fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                        <path d="M12 20V18" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M19.4144 17.4142L18.0002 16M4.58594 17.4142L6.00015 16" 
                              stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <!-- SVG alternativo (ícono de mostrar), inicialmente oculto -->
                    <svg id="showIcon" width="28" height="28" viewBox="0 0 24 24" fill="none" 
                         xmlns="http://www.w3.org/2000/svg" style="display: none;">
                        <path d="M11.9992 14.5001C13.38 14.5001 14.4992 13.3809 14.4992 12.0001C14.4992 10.6193 13.38 9.5001 11.9992 9.5001C10.6184 9.5001 9.4992 10.6193 9.4992 12.0001C9.4992 13.3809 10.6184 14.5001 11.9992 14.5001Z" 
                              fill="currentColor"></path>
                        <path d="M11.995 5.4668C7.97745 5.4668 4.60511 8.19229 3.60172 11.8928C3.58328 11.9608 3.58328 12.0329 3.60172 12.1009C4.60511 15.8014 7.97745 18.5269 11.995 18.5269C16.0126 18.5269 19.3849 15.8014 20.3883 12.1009C20.4068 12.0329 20.4068 11.9608 20.3883 11.8928C19.3849 8.19229 16.0126 5.4668 11.995 5.4668ZM11.995 17.2769C8.6397 17.2769 5.80496 15.0546 4.8724 11.9968C5.80496 8.93905 8.6397 6.7168 11.995 6.7168C15.3504 6.7168 18.1851 8.93905 19.1177 11.9968C18.1851 15.0546 15.3504 17.2769 11.995 17.2769Z" 
                              fill-rule="evenodd" clip-rule="evenodd" fill="currentColor"></path>
                    </svg>
                </button>
            </div>
            <!-- Botón de Envío Deshabilitado por Defecto con Nueva Clase -->
            <button type="submit" class="btn-login w-100 mt-0 mb-4" disabled>Iniciar Sesión</button>
        </form>

        <a href="index.php?controller=usuario&amp;action=register" class="text-decoration-none" style="font-size: 1rem;">
            ¿No tienes cuenta? <span class="text-primary">Regístrate</span>
        </a>
    </div>
    <!-- SVG Derecha -->
    <svg width="300" height="490" viewBox="0 0 300 490" fill="none" xmlns="http://www.w3.org/2000/svg" class="position-absolute svg-right d-none d-lg-block pointer-events-none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M144.95 71.7439C120.397 85.9193 106.156 111.096 104.857 137.452C79.7347 145.528 59.1022 165.801 51.7645 193.185C44.4269 220.569 52.159 248.442 69.8774 267.998C57.8239 291.473 57.5692 320.396 71.7443 344.95C85.9201 369.501 111.096 383.743 137.453 385.043C145.529 410.165 165.801 430.797 193.186 438.134C220.57 445.472 248.443 437.74 267.999 420.022C291.473 432.075 320.397 432.329 344.95 418.155C369.502 403.978 383.743 378.803 385.043 352.447C410.165 344.37 430.797 324.097 438.135 296.713C445.473 269.328 437.741 241.456 420.023 221.9C432.075 198.425 432.33 169.502 418.155 144.949C403.979 120.396 378.803 106.155 352.447 104.857C344.37 79.734 324.097 59.1013 296.713 51.7638C269.329 44.4261 241.456 52.1582 221.901 69.8767C198.426 57.8233 169.502 57.5685 144.95 71.7439Z" fill="#F5BFF0"></path>
    </svg>
</main>

<footer>
  <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
    <span>© 2024 Greeny Corporation. All rights reserved.</span>
    <div class="d-flex gap-3">
      <a target="_blank" href="https://greeny.com/security">Security</a>
      <a target="_blank" href="https://greeny.com/legal">Legal</a>
      <a target="_blank" href="https://greeny.com/privacy/policy">Privacy</a>
    </div>
  </div>
</footer>


<!-- Añadir script personalizado para la funcionalidad de toggle de contraseña y activar/desactivar el botón -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const hideIcon = document.getElementById('hideIcon');
        const showIcon = document.getElementById('showIcon');

        // Botón de envío
        const submitButton = document.querySelector('button.btn-login');
        // Input de email
        const emailInput = document.querySelector('input[name="email"]');

        // Función para validar los campos
        function validateInputs() {
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (email !== '' && password !== '') {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }

        // Inicializar el estado del botón
        validateInputs();

        // Escuchar eventos de entrada en los inputs
        emailInput.addEventListener('input', validateInputs);
        passwordInput.addEventListener('input', validateInputs);

        // Funcionalidad del botón de toggle de contraseña
        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle the icons
            hideIcon.style.display = type === 'password' ? 'block' : 'none';
            showIcon.style.display = type === 'password' ? 'none' : 'block';

            // Actualizar aria-label
            togglePassword.setAttribute('aria-label', type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    });
</script>
