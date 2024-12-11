
<link rel="stylesheet" href="css/Register.css">
<main class="flex-grow-1">
        <div class="container-fluid position-relative">
            <div class="row min-vh-100">
                <!-- Columna Izquierda con Formulario -->
                <div class="col-lg-6 d-flex align-items-center justify-content-center">
                    <div class="main-content mb-4 py-2 px-4">
                        <!-- Logo -->
                        <div class="d-flex flex-column align-items-center mb-3">
                            <img class="logo-login mt-1 mb-2" src="/DAW2/Proyecto1/img/Iconos/Greeny-Logo.svg" alt="Logo de Greeny sin texto">
                            <h1 class="login-title mb-1">¡Bienvenido a Greeny!</h1>
                            <p class="login-subtitle text-muted">Regístrate y comienza a disfrutar de nuestros servicios.</p>
                        </div>

                        <!-- Formulario de Registro -->
                        <form id="registrationForm" action="index.php?controller=usuario&amp;action=register" method="POST" class="w-100" novalidate>
                            
                            <!-- Etapa 1: Email y Contraseña -->
                            <div id="step-1">
                                <div class="form-group mb-4 position-relative">
                                    <input type="email" class="form-control input-custom" placeholder="Dirección de correo electrónico" name="email" id="email" required>
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un correo electrónico válido.
                                    </div>
                                </div>
                                <div class="form-group mb-4 position-relative">
                                    <input type="password" class="form-control input-custom" placeholder="Contraseña" name="password" id="passwordInput" required>
                                    <!-- Botón de toggle de contraseña -->
                                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar contraseña">
                                        <!-- SVG Original de Ocultar Contraseña -->
                                        <svg id="hideIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <!-- Contenido del SVG Original de Ocultar -->
                                            <path d="M3.59961 12C3.59961 12 3.59961 12.07 3.60961 12.1C4.60961 15.8 7.98961 18.53 11.9996 18.53C16.0096 18.53 19.3996 15.8 20.3996 12.1C20.3996 12.07 20.4096 12.03 20.4096 12M19.1296 12C18.1996 15.06 15.3596 17.28 12.0096 17.28C8.65961 17.28 5.81961 15.06 4.87961 12M12 20V18M19.4144 17.4142L18.0002 16M4.58594 17.4142L6.00015 16" 
                                                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                                            <path d="M12 20V18" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M19.4144 17.4142L18.0002 16M4.58594 17.4142L6.00015 16" 
                                                stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <!-- SVG Original de Mostrar Contraseña, inicialmente oculto -->
                                        <svg id="showIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                                            <!-- Contenido del SVG Original de Mostrar -->
                                            <path d="M11.9992 14.5001C13.38 14.5001 14.4992 13.3809 14.4992 12.0001C14.4992 10.6193 13.38 9.5001 11.9992 9.5001C10.6184 9.5001 9.4992 10.6193 9.4992 12.0001C9.4992 13.3809 10.6184 14.5001 11.9992 14.5001Z" 
                                                fill="currentColor"></path>
                                            <path d="M11.995 5.4668C7.97745 5.4668 4.60511 8.19229 3.60172 11.8928C3.58328 11.9608 3.58328 12.0329 3.60172 12.1009C4.60511 15.8014 7.97745 18.5269 11.995 18.5269C16.0126 18.5269 19.3849 15.8014 20.3883 12.1009C20.4068 12.0329 20.4068 11.9608 20.3883 11.8928C19.3849 8.19229 16.0126 5.4668 11.995 5.4668ZM11.995 17.2769C8.6397 17.2769 5.80496 15.0546 4.8724 11.9968C5.80496 8.93905 8.6397 6.7168 11.995 6.7168C15.3504 6.7168 18.1851 8.93905 19.1177 11.9968C18.1851 15.0546 15.3504 17.2769 11.995 17.2769Z" 
                                                fill-rule="evenodd" clip-rule="evenodd" fill="currentColor"></path>
                                        </svg>
                                    </button>
                                    <div class="invalid-feedback">
                                        La contraseña debe tener al menos 6 caracteres.
                                    </div>
                                </div>
                                <div class="form-group mb-4 position-relative">
                                    <input type="password" class="form-control input-custom" placeholder="Confirmar Contraseña" name="confirm_password" id="confirmPasswordInput" required>
                                    <!-- Botón de toggle para confirmar contraseña -->
                                    <button type="button" class="toggle-password" id="toggleConfirmPassword" aria-label="Mostrar contraseña">
                                        <!-- SVG Original de Ocultar Confirmar Contraseña -->
                                        <svg id="hideConfirmIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <!-- Contenido del SVG Original de Ocultar Confirmar -->
                                            <path d="M3.59961 12C3.59961 12 3.59961 12.07 3.60961 12.1C4.60961 15.8 7.98961 18.53 11.9996 18.53C16.0096 18.53 19.3996 15.8 20.3996 12.1C20.3996 12.07 20.4096 12.03 20.4096 12M19.1296 12C18.1996 15.06 15.3596 17.28 12.0096 17.28C8.65961 17.28 5.81961 15.06 4.87961 12M12 20V18M19.4144 17.4142L18.0002 16M4.58594 17.4142L6.00015 16" 
                                                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                                            <path d="M12 20V18" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M19.4144 17.4142L18.0002 16M4.58594 17.4142L6.00015 16" 
                                                stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <!-- SVG Original de Mostrar Confirmar Contraseña, inicialmente oculto -->
                                        <!-- Reemplaza el contenido de este SVG con tu SVG original de "mostrar" -->
                                        <svg id="showConfirmIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                                            <!-- Contenido del SVG Original de Mostrar Confirmar -->
                                            <path d="M11.9992 14.5001C13.38 14.5001 14.4992 13.3809 14.4992 12.0001C14.4992 10.6193 13.38 9.5001 11.9992 9.5001C10.6184 9.5001 9.4992 10.6193 9.4992 12.0001C9.4992 13.3809 10.6184 14.5001 11.9992 14.5001Z" 
                                                fill="currentColor"></path>
                                            <path d="M11.995 5.4668C7.97745 5.4668 4.60511 8.19229 3.60172 11.8928C3.58328 11.9608 3.58328 12.0329 3.60172 12.1009C4.60511 15.8014 7.97745 18.5269 11.995 18.5269C16.0126 18.5269 19.3849 15.8014 20.3883 12.1009C20.4068 12.0329 20.4068 11.9608 20.3883 11.8928C19.3849 8.19229 16.0126 5.4668 11.995 5.4668ZM11.995 17.2769C8.6397 17.2769 5.80496 15.0546 4.8724 11.9968C5.80496 8.93905 8.6397 6.7168 11.995 6.7168C15.3504 6.7168 18.1851 8.93905 19.1177 11.9968C18.1851 15.0546 15.3504 17.2769 11.995 17.2769Z" 
                                                fill-rule="evenodd" clip-rule="evenodd" fill="currentColor"></path>
                                        </svg>
                                    </button>
                                    <div class="invalid-feedback">
                                        Las contraseñas no coinciden.
                                    </div>
                                </div>
                                
                                <!-- Botón de Continuar -->
                                <button type="button" class="btn-login mb-3" id="nextStepButton" disabled>Continuar</button>
                            </div>
                            
                            <!-- Etapa 2: Información Personal -->
                            <div id="step-2" style="display: none;">
                                <div class="form-group mb-4">
                                    <input type="text" class="form-control input-custom" placeholder="Nombre Completo" name="nombre_completo" id="nombreCompleto" required>
                                    <div class="invalid-feedback">
                                        Por favor, ingresa tu nombre completo.
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <input type="text" class="form-control input-custom" placeholder="Dirección" name="direccion" id="direccion" required>
                                    <div class="invalid-feedback">
                                        Por favor, ingresa tu dirección.
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <input type="text" class="form-control input-custom" placeholder="Código Postal" name="codigo_postal" id="codigoPostal" required>
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un código postal válido de 5 dígitos.
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <input type="tel" class="form-control input-custom" placeholder="Teléfono" name="telefono" id="telefono" required>
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un número de teléfono válido.
                                    </div>
                                </div>
                                
                                <!-- Botón de Volver con clase btn-login -->
                                <button type="button" class="btn-login mb-3" id="prevStepButton">Volver</button>
                                
                                <!-- Botón de Envío -->
                                <button type="submit" class="btn-login mb-3" id="submitButton" disabled>Registrarse</button>
                            </div>
                        </form>

                        <!-- Mensaje de Términos de Servicio -->
                        <div class="mt-4 text-center text-gray-500 text-sm">
                            Al crear una cuenta, aceptas nuestros 
                            <a href="https://greeny.com/legal/terms-of-service" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">Términos de Servicio</a> 
                            y reconoces la recepción de nuestra 
                            <a href="https://greeny.com/privacy/policy" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">Política de Privacidad</a>.
                        </div>

                        <!-- Enlace a Login -->
                        <div class="mt-3 text-center">
                            ¿Ya tienes una cuenta? 
                            <a href="index.php?controller=usuario&amp;action=login" class="text-decoration-none">
                                <span class="text-primary">Inicia sesión</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha con Imagen -->
                <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
                    <img src="/DAW2/Proyecto1/img/Iconos/splash.webp" alt="Imagen de registro" class="img-fluid custom-image">
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
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

    <!-- JavaScript para Funcionalidades -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle de Contraseña
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const hideIcon = document.getElementById('hideIcon');
        const showIcon = document.getElementById('showIcon');

        // Toggle de Confirmar Contraseña
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPasswordInput');
        const hideConfirmIcon = document.getElementById('hideConfirmIcon');
        const showConfirmIcon = document.getElementById('showConfirmIcon');

        // Botones de pasos
        const nextStepButton = document.getElementById('nextStepButton');
        const prevStepButton = document.getElementById('prevStepButton');
        const submitButton = document.getElementById('submitButton');

        // Secciones del formulario
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');

        // Inputs de Etapa 1
        const emailInputField = document.getElementById('email');
        const passwordInputField = document.getElementById('passwordInput');
        const confirmPasswordField = document.getElementById('confirmPasswordInput');

        // Inputs de Etapa 2
        const nombreCompletoInput = document.getElementById('nombreCompleto');
        const direccionInput = document.getElementById('direccion');
        const codigoPostalInput = document.getElementById('codigoPostal');
        const telefonoInput = document.getElementById('telefono');

        // Flags para rastrear si un campo ha sido tocado
        let touchedFields = {
            email: false,
            password: false,
            confirm_password: false,
            nombre_completo: false,
            direccion: false,
            codigo_postal: false,
            telefono: false
        };

        // Función para validar Etapa 1 con feedback visual
        function validateStep1() {
            const email = emailInputField.value.trim();
            const password = passwordInputField.value.trim();
            const confirmPassword = confirmPasswordField.value.trim();

            const emailValid = /\S+@\S+\.\S+/.test(email);
            const passwordValid = password.length >= 6;
            const passwordsMatch = password === confirmPassword && password !== '';

            // Feedback visual para email
            if (touchedFields.email) {
                if (email !== "" && emailValid) {
                    emailInputField.classList.add('is-valid');
                    emailInputField.classList.remove('is-invalid');
                } else if (email !== "") {
                    emailInputField.classList.add('is-invalid');
                    emailInputField.classList.remove('is-valid');
                } else {
                    emailInputField.classList.remove('is-valid', 'is-invalid');
                }
            } else {
                emailInputField.classList.remove('is-valid', 'is-invalid');
            }

            // Feedback visual para contraseña
            if (touchedFields.password) {
                if (password !== "" && passwordValid) {
                    passwordInputField.classList.add('is-valid');
                    passwordInputField.classList.remove('is-invalid');
                    togglePassword.classList.remove('invalid');
                    togglePassword.classList.add('valid');
                } else if (password !== "") {
                    passwordInputField.classList.add('is-invalid');
                    passwordInputField.classList.remove('is-valid');
                    togglePassword.classList.remove('valid');
                    togglePassword.classList.add('invalid');
                } else {
                    passwordInputField.classList.remove('is-valid', 'is-invalid');
                    togglePassword.classList.remove('invalid', 'valid');
                }
            } else {
                passwordInputField.classList.remove('is-valid', 'is-invalid');
                togglePassword.classList.remove('invalid', 'valid');
            }

            // Feedback visual para confirmar contraseña
            if (touchedFields.confirm_password) {
                if (confirmPassword !== "" && passwordsMatch) {
                    confirmPasswordField.classList.add('is-valid');
                    confirmPasswordField.classList.remove('is-invalid');
                    toggleConfirmPassword.classList.remove('invalid');
                    toggleConfirmPassword.classList.add('valid');
                } else if (confirmPassword !== "") {
                    confirmPasswordField.classList.add('is-invalid');
                    confirmPasswordField.classList.remove('is-valid');
                    toggleConfirmPassword.classList.remove('valid');
                    toggleConfirmPassword.classList.add('invalid');
                } else {
                    confirmPasswordField.classList.remove('is-valid', 'is-invalid');
                    toggleConfirmPassword.classList.remove('invalid', 'valid');
                }
            } else {
                confirmPasswordField.classList.remove('is-valid', 'is-invalid');
                toggleConfirmPassword.classList.remove('invalid', 'valid');
            }

            // Habilitar el botón de Continuar solo si todas las condiciones son verdaderas
            const canContinue = (emailValid && passwordValid && passwordsMatch) && (email !== "" && password !== "" && confirmPassword !== "");
            nextStepButton.disabled = !canContinue;
        }

        // Función para validar Etapa 2 con feedback visual
        function validateStep2() {
            const nombreCompleto = nombreCompletoInput.value.trim();
            const direccion = direccionInput.value.trim();
            const codigoPostal = codigoPostalInput.value.trim();
            const telefono = telefonoInput.value.trim();

            const nombreValid = nombreCompleto.length > 0;
            const direccionValid = direccion.length > 0;
            const codigoPostalValid = /^\d{5}$/.test(codigoPostal); // Ejemplo: 5 dígitos
            const telefonoValid = /^\+?\d{7,15}$/.test(telefono); // Ejemplo: 7-15 dígitos, opcional +

            // Feedback visual para nombre completo
            if (touchedFields.nombre_completo) {
                if (nombreValid) {
                    nombreCompletoInput.classList.add('is-valid');
                    nombreCompletoInput.classList.remove('is-invalid');
                } else {
                    nombreCompletoInput.classList.add('is-invalid');
                    nombreCompletoInput.classList.remove('is-valid');
                }
            } else {
                nombreCompletoInput.classList.remove('is-valid', 'is-invalid');
            }

            // Feedback visual para dirección
            if (touchedFields.direccion) {
                if (direccionValid) {
                    direccionInput.classList.add('is-valid');
                    direccionInput.classList.remove('is-invalid');
                } else {
                    direccionInput.classList.add('is-invalid');
                    direccionInput.classList.remove('is-valid');
                }
            } else {
                direccionInput.classList.remove('is-valid', 'is-invalid');
            }

            // Feedback visual para código postal
            if (touchedFields.codigo_postal) {
                if (codigoPostalValid) {
                    codigoPostalInput.classList.add('is-valid');
                    codigoPostalInput.classList.remove('is-invalid');
                } else {
                    codigoPostalInput.classList.add('is-invalid');
                    codigoPostalInput.classList.remove('is-valid');
                }
            } else {
                codigoPostalInput.classList.remove('is-valid', 'is-invalid');
            }

            // Feedback visual para teléfono
            if (touchedFields.telefono) {
                if (telefonoValid) {
                    telefonoInput.classList.add('is-valid');
                    telefonoInput.classList.remove('is-invalid');
                } else {
                    telefonoInput.classList.add('is-invalid');
                    telefonoInput.classList.remove('is-valid');
                }
            } else {
                telefonoInput.classList.remove('is-valid', 'is-invalid');
            }

            // Habilitar el botón de Registrarse solo si todas las condiciones son verdaderas
            const canRegister = (nombreValid && direccionValid && codigoPostalValid && telefonoValid) && (nombreCompleto !== "" && direccion !== "" && codigoPostal !== "" && telefono !== "");
            submitButton.disabled = !canRegister;
        }

        // Inicializar el estado de los botones
        validateStep1();
        validateStep2();

        // Función para manejar el evento 'blur' y marcar el campo como tocado
        function handleBlur(event) {
            const field = event.target.name;
            touchedFields[field] = true;
            if (field === 'email' || field === 'password' || field === 'confirm_password') {
                validateStep1();
            } else if (field === 'nombre_completo' || field === 'direccion' || field === 'codigo_postal' || field === 'telefono') {
                validateStep2();
            }
        }

        // Escuchar eventos de 'blur' para marcar campos como tocados
        emailInputField.addEventListener('blur', handleBlur);
        passwordInputField.addEventListener('blur', handleBlur);
        confirmPasswordField.addEventListener('blur', handleBlur);
        nombreCompletoInput.addEventListener('blur', handleBlur);
        direccionInput.addEventListener('blur', handleBlur);
        codigoPostalInput.addEventListener('blur', handleBlur);
        telefonoInput.addEventListener('blur', handleBlur);

        // Escuchar eventos de entrada en Etapa 1
        emailInputField.addEventListener('input', validateStep1);
        passwordInputField.addEventListener('input', validateStep1);
        confirmPasswordField.addEventListener('input', validateStep1);

        // Escuchar eventos de entrada en Etapa 2
        nombreCompletoInput.addEventListener('input', validateStep2);
        direccionInput.addEventListener('input', validateStep2);
        codigoPostalInput.addEventListener('input', validateStep2);
        telefonoInput.addEventListener('input', validateStep2);

        // Manejar el botón de Continuar (Paso 1 -> Paso 2)
        nextStepButton.addEventListener('click', function () {
            step1.style.display = 'none';
            step2.style.display = 'block';
        });

        // Manejar el botón de Volver (Paso 2 -> Paso 1) con clase btn-login
        prevStepButton.addEventListener('click', function () {
            step2.style.display = 'none';
            step1.style.display = 'block';
        });

        // Funcionalidad del botón de toggle de contraseña
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            hideIcon.style.display = type === 'password' ? 'block' : 'none';
            showIcon.style.display = type === 'password' ? 'none' : 'block';

            togglePassword.setAttribute('aria-label', type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });

        // Funcionalidad del botón de toggle de confirmar contraseña
        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);

            hideConfirmIcon.style.display = type === 'password' ? 'block' : 'none';
            showConfirmIcon.style.display = type === 'password' ? 'none' : 'block';

            toggleConfirmPassword.setAttribute('aria-label', type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    });
    </script>
