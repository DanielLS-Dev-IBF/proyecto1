// ===============================================================================
// SISTEMA DE ADMINISTRACIÓN - GESTIÓN DE USUARIOS, PEDIDOS, PRODUCTOS Y LOGS
// ===============================================================================

$(document).ready(function () {
  // ===== VARIABLES GLOBALES =====
  let currentDataTable = null; // **DOM:** Almacena la instancia actual de DataTable para limpieza
  let currentModalEvents = []; // **EVENTS:** Array para tracking de eventos de modales dinámicos

  // **DOM:** Configuración base para todas las DataTables con layout responsivo
  const dtConfig = {
    responsive: { details: false },
    dom:
      "<'row'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'f>>" +
      "rt" +
      "<'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-end'p>>",
  };

  // ===== CONSTANTES DEL NEGOCIO =====
  const MINIMO_ENVIO_GRATUITO = 50.0; // Mínimo para envío gratuito
  const COSTO_ENVIO = 5.0; // Costo fijo de envío
  // ===== UTILIDADES =====
  // **CURRENCY:** Obtiene el símbolo de moneda según el código ISO
  function obtenerSimboloMoneda(moneda) {
    const simbolos = { USD: "$", CAD: "C$", EUR: "€" };
    return simbolos[moneda] || "€";
  }

  // **DOM:** Actualiza los encabezados de tablas con el símbolo de moneda correspondiente
  function actualizarEncabezados(simbolo) {
    $("#tabla-productos thead tr th").eq(3).text(`Precio Base (${simbolo})`);
    $("#tabla-pedidos thead tr th").eq(3).text(`Total (${simbolo})`);
  }

  // **DOM + CURRENCY:** Actualiza todos los precios mostrados en las tablas según la moneda seleccionada
  function actualizarPreciosEnTablas(selectedCurrency) {
    CurrencyConverter.actualizarPrecios(".precio-base", selectedCurrency);
    CurrencyConverter.actualizarPrecios(".precio-pedido", selectedCurrency);
  }
  // ===== GESTIÓN DE MODALES =====
  // **DOM + EVENTS:** Crea modales dinámicos con gestión automática de eventos
  function createModal(
    modalId,
    title,
    bodyContent,
    footerButtons,
    isForm = false
  ) {
    cleanupModal(); // **DOM:** Limpia cualquier modal previo

    const formStart = isForm ? `<form id="form-${modalId}">` : "";
    const formEnd = isForm ? "</form>" : "";

    // **DOM:** Construcción dinámica del HTML del modal
    const modalHtml = `
      <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            ${formStart}
              <div class="modal-header">
                <h5 class="modal-title" id="${modalId}Label">${title}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">${bodyContent}</div>
              <div class="modal-footer">${footerButtons}</div>
            ${formEnd}
          </div>
        </div>
      </div>`;

    // **DOM:** Inserción del modal en el body
    $("body").append(modalHtml);

    // **DOM:** Inicialización del modal de Bootstrap
    const modal = new bootstrap.Modal(document.getElementById(modalId), {
      backdrop: false,
    });

    // **EVENTS:** Auto-limpieza cuando se cierra el modal
    $(`#${modalId}`).on("hidden.bs.modal", function () {
      cleanupModal();
    });

    return modal;
  }

  // **DOM + EVENTS:** Limpia modales y eventos para prevenir memory leaks
  function cleanupModal() {
    $(".modal").remove(); // **DOM:** Elimina elementos modal del DOM
    $(".modal-backdrop").remove(); // **DOM:** Elimina backdrop de Bootstrap

    // **EVENTS:** Limpia todos los event listeners registrados
    currentModalEvents.forEach((eventConfig) => {
      $(document).off(eventConfig.event, eventConfig.selector);
    });
    currentModalEvents = []; // Reset del array de tracking
  }

  // **EVENTS:** Registra y trackea eventos de modales dinámicos
  function addModalEvent(event, selector, handler) {
    $(document).off(event, selector).on(event, selector, handler);
    currentModalEvents.push({ event, selector }); // Tracking para limpieza posterior
  }
  // ===== FUNCIONES DE CÁLCULO =====
  // **DOM + LOGIC:** Función genérica para recalcular totales de pedidos
  function recalcularTotales(
    tableSelector,
    subtotalSelector,
    gastosSelector,
    totalSelector
  ) {
    let subtotal = 0;

    // **DOM:** Itera sobre cada fila de la tabla para calcular totales
    $(`${tableSelector} tbody tr`).each(function () {
      const precioUnitario =
        parseFloat($(this).find(".precio-unitario").val()) || 0;
      const cantidad = parseInt($(this).find(".cantidad").val()) || 0;
      const totalProducto = precioUnitario * cantidad;
      // **DOM:** Actualiza el campo total de cada producto
      $(this).find(".total-producto").val(totalProducto.toFixed(2));
      subtotal += totalProducto;
    });

    // **BUSINESS LOGIC:** Calcula gastos de envío según reglas de negocio
    const gastosEnvio = subtotal < MINIMO_ENVIO_GRATUITO ? COSTO_ENVIO : 0.0;
    const total = subtotal + gastosEnvio;

    // **DOM:** Actualiza los elementos de resumen con los nuevos valores
    $(subtotalSelector).text(subtotal.toFixed(2) + " €");
    $(gastosSelector)
      .text(gastosEnvio.toFixed(2) + " €")
      .toggleClass("text-success text-danger", gastosEnvio === 0);
    $(totalSelector).text(total.toFixed(2) + " €");

    // **DOM:** Efecto visual para destacar cambios en totales
    $(gastosSelector + ", " + totalSelector)
      .fadeOut(100)
      .fadeIn(100);
  }

  // **HELPER:** Función específica para recalcular totales en modal de creación
  function recalcularTotalesModal() {
    recalcularTotales(
      "#tabla-productos-pedido",
      "#subtotal_pedido_modal",
      "#gastos_envio_pedido_modal",
      "#total_pedido_modal"
    );
  }
  function recalcularTotalesModalEd() {
    recalcularTotales(
      "#tabla-productos-pedido-ed",
      "#subtotal_pedido_ed_modal",
      "#gastos_envio_pedido_ed_modal",
      "#total_pedido_ed_modal"
    );
  }

  // ===== FUNCIONES DE INICIALIZACIÓN DE MONEDA =====
  function initializeCurrencyForPedidos() {
    CurrencyConverter.fetchCurrencyRates().then((rates) => {
      if (rates) {
        const savedCurrency =
          sessionStorage.getItem("selectedCurrency") || "EUR";
        $("#select-moneda-admin").val(savedCurrency);
        CurrencyConverter.actualizarPrecios(".precio-pedido", savedCurrency);

        const simboloCabecera = obtenerSimboloMoneda(savedCurrency);
        $("#tabla-pedidos thead tr th")
          .eq(3)
          .text(`Total (${simboloCabecera})`);
      }
    });
  }

  // ===== LIMPIEZA ANTES DE CARGAR =====
  function cleanupBeforeLoad() {
    if (currentDataTable) {
      currentDataTable.destroy();
      currentDataTable = null;
    }
    $("#admin-content").empty();

    // Limpiar todos los eventos específicos de secciones de una vez
    const eventSelectors = [
      "#btn-crear-usuario",
      "#btn-crear-pedido",
      "#btn-crear-producto",
      ".btn-editar-usuario",
      ".btn-editar-pedido",
      ".btn-editar-producto",
      ".btn-borrar-usuario",
      ".btn-borrar-pedido",
      ".btn-borrar-producto",
      "#btn-agregar-producto-pedido",
      "#btn-agregar-producto-pedido-ed",
      ".producto-select",
      ".producto-select-ed",
      ".cantidad",
      ".btn-eliminar-producto",
      ".btn-eliminar-producto-ed",
    ];

    eventSelectors.forEach((selector) => {
      $(document).off("click change input submit", selector);
    });
  }

  // ===== NAVEGACIÓN UNIFICADA =====
  function initializeNavigation() {
    const navButtons = {
      "#btn-usuarios": () => loadSection("usuarios", loadUsuarios),
      "#btn-pedidos": () => loadSection("pedidos", loadPedidos),
      "#btn-productos": () => loadSection("productos", loadProductos),
      "#btn-logs": () => loadSection("logs", loadLogs),
    };

    // Limpiar eventos de navegación previos
    Object.keys(navButtons).forEach((selector) => {
      $(document).off("click", selector);
    }); // Configurar eventos de navegación
    Object.entries(navButtons).forEach(([selector, handler]) => {
      $(document).on("click", selector, handler);
    });
  }

  function loadSection(tabName, loadFunction) {
    sessionStorage.setItem("selectedTab", `btn-${tabName}`);
    loadFunction();
  }

  // ===== EVENTOS GLOBALES ÚNICOS =====
  function initializeGlobalEvents() {
    // Limpiar eventos globales previos
    $(document).off("change", "#select-moneda-admin");

    // Evento único para cambio de moneda
    $(document).on("change", "#select-moneda-admin", function () {
      const selectedCurrency = $(this).val();
      sessionStorage.setItem("selectedCurrency", selectedCurrency);
      actualizarPreciosEnTablas(selectedCurrency);
      actualizarEncabezados(obtenerSimboloMoneda(selectedCurrency));
    });
  }
  // ===== FUNCIONES DE CARGA DE SECCIONES =====
  function loadUsuarios() {
    cleanupBeforeLoad();

    // **DOM:** Construcción del HTML para la sección de usuarios
    const cardHtml = `
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Gestión de Usuarios</h4>
          <button class="btn btn-success btn-sm" id="btn-crear-usuario">
            <i class="bi bi-plus-circle"></i> Crear Usuario
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered nowrap" style="width:100%" id="tabla-usuarios">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre Completo</th>
                  <th>Email</th>
                  <th>Rol</th>
                  <th>Teléfono</th>
                  <th>Acciones</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>`;

    // **DOM:** Inserción del HTML en el contenedor principal
    $("#admin-content").html(cardHtml);

    currentDataTable = $("#tabla-usuarios").DataTable({
      ...dtConfig,
      ajax: {
        url: "index.php?controller=admin&action=getUsuariosJSON",
        type: "GET",
        dataSrc: "",
      },
      columns: [
        { data: "id_usuario" },
        { data: "nombre_completo" },
        { data: "email" },
        { data: "rol" },
        { data: "telefono" },
        {
          data: null,
          render: function (data, type, row) {
            return `
              <button class="btn btn-warning btn-sm btn-editar-usuario" data-id="${row.id_usuario}">Editar</button>
              <button class="btn btn-danger btn-sm btn-borrar-usuario" data-id="${row.id_usuario}" data-nombre="${row.nombre_completo}">Borrar</button>
            `;
          },
        },
      ],
    });

    setupUsuarioEvents();
  }

  function setupUsuarioEvents() {
    // Crear usuario
    addModalEvent("click", "#btn-crear-usuario", function () {
      const modal = createModal(
        "modalCrearUsuario",
        "Crear Usuario",
        `
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre Completo</label>
          <input type="text" class="form-control" id="nombre" name="nombre_completo" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="rol" class="form-label">Rol</label>
          <select class="form-select" id="rol" name="rol" required>
            <option value="admin">Admin</option>
            <option value="usuario">Usuario</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="telefono" class="form-label">Teléfono</label>
          <input type="text" class="form-control" id="telefono" name="telefono">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required minlength="6">
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
        </div>
        `,
        `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary" id="save-user">Guardar</button>
        `,
        true
      );
      modal.show();

      addModalEvent("submit", "#form-modalCrearUsuario", function (e) {
        e.preventDefault();

        const password = $("#password").val();
        const confirmPassword = $("#confirm_password").val();

        if (password !== confirmPassword) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Las contraseñas no coinciden.",
          });
          return;
        }

        const $submitButton = $("#save-user");
        $submitButton.prop("disabled", true).text("Guardando...");

        $.ajax({
          url: "index.php?controller=admin&action=createUsuario",
          type: "POST",
          data: $(this).serialize(),
          dataType: "json",
          success: function (response) {
            if (response.status === "ok") {
              Swal.fire({
                icon: "success",
                title: "¡Éxito!",
                text: response.message,
                timer: 1500,
                showConfirmButton: false,
              }).then(() => {
                modal.hide();
                currentDataTable.ajax.reload(null, false);
              });
            } else {
              if (response.errors) {
                const errorMessages = Object.values(response.errors).join(
                  "<br>"
                );
                Swal.fire({
                  icon: "error",
                  title: "Errores de Validación",
                  html: errorMessages,
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                });
              }
              $submitButton.prop("disabled", false).text("Guardar");
            }
          },
          error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Ocurrió un error inesperado.",
            });
            $submitButton.prop("disabled", false).text("Guardar");
          },
        });
      });
    });

    // Editar usuario
    addModalEvent("click", ".btn-editar-usuario", function () {
      const userId = $(this).data("id");
      const rowData = currentDataTable.row($(this).parents("tr")).data();

      const modal = createModal(
        "modalEditarUsuario",
        "Editar Usuario",
        `
        <input type="hidden" id="id_usuario" name="id_usuario" value="${
          rowData.id_usuario
        }">
        <div class="mb-3">
          <label for="nombre_editar" class="form-label">Nombre Completo</label>
          <input type="text" class="form-control" id="nombre_editar" name="nombre_completo" value="${
            rowData.nombre_completo
          }" required>
        </div>
        <div class="mb-3">
          <label for="email_editar" class="form-label">Email</label>
          <input type="email" class="form-control" id="email_editar" name="email" value="${
            rowData.email
          }" required>
        </div>
        <div class="mb-3">
          <label for="rol_editar" class="form-label">Rol</label>
          <select class="form-select" id="rol_editar" name="rol" required>
            <option value="admin" ${
              rowData.rol === "admin" ? "selected" : ""
            }>Admin</option>
            <option value="usuario" ${
              rowData.rol === "usuario" ? "selected" : ""
            }>Usuario</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="telefono_editar" class="form-label">Teléfono</label>
          <input type="text" class="form-control" id="telefono_editar" name="telefono" value="${
            rowData.telefono
          }">
        </div>
        <!-- Opcional: Añadir campos para cambiar la contraseña -->
        <div class="mb-3">
          <label for="password_editar" class="form-label">Nueva Contraseña</label>
          <input type="password" class="form-control" id="password_editar" name="password" minlength="6">
        </div>
        <div class="mb-3">
          <label for="confirm_password_editar" class="form-label">Confirmar Nueva Contraseña</label>
          <input type="password" class="form-control" id="confirm_password_editar" name="confirm_password" minlength="6">
        </div>
        `,
        `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        `,
        true
      );
      modal.show();

      // Manejar el envío del formulario de edición
      $("#form-modalEditarUsuario").on("submit", function (e) {
        e.preventDefault();

        // Validar que las contraseñas coincidan si se han ingresado
        const password = $("#password_editar").val();
        const confirmPassword = $("#confirm_password_editar").val();
        if (password !== confirmPassword) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Las contraseñas no coinciden.",
          });
          return;
        }

        const $submitButton = $(this).find('button[type="submit"]');
        $submitButton.prop("disabled", true).text("Actualizando...");

        $.ajax({
          url: "index.php?controller=admin&action=updateUsuario",
          type: "POST",
          data: $(this).serialize(),
          dataType: "json",
          success: function (response) {
            if (response.status === "ok") {
              Swal.fire({
                icon: "success",
                title: "¡Éxito!",
                text: response.message,
                timer: 1500,
                showConfirmButton: false,
              }).then(() => {
                modal.hide();
                currentDataTable.ajax.reload(null, false);
              });
            } else {
              if (response.errors) {
                const errorMessages = Object.values(response.errors).join(
                  "<br>"
                );
                Swal.fire({
                  icon: "error",
                  title: "Errores de Validación",
                  html: errorMessages,
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                });
              }
              $submitButton.prop("disabled", false).text("Actualizar");
            }
          },
          error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Ocurrió un error al procesar la solicitud. Inténtalo de nuevo.",
            });
            $submitButton.prop("disabled", false).text("Actualizar");
          },
        });
      });
    });

    // Borrar usuario (delegación)
    $(document)
      .off("click", ".btn-borrar-usuario")
      .on("click", ".btn-borrar-usuario", function () {
        const userId = $(this).data("id");
        const userName = $(this).data("nombre");

        Swal.fire({
          title: "¿Estás seguro?",
          text: `¿Deseas borrar al usuario "${userName}" (ID: ${userId})? Esta acción no se puede deshacer.`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, borrar",
          cancelButtonText: "Cancelar",
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "index.php?controller=admin&action=deleteUsuario",
              type: "POST",
              data: { id_usuario: userId },
              dataType: "json",
              success: function (response) {
                if (response.status === "ok") {
                  Swal.fire({
                    icon: "success",
                    title: "¡Borrado!",
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false,
                  });
                  currentDataTable.ajax.reload();
                } else {
                  Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.message,
                  });
                }
              },
              error: function (error) {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Hubo un error al borrar el usuario. Inténtalo de nuevo.",
                });
                console.error("Error AJAX:", error);
              },
            });
          }
        });
      });
  }

  function loadPedidos() {
    cleanupBeforeLoad();

    const cardHtml = `
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Gestión de Pedidos</h4>
          <button class="btn btn-success btn-sm" id="btn-crear-pedido">
            <i class="bi bi-plus-circle"></i> Crear Pedido
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered nowrap" style="width:100%" id="tabla-pedidos">
              <thead>
                <tr>
                  <th>ID Pedido</th>
                  <th>ID Usuario</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Dirección</th>
                  <th>Acciones</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>`;

    $("#admin-content").html(cardHtml);

    currentDataTable = $("#tabla-pedidos").DataTable({
      ...dtConfig,
      ajax: {
        url: "index.php?controller=admin&action=getPedidosJSON",
        type: "GET",
        dataSrc: "",
      },
      columns: [
        { data: "id_pedido" },
        { data: "id_usuario" },
        { data: "fecha_pedido" },
        {
          data: "total",
          render: function (data, type, row) {
            return `<span class="precio-pedido" data-eur="${parseFloat(
              data
            )}">${parseFloat(data).toFixed(2)} €</span>`;
          },
        },
        { data: "direccion" },
        {
          data: null,
          render: function (data, type, row) {
            return `
              <button class="btn btn-warning btn-sm btn-editar-pedido" data-id="${row.id_pedido}">Editar</button>
              <button class="btn btn-danger btn-sm btn-borrar-pedido" data-id="${row.id_pedido}">Borrar</button>
            `;
          },
        },
      ],
    });

    setupPedidosEvents();
    initializeCurrencyForPedidos();
  }

  function setupPedidosEvents() {
    $(document)
      .off("click", "#btn-crear-pedido")
      .on("click", "#btn-crear-pedido", function () {
        const modal = createModal(
          "modalCrearPedido",
          "Crear Pedido",
          `
              <div class="mb-3">
                <label for="id_usuario_select" class="form-label">Usuario <span class="text-danger">*</span></label>
                <select class="form-select" id="id_usuario_select" name="id_usuario" required>
                  <!-- Se llenará por AJAX -->
                </select>
              </div>
  
              <div class="mb-3">
                <label for="direccion_select" class="form-label">Dirección <span class="text-danger">*</span></label>
                <select class="form-select" id="direccion_select" name="direccion" required>
                  <!-- Se llenará al seleccionar un usuario -->
                </select>
              </div>
  
              <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono">
              </div>
              <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo">
              </div>
              <div class="mb-3">
                <label for="metodo_pago" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                  <option value="">Selecciona un método de pago</option>
                  <option value="PayPal">PayPal</option>
                  <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                  <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                </select>
              </div>
              <hr>
              <h5>Productos</h5>
              <button type="button" class="btn btn-primary btn-sm mb-2" id="btn-agregar-producto-pedido">Agregar Producto</button>
              <table class="table table-bordered" id="tabla-productos-pedido">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Filas dinámicas de productos -->
                </tbody>
              </table>
              <div class="text-end">
                <table class="table">
                  <tr>
                    <th>Subtotal:</th>
                    <td id="subtotal_pedido_modal">0.00 €</td>
                  </tr>
                  <tr>
                    <th>Gastos de Envío:</th>
                    <td id="gastos_envio_pedido_modal">0.00 €</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td id="total_pedido_modal">0.00 €</td>
                  </tr>
                </table>
              </div>
          `,
          `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          `,
          true
        );
        modal.show();

        $.ajax({
          url: "index.php?controller=admin&action=getUsuariosJSON",
          method: "GET",
          dataType: "json",
          success: function (usuarios) {
            const options = [
              '<option value="">Selecciona un usuario</option>',
              ...usuarios.map(
                (u) =>
                  `<option value="${u.id_usuario}">${u.nombre_completo}</option>`
              ),
            ].join("");

            $("#id_usuario_select").html(options);
          },
          error: function (err) {
            console.error("Error cargando usuarios:", err);
            Swal.fire(
              "Error",
              "No se pudo obtener la lista de usuarios",
              "error"
            );
          },
        });

        $("#id_usuario_select").on("change", function () {
          const selectedUserId = $(this).val();
          if (!selectedUserId) {
            $("#direccion_select").html(
              '<option value="">Sin direcciones</option>'
            );
            $("#telefono").val("");
            $("#correo").val("");
            return;
          }

          $.ajax({
            url: "index.php?controller=admin&action=getUsuarioDetallesJSON",
            method: "GET",
            data: { id_usuario: selectedUserId },
            dataType: "json",
            success: function (response) {
              if (response.status === "ok") {
                const u = response.usuario;
                $("#telefono").val(u.telefono);
                $("#correo").val(u.correo);

                let dirOptions =
                  '<option value="">Selecciona una dirección</option>';
                u.direcciones.forEach((d) => {
                  dirOptions += `<option value="${d.texto}">${d.texto}</option>`;
                });
                $("#direccion_select").html(dirOptions);
              } else {
                Swal.fire("Error", response.message, "error");
              }
            },
            error: function (err) {
              console.error("Error:", err);
              Swal.fire(
                "Error",
                "No se pudo obtener detalles del usuario",
                "error"
              );
            },
          });
        });

        $("#form-modalCrearPedido").on(
          "click",
          "#btn-agregar-producto-pedido",
          function () {
            $.ajax({
              url: "index.php?controller=admin&action=getProductosJSON",
              type: "GET",
              dataType: "json",
              success: function (productos) {
                let options =
                  '<option value="">Selecciona un producto</option>';
                productos.forEach((producto) => {
                  options += `
                  <option
                    value="${producto.id_producto}"
                    data-precio="${parseFloat(producto.precio_base).toFixed(2)}"
                  >
                    ${producto.nombre}
                  </option>
                `;
                });

                const fila = `
                <tr>
                  <td>
                    <select class="form-select producto-select" required>
                      ${options}
                    </select>
                  </td>
                  <td>
                    <input type="number" class="form-control precio-unitario" readonly>
                  </td>
                  <td>
                    <input type="number" class="form-control cantidad" min="1" value="1" required>
                  </td>
                  <td>
                    <input type="number" class="form-control total-producto" readonly value="0.00">
                  </td>
                  <td>
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar-producto">Eliminar</button>
                  </td>
                </tr>
              `;
                $("#tabla-productos-pedido tbody").append(fila);
              },
              error: function (error) {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "No se pudo cargar la lista de productos.",
                });
              },
            });
            recalcularTotalesModal();
          }
        );

        $("#form-modalCrearPedido").on(
          "change",
          ".producto-select",
          function () {
            const fila = $(this).closest("tr");
            const rawPrecio = $(this).find(":selected").data("precio");

            const precio = parseFloat(rawPrecio) || 0;

            fila.find(".precio-unitario").val(precio.toFixed(2));

            const cantidad = parseInt(fila.find(".cantidad").val()) || 0;

            const total = precio * cantidad;

            fila.find(".total-producto").attr("value", total.toFixed(2));

            recalcularTotalesModal();
          }
        );

        $("#form-modalCrearPedido").on("input", ".cantidad", function () {
          const fila = $(this).closest("tr");
          const precio = parseFloat(fila.find(".precio-unitario").val()) || 0;

          const cantidad = parseInt($(this).val()) || 0;

          const total = precio * cantidad;
          fila.find(".total-producto").val(total.toFixed(2));

          recalcularTotalesModal();
        });

        $("#form-modalCrearPedido").on(
          "click",
          ".btn-eliminar-producto",
          function () {
            $(this).closest("tr").remove();
            recalcularTotalesModal();
          }
        );

        $("#form-modalCrearPedido").on("submit", function (e) {
          e.preventDefault();

          const id_usuario = $("#id_usuario_select").val().trim();
          const direccion = $("#direccion_select").val().trim();
          const metodo_pago = $("#metodo_pago").val();

          const subtotal = parseFloat($("#subtotal_pedido_modal").text()) || 0;
          const gastosEnvio =
            parseFloat($("#gastos_envio_pedido_modal").text()) || 0;
          const total = parseFloat($("#total_pedido_modal").text()) || 0;

          if (!id_usuario) {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "El ID de usuario es obligatorio.",
            });
            return;
          }

          if (!direccion) {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "La dirección es obligatoria.",
            });
            return;
          }

          if (!metodo_pago) {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Debes seleccionar un método de pago.",
            });
            return;
          }

          if ($("#tabla-productos-pedido tbody tr").length === 0) {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Debes agregar al menos un producto al pedido.",
            });
            return;
          }

          const filas = $("#tabla-productos-pedido tbody tr").toArray();

          const productos = filas.map((fila, index) => {
            const $fila = $(fila);
            const id_producto = $fila.find(".producto-select").val();
            const nombre_producto = $fila
              .find(".producto-select option:selected")
              .text();
            const precio_unitario =
              parseFloat($fila.find(".precio-unitario").val()) || 0;
            const cantidad = parseInt($fila.find(".cantidad").val()) || 0;
            const total_producto =
              parseFloat($fila.find(".total-producto").val()) || 0;

            return {
              id_producto,
              nombre_producto,
              precio_unitario,
              cantidad,
              total_producto,
              index: index + 1,
            };
          });

          const productosInvalidos = productos.filter((producto) => {
            if (!producto.id_producto) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: `El producto en la fila ${producto.index} no está seleccionado.`,
              });
              return true;
            }

            if (producto.cantidad <= 0) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: `La cantidad en la fila ${producto.index} debe ser mayor a cero.`,
              });
              return true;
            }

            return false;
          });

          if (productosInvalidos.length > 0) {
            return;
          }

          const $submitButton = $(this).find('button[type="submit"]');
          $submitButton.prop("disabled", true).text("Guardando...");

          const pedidoData = {
            id_usuario,
            direccion,
            telefono: $("#telefono").val().trim(),
            correo: $("#correo").val().trim(),
            metodo_pago,
            subtotal,
            gastos_envio: gastosEnvio,
            total,
            productos,
          };

          $.ajax({
            url: "index.php?controller=admin&action=createPedido",
            type: "POST",
            data: JSON.stringify(pedidoData),
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
              if (response.status === "ok") {
                Swal.fire({
                  icon: "success",
                  title: "¡Éxito!",
                  text: response.message,
                  timer: 1500,
                  showConfirmButton: false,
                }).then(() => {
                  modal.hide();
                  currentDataTable.ajax.reload(null, false);
                });
              } else {
                if (response.errors) {
                  const errorMessages = Object.values(response.errors).join(
                    "<br>"
                  );
                  Swal.fire({
                    icon: "error",
                    title: "Errores de Validación",
                    html: errorMessages,
                  });
                } else {
                  Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.message,
                  });
                }
                $submitButton.prop("disabled", false).text("Guardar");
              }
            },
            error: function (xhr, status, error) {
              console.error("Error AJAX:", status, error);
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Ocurrió un error inesperado.",
              });
              $submitButton.prop("disabled", false).text("Guardar");
            },
          });
        });
      });

    // Delegación de eventos para botones de editar y borrar pedidos
    $("#tabla-pedidos tbody").on("click", ".btn-editar-pedido", function () {
      const pedidoId = $(this).data("id");
      // 1) Solicita detalles del pedido vía AJAX
      $.ajax({
        url: "index.php?controller=admin&action=getPedidoDetallesJSON",
        method: "GET",
        data: { id_pedido: pedidoId },
        dataType: "json",
        success: function (response) {
          if (response.status === "ok") {
            // 2) Muestra el modal pre-cargado
            showEditarPedidoModal(
              response.pedido,
              response.productosDisponibles
            );
          } else {
            Swal.fire("Error", response.message, "error");
          }
        },
        error: function (err) {
          console.error("Error al obtener detalles del pedido:", err);
          Swal.fire(
            "Error",
            "No se pudo obtener el detalle del pedido",
            "error"
          );
        },
      });
    });

    function showEditarPedidoModal(pedido, productosDisponibles) {
      // Crear el modal (SIN form en createModal, porque lo pondremos dentro)
      const modalId = "modalEditarPedido";
      const modal = createModal(
        modalId,
        "Editar Pedido (ID " + pedido.id_pedido + ")",
        `
              <input type="hidden" name="id_pedido" value="${pedido.id_pedido}">
  
              <!-- ID Usuario -->
              <div class="mb-3">
                <label for="id_usuario_select_ed" class="form-label">Usuario <span class="text-danger">*</span></label>
                <select class="form-select" id="id_usuario_select_ed" name="id_usuario" required>
                  <!-- Se llenará con la lista de usuarios (ver más abajo) -->
                </select>
              </div>
  
              <!-- Dirección -->
              <div class="mb-3">
                <label for="direccion_ed" class="form-label">Dirección <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="direccion_ed" name="direccion" value="${
                  pedido.direccion
                }" required>
              </div>
  
              <!-- Teléfono y Correo -->
              <div class="mb-3">
                <label for="telefono_ed" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono_ed" name="telefono" value="${
                  pedido.telefono
                }">
              </div>
              <div class="mb-3">
                <label for="correo_ed" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo_ed" name="correo" value="${
                  pedido.correo
                }">
              </div>
  
              <!-- Método de Pago -->
              <div class="mb-3">
                <label for="metodo_pago_ed" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                <select class="form-select" id="metodo_pago_ed" name="metodo_pago" required>
                  <option value="">Selecciona un método de pago</option>
                  <option value="PayPal" ${
                    pedido.metodo_pago === "PayPal" ? "selected" : ""
                  }>PayPal</option>
                  <option value="Tarjeta de Crédito" ${
                    pedido.metodo_pago === "Tarjeta de Crédito"
                      ? "selected"
                      : ""
                  }>Tarjeta de Crédito</option>
                  <option value="Transferencia Bancaria" ${
                    pedido.metodo_pago === "Transferencia Bancaria"
                      ? "selected"
                      : ""
                  }>Transferencia Bancaria</option>
                </select>
              </div>
  
              <hr>
              <h5>Productos</h5>
              <button type="button" class="btn btn-primary btn-sm mb-2" id="btn-agregar-producto-pedido-ed">
                Agregar Producto
              </button>
  
              <table class="table table-bordered" id="tabla-productos-pedido-ed">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Llenado dinámicamente -->
                </tbody>
              </table>
              <div class="text-end">
                <table class="table">
                  <tr>
                    <th>Subtotal:</th>
                    <td id="subtotal_pedido_ed_modal">0.00 €</td>
                  </tr>
                  <tr>
                    <th>Gastos de Envío:</th>
                    <td id="gastos_envio_pedido_ed_modal">0.00 €</td>
                  </tr>
                  <tr>
                    <th>Total:</th>
                    <td id="total_pedido_ed_modal">0.00 €</td>
                  </tr>
                </table>
              </div>
          `,
        `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary" id="btn-update-pedido">Actualizar</button>
          `,
        true
      );

      modal.show();

      // 1) Cargar la lista completa de usuarios (similar a createPedido)
      //    Para preseleccionar "pedido.id_usuario"
      $.ajax({
        url: "index.php?controller=admin&action=getUsuariosJSON",
        method: "GET",
        dataType: "json",
        success: function (usuarios) {
          if (!Array.isArray(usuarios)) {
            console.error("La respuesta no es un array.");
            Swal.fire(
              "Error",
              "Formato de datos inválido recibido del servidor.",
              "error"
            );
            return;
          }

          const options = [
            '<option value="">Selecciona un usuario</option>',
            ...usuarios.map(
              (u) =>
                `<option value="${u.id_usuario}" ${
                  u.id_usuario == pedido.id_usuario ? "selected" : ""
                }>${u.nombre_completo}</option>`
            ),
          ].join("");

          $("#id_usuario_select_ed").html(options);
        },
        error: function (err) {
          console.error("Error cargando usuarios (Editar Pedido):", err);
          Swal.fire(
            "Error",
            "No se pudo obtener la lista de usuarios",
            "error"
          );
        },
      });

      // 2) Renderizar productos existentes en "pedido.productos"
      //    (Parecido a tu create, pero con valores ya asignados)
      renderProductosEd(pedido.productos, productosDisponibles);

      // 3) Lógica para recalcular total, etc.
      //    (igual que tu create: cambiar select => precio, input => recalcula, etc.)
      attachEditarPedidoEvents(productosDisponibles);

      // 4) Manejar el "submit" => updatePedido
      // Manejar el envío del formulario de edición
      $("#form-" + modalId).on("submit", function (e) {
        e.preventDefault();

        // Recolectar datos
        const id_pedido = pedido.id_pedido;
        const id_usuario = $("#id_usuario_select_ed").val();
        const direccion = $("#direccion_ed").val().trim();
        const telefono = $("#telefono_ed").val().trim();
        const correo = $("#correo_ed").val().trim();
        const metodo_pago = $("#metodo_pago_ed").val();

        // Obtener los totales
        const subtotal = parseFloat($("#subtotal_pedido_ed_modal").text()) || 0;
        const gastosEnvio =
          parseFloat($("#gastos_envio_pedido_ed_modal").text()) || 0;
        const total = parseFloat($("#total_pedido_ed_modal").text()) || 0;

        // Validaciones
        if (!id_usuario) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "El ID de usuario es obligatorio.",
          });
          return;
        }

        if (!direccion) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "La dirección es obligatoria.",
          });
          return;
        }

        if (!metodo_pago) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Debes seleccionar un método de pago.",
          });
          return;
        }

        if ($("#tabla-productos-pedido-ed tbody tr").length === 0) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Debes agregar al menos un producto al pedido.",
          });
          return;
        }

        // Obtener todas las filas de productos
        const filas = $("#tabla-productos-pedido-ed tbody tr").toArray();

        // Mapear cada fila a un objeto de producto
        const productos = filas.map((fila, index) => {
          const $fila = $(fila);
          const id_producto = $fila.find(".producto-select-ed").val();
          const nombre_producto = $fila
            .find(".producto-select-ed option:selected")
            .text();
          const precio_unitario =
            parseFloat($fila.find(".precio-unitario").val()) || 0;
          const cantidad = parseInt($fila.find(".cantidad").val()) || 0;
          const total_producto =
            parseFloat($fila.find(".total-producto").val()) || 0;

          return {
            id_producto,
            nombre_producto,
            precio_unitario,
            cantidad,
            total_producto,
            index: index + 1,
          };
        });

        // Filtrar los productos que tienen datos inválidos
        const productosInvalidos = productos.filter((producto) => {
          if (!producto.id_producto) {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: `El producto en la fila ${producto.index} no está seleccionado.`,
            });
            return true;
          }

          if (producto.cantidad <= 0) {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: `La cantidad en la fila ${producto.index} debe ser mayor a cero.`,
            });
            return true;
          }

          return false;
        });

        // Verificar si hay productos inválidos
        if (productosInvalidos.length > 0) {
          return; // Detener el proceso si hay errores
        }

        // Deshabilitar el botón de actualizar para prevenir múltiples clics
        const $submitButton = $("#btn-update-pedido");
        $submitButton.prop("disabled", true).text("Actualizando...");

        // Construir el objeto de pedido
        const pedidoData = {
          id_pedido,
          id_usuario,
          direccion,
          telefono: $("#telefono_ed").val().trim(),
          correo: $("#correo_ed").val().trim(),
          metodo_pago,
          subtotal,
          gastos_envio: gastosEnvio,
          total,
          productos,
        };

        // Solicitud AJAX para actualizar el pedido
        $.ajax({
          url: "index.php?controller=admin&action=updatePedido",
          type: "POST",
          data: JSON.stringify(pedidoData),
          contentType: "application/json",
          dataType: "json",
          success: function (response) {
            if (response.status === "ok") {
              Swal.fire({
                icon: "success",
                title: "¡Éxito!",
                text: response.message,
                timer: 1500,
                showConfirmButton: false,
              }).then(() => {
                modal.hide();
                currentDataTable.ajax.reload(null, false);
              });
            } else {
              if (response.errors) {
                const errorMessages = Object.values(response.errors).join(
                  "<br>"
                );
                Swal.fire({
                  icon: "error",
                  title: "Errores de Validación",
                  html: errorMessages,
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                });
              }
              $submitButton.prop("disabled", false).text("Actualizar");
            }
          },
          error: function (xhr, status, error) {
            console.error("Error AJAX:", status, error);
            console.error("Respuesta del servidor:", xhr.responseText);
            Swal.fire("Error", "Ocurrió un error inesperado.", "error");
            $submitButton.prop("disabled", false).text("Actualizar");
          },
        });
      });
    }

    function renderProductosEd(productosPedido, productosDisponibles) {
      const $tbody = $("#tabla-productos-pedido-ed tbody");
      $tbody.empty();

      productosPedido.forEach((det) => {
        // Generar <option> de productos con "selected" si coincide
        let options = '<option value="">Selecciona un producto</option>';
        productosDisponibles.forEach((prod) => {
          const selected =
            prod.id_producto == det.id_producto ? "selected" : "";
          options += `
              <option 
                value="${prod.id_producto}"
                data-precio="${parseFloat(prod.precio_base).toFixed(2)}"
                ${selected}
              >
                ${prod.nombre}
              </option>
            `;
        });

        // Fila con valores precargados
        const fila = `
            <tr>
              <td>
                <select class="form-select producto-select-ed" required>
                  ${options}
                </select>
              </td>
              <td>
                <input type="number" class="form-control precio-unitario" readonly value="${parseFloat(
                  det.precio_unitario
                ).toFixed(2)}">
              </td>
              <td>
                <input type="number" class="form-control cantidad" min="1" value="${
                  det.cantidad
                }" required>
              </td>
              <td>
                <input type="number" class="form-control total-producto" readonly value="${parseFloat(
                  det.total_producto
                ).toFixed(2)}">
              </td>
              <td>
                <button type="button" class="btn btn-danger btn-sm btn-eliminar-producto-ed">
                  Eliminar
                </button>
              </td>
            </tr>
          `;
        $tbody.append(fila);
      });

      // Calcular total inicial
      recalcularTotalesModalEd();
    }

    function attachEditarPedidoEvents(productosDisponibles) {
      //Borramos eventos previos para evitar duplicaciones
      $(document).off("click", "#btn-agregar-producto-pedido-ed");
      // Handler para "Agregar Producto" en la edición
      $(document).on("click", "#btn-agregar-producto-pedido-ed", function () {
        // Generar la fila vacía
        let options = '<option value="">Selecciona un producto</option>';
        productosDisponibles.forEach((prod) => {
          options += `
              <option
                value="${prod.id_producto}"
                data-precio="${parseFloat(prod.precio_base).toFixed(2)}"
              >
                ${prod.nombre}
              </option>
            `;
        });

        const nuevaFila = `
            <tr>
              <td>
                <select class="form-select producto-select-ed" required>
                  ${options}
                </select>
              </td>
              <td><input type="number" class="form-control precio-unitario" readonly value="0.00"></td>
              <td><input type="number" class="form-control cantidad" min="1" value="1" required></td>
              <td><input type="number" class="form-control total-producto" readonly value="0.00"></td>
              <td>
                <button type="button" class="btn btn-danger btn-sm btn-eliminar-producto-ed">Eliminar</button>
              </td>
            </tr>
          `;
        $("#tabla-productos-pedido-ed tbody").append(nuevaFila);
      });

      // Handler: al cambiar la selección del producto
      $(document).on("change", ".producto-select-ed", function () {
        const $fila = $(this).closest("tr");
        const precio =
          parseFloat($(this).find(":selected").data("precio")) || 0;
        const cant = parseInt($fila.find(".cantidad").val()) || 0;
        const total = precio * cant;

        $fila.find(".precio-unitario").val(precio.toFixed(2));
        $fila.find(".total-producto").val(total.toFixed(2));

        // Recalcular los totales
        recalcularTotalesModalEd();
      });

      // Handler: al cambiar la cantidad
      $(document).on(
        "input",
        "#tabla-productos-pedido-ed .cantidad",
        function () {
          const $fila = $(this).closest("tr");
          const precio = parseFloat($fila.find(".precio-unitario").val()) || 0;
          const cant = parseInt($(this).val()) || 0;
          $fila.find(".total-producto").val((precio * cant).toFixed(2));
          // Recalcular los totales
          recalcularTotalesModalEd();
        }
      );

      // Handler: eliminar producto
      $(document).on("click", ".btn-eliminar-producto-ed", function () {
        $(this).closest("tr").remove();
        // Recalcular los totales
        recalcularTotalesModalEd();
      });
    }

    $("#tabla-pedidos tbody").on("click", ".btn-borrar-pedido", function () {
      const pedidoId = $(this).data("id");

      Swal.fire({
        title: "¿Estás seguro?",
        text: `¿Deseas borrar el pedido con ID: ${pedidoId}? Esta acción no se puede deshacer.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, borrar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          // Enviar solicitud AJAX para borrar el pedido
          $.ajax({
            url: "index.php?controller=admin&action=deletePedido",
            type: "POST",
            data: { id_pedido: pedidoId },
            dataType: "json",
            success: function (response) {
              if (response.status === "ok") {
                Swal.fire({
                  icon: "success",
                  title: "¡Borrado!",
                  text: response.message,
                  timer: 1500,
                  showConfirmButton: false,
                });
                currentDataTable.ajax.reload();
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                });
              }
            },
            error: function (error) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Hubo un error al borrar el pedido. Inténtalo de nuevo.",
              });
              console.error("Error AJAX:", error);
            },
          });
        }
      });
    });
    // Inicializar la conversión de moneda para Pedidos
    CurrencyConverter.fetchCurrencyRates().then((rates) => {
      if (rates) {
        // Verificar si hay una moneda seleccionada previamente
        const savedCurrency =
          sessionStorage.getItem("selectedCurrency") || "EUR";
        $("#select-moneda-admin").val(savedCurrency);
        CurrencyConverter.actualizarPrecios(".precio-pedido", savedCurrency);

        // Actualizar la cabecera de la tabla
        const simboloCabecera = obtenerSimboloMoneda(savedCurrency);
        $("#tabla-pedidos thead tr th")
          .eq(3)
          .text(`Total (${simboloCabecera})`);
      }
    });
  }
  // ==========================
  //        PRODUCTOS
  // ==========================
  function loadProductos() {
    cleanupBeforeLoad();

    // Similar estructura que usuarios/pedidos pero simplificada
    const cardHtml = `
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Gestión de Productos</h4>
          <button class="btn btn-success btn-sm" id="btn-crear-producto">
            <i class="bi bi-plus-circle"></i> Crear Producto
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered nowrap" style="width:100%" id="tabla-productos">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Precio Base</th>
                  <th>Tipo</th>
                  <th>Imagen</th>
                  <th>Acciones</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>`;

    $("#admin-content").html(cardHtml);

    currentDataTable = $("#tabla-productos").DataTable({
      ...dtConfig,
      responsive: true,
      ajax: {
        url: "index.php?controller=admin&action=getProductosJSON",
        type: "GET",
        dataSrc: "",
      },
      columns: [
        { data: "id_producto", responsivePriority: 1, width: "5%" },
        { data: "nombre", responsivePriority: 2, width: "15%" },
        {
          data: "descripcion",
          responsivePriority: 3,
          width: "30%",
          render: function (data, type, row) {
            const maxLength = 100;
            return data.length > maxLength
              ? data.substr(0, maxLength) + "..."
              : data;
          },
        },
        {
          data: "precio_base",
          responsivePriority: 2,
          width: "10%",
          render: function (data, type, row) {
            return `<span class="precio-base" data-eur="${parseFloat(
              data
            )}">${parseFloat(data).toFixed(2)} €</span>`;
          },
        },
        { data: "tipo", responsivePriority: 2, width: "10%" },
        {
          data: "img",
          responsivePriority: 1,
          width: "10%",
          render: function (data, type, row) {
            return data
              ? `<img src="${data}" alt="Imagen" class="img-thumbnail" width="50">`
              : "No disponible";
          },
          orderable: false,
          searchable: false,
        },
        {
          data: null,
          responsivePriority: 1,
          className: "all",
          width: "15%",
          render: function (data, type, row) {
            return `
              <button class="btn btn-warning btn-sm btn-editar-producto" data-id="${row.id_producto}">Editar</button>
              <button class="btn btn-danger btn-sm btn-borrar-producto" data-id="${row.id_producto}">Borrar</button>
            `;
          },
          orderable: false,
          searchable: false,
        },
      ],
    });

    setupProductosEvents();
    initializeCurrencyForProductos();
  }
  // DOM + EVENTS: Configuración de eventos específicos para gestión de productos
  function setupProductosEvents() {
    // EVENTS: Configurar evento para crear nuevo producto - prevenir duplicados con .off()
    $(document)
      .off("click", "#btn-crear-producto")
      .on("click", "#btn-crear-producto", function () {
        const modal = createModal(
          "modalCrearProducto",
          "Crear Producto",
          `
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion">
          </div>
          <div class="mb-3">
            <label for="precio_base" class="form-label">Precio Base <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="precio_base" name="precio_base" required>
          </div>
          <div class="mb-3">
            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
            <select class="form-select" id="tipo" name="tipo" required>
              <option value="">Selecciona un tipo</option>
              <option value="Bowl">Bowl</option>
              <option value="Postre">Postre</option>
              <option value="Bebida">Bebida</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="img" class="form-label">Ruta de la Imagen del Producto <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="img" name="img" placeholder="img/Productos/imagen1.jpg" required>
          </div>
        `,
          `
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary" id="save-producto">Guardar</button>
        `,
          true
        );
        modal.show();

        // EVENTS: Configurar envío del formulario - manejo completo de validación y AJAX
        $("#form-modalCrearProducto")
          .off("submit")
          .on("submit", function (e) {
            e.preventDefault();

            // BUSINESS LOGIC: Validaciones del lado cliente antes del envío
            const nombre = $("#nombre").val().trim();
            const precio_base = $("#precio_base").val();
            const tipo = $("#tipo").val();

            if (!nombre) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "El nombre del producto es obligatorio.",
              });
              return;
            }

            if (!precio_base || isNaN(precio_base) || precio_base <= 0) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "El precio base debe ser un número positivo.",
              });
              return;
            }

            if (!tipo) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Debes seleccionar un tipo de producto.",
              });
              return;
            }

            // DOM: Deshabilitar botón para prevenir doble-envío durante AJAX
            const $submitButton = $("#save-producto");
            $submitButton.prop("disabled", true).text("Guardando..."); // AJAX: Envío serializado de datos del formulario al servidor
            $.ajax({
              url: "index.php?controller=admin&action=createProducto",
              type: "POST",
              data: $(this).serialize(),
              dataType: "json",
              success: function (response) {
                if (response.status === "ok") {
                  Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false,
                  }).then(() => {
                    modal.hide();
                    // DOM: Recargar tabla sin resetear paginación para mantener estado de usuario
                    currentDataTable.ajax.reload(null, false);
                  });
                } else if (response.status === "error") {
                  if (response.errors) {
                    const errorMessages = Object.values(response.errors).join(
                      "<br>"
                    );
                    Swal.fire({
                      icon: "error",
                      title: "Errores de Validación",
                      html: errorMessages,
                    });
                  } else {
                    Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: response.message,
                    });
                  }
                  $submitButton.prop("disabled", false).text("Guardar");
                }
              },
              error: function (xhr, status, error) {
                console.error("Error AJAX:", status, error);
                console.error("Respuesta del servidor:", xhr.responseText);
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Ocurrió un error inesperado.",
                });
                $submitButton.prop("disabled", false).text("Guardar");
              },
            });
          });
      });

    // EVENTS: Delegación para botones de editar - detecta clics en elementos dinámicos
    $("#tabla-productos tbody").on(
      "click",
      ".btn-editar-producto",
      function () {
        const productoId = $(this).data("id");

        // DOM: Obtener datos de la fila seleccionada desde DataTable
        const rowData = currentDataTable.row($(this).parents("tr")).data();

        const modal = createModal(
          "modalEditarProducto",
          "Editar Producto",
          `
          <input type="hidden" id="id_producto" name="id_producto" value="${
            rowData.id_producto
          }">
          <div class="mb-3">
            <label for="nombre_editar" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nombre_editar" name="nombre" value="${
              rowData.nombre
            }" required>
          </div>
          <div class="mb-3">
            <label for="descripcion_editar" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcion_editar" name="descripcion" value="${
              rowData.descripcion
            }">
          </div>
          <div class="mb-3">
            <label for="precio_base_editar" class="form-label">Precio Base <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="precio_base_editar" name="precio_base" value="${
              rowData.precio_base
            }" required>
          </div>
          <div class="mb-3">
            <label for="tipo_editar" class="form-label">Tipo <span class="text-danger">*</span></label>
            <select class="form-select" id="tipo_editar" name="tipo" required>
              <option value="">Selecciona un tipo</option>
              <option value="Bowl" ${
                rowData.tipo === "Bowl" ? "selected" : ""
              }>Bowl</option>
              <option value="Postre" ${
                rowData.tipo === "Postre" ? "selected" : ""
              }>Postre</option>
              <option value="Bebida" ${
                rowData.tipo === "Bebida" ? "selected" : ""
              }>Bebida</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="img" class="form-label">Ruta de la Imagen del Producto <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="img" name="img" placeholder="img/Productos/imagen1.jpg">
          </div>
        `,
          `
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary" id="update-producto">Actualizar</button>
        `,
          true
        );
        modal.show();

        // EVENTS: Configurar envío del formulario de edición con validaciones
        $("#form-modalEditarProducto")
          .off("submit")
          .on("submit", function (e) {
            e.preventDefault();

            // BUSINESS LOGIC: Validaciones del lado cliente para datos actualizados
            const nombre = $("#nombre_editar").val().trim();
            const precio_base = $("#precio_base_editar").val();
            const tipo = $("#tipo_editar").val();

            if (!nombre) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "El nombre del producto es obligatorio.",
              });
              return;
            }

            if (!precio_base || isNaN(precio_base) || precio_base <= 0) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "El precio base debe ser un número positivo.",
              });
              return;
            }

            if (!tipo) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Debes seleccionar un tipo de producto.",
              });
              return;
            }

            // DOM: Deshabilitar botón durante envío para prevenir duplicación
            const $submitButton = $("#update-producto");
            $submitButton.prop("disabled", true).text("Actualizando...");

            // AJAX: Envío de datos actualizados al servidor
            $.ajax({
              url: "index.php?controller=admin&action=updateProducto",
              type: "POST",
              data: $(this).serialize(),
              dataType: "json",
              success: function (response) {
                if (response.status === "ok") {
                  Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false,
                  }).then(() => {
                    modal.hide();
                    currentDataTable.ajax.reload(null, false);
                  });
                } else if (response.status === "error") {
                  if (response.errors) {
                    const errorMessages = Object.values(response.errors).join(
                      "<br>"
                    );
                    Swal.fire({
                      icon: "error",
                      title: "Errores de Validación",
                      html: errorMessages,
                    });
                  } else {
                    Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: response.message,
                    });
                  }
                  $submitButton.prop("disabled", false).text("Actualizar");
                }
              },
              error: function (xhr, status, error) {
                console.error("Error AJAX:", status, error);
                console.error("Respuesta del servidor:", xhr.responseText);
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Ocurrió un error inesperado.",
                });
                $submitButton.prop("disabled", false).text("Actualizar");
              },
            });
          });
      }
    );

    // EVENTS: Delegación para botones de borrar - confirmación previa obligatoria
    $("#tabla-productos tbody").on(
      "click",
      ".btn-borrar-producto",
      function () {
        const productoId = $(this).data("id");

        // HELPER: Modal de confirmación para prevenir eliminación accidental
        Swal.fire({
          title: "¿Estás seguro?",
          text: `¿Deseas borrar el producto con ID: ${productoId}? Esta acción no se puede deshacer.`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, borrar",
          cancelButtonText: "Cancelar",
        }).then((result) => {
          if (result.isConfirmed) {
            // AJAX: Solicitud de eliminación al servidor con ID específico
            $.ajax({
              url: "index.php?controller=admin&action=deleteProducto",
              type: "POST",
              data: { id_producto: productoId },
              dataType: "json",
              success: function (response) {
                if (response.status === "ok") {
                  Swal.fire({
                    icon: "success",
                    title: "¡Borrado!",
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false,
                  });
                  // DOM: Recargar tabla tras eliminación exitosa
                  currentDataTable.ajax.reload();
                } else {
                  Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.message,
                  });
                }
              },
              error: function (error) {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Hubo un error al borrar el producto. Inténtalo de nuevo.",
                });
                console.error("Error AJAX:", error);
              },
            });
          }
        });
      }
    );
  }

  // CURRENCY + DOM: Inicialización del conversor de monedas para productos
  function initializeCurrencyForProductos() {
    CurrencyConverter.fetchCurrencyRates().then((rates) => {
      if (rates) {
        // DOM: Recuperar moneda guardada del usuario para persistencia
        const savedCurrency =
          sessionStorage.getItem("selectedCurrency") || "EUR";
        $("#select-moneda-admin").val(savedCurrency);
        // CURRENCY: Actualizar todos los precios mostrados según moneda seleccionada
        CurrencyConverter.actualizarPrecios(".precio-base", savedCurrency);

        // DOM: Actualizar cabecera de tabla con símbolo de moneda correspondiente
        const simboloCabecera = obtenerSimboloMoneda(savedCurrency);
        $("#tabla-productos thead tr th")
          .eq(3)
          .text(`Precio Base (${simboloCabecera})`);
      }
    });
  }

  // DOM: Carga y configuración de la sección de logs del sistema
  function loadLogs() {
    cleanupBeforeLoad();

    const cardHtml = `
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Logs del Sistema</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered nowrap" style="width:100%" id="tabla-logs">
              <thead>
                <tr>
                  <th>ID Log</th>
                  <th>Tabla</th>
                  <th>Acción</th>
                  <th>ID Afectado</th>
                  <th>Fecha</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>      </div>`;

    // DOM: Inyectar HTML de la tabla de logs en el contenedor principal
    $("#admin-content").html(cardHtml);

    // DOM: Inicializar DataTable para logs con configuración de solo-lectura
    currentDataTable = $("#tabla-logs").DataTable({
      ...dtConfig,
      ajax: {
        url: "index.php?controller=admin&action=getLogsJSON",
        type: "GET",
        dataSrc: "",
      },
      columns: [
        { data: "id_log" },
        { data: "tabla" },
        { data: "tipo_accion" },
        { data: "id_afectado" },
        { data: "fecha" },
      ],
    });
  }

  // ===== INICIALIZACIÓN DEL SISTEMA =====
  // EVENTS: Configurar navegación principal del panel
  initializeNavigation();
  // EVENTS: Configurar eventos globales (monedas, cleanup, etc.)
  initializeGlobalEvents();
  // DOM: Restaurar pestaña activa desde sesión o cargar usuarios por defecto
  const savedTab = sessionStorage.getItem("selectedTab");
  if (savedTab === "btn-usuarios") loadUsuarios();
  else if (savedTab === "btn-pedidos") loadPedidos();
  else if (savedTab === "btn-productos") loadProductos();
  else if (savedTab === "btn-logs") loadLogs();
  else loadUsuarios(); // DOM: Cargar usuarios como sección por defecto
});
