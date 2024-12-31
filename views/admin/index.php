<!-- views/admin/index.php -->
<?php
    include_once "views/TopNav.php";
?>

<div class="container my-5">
    <h2 class="text-center">Panel de Administración</h2>
    <hr>

    <!-- Botones principales centrados -->
    <div class="mb-4 d-flex justify-content-center gap-2">
        <button class="btn-hover btn btn-secondary" id="btn-usuarios">Usuarios</button>
        <button class="btn-hover btn btn-secondary" id="btn-pedidos">Pedidos</button>
        <button class="btn-hover btn btn-secondary" id="btn-productos">Productos</button>
    </div>

    <!-- Contenedor dinámico -->
    <div id="admin-content"></div>
</div>

<script>
$(document).ready(function() {
  let currentDataTable = null;

  // Configuración DataTables para alinear length/info a la izquierda y filter/paginación a la derecha
  // y DESHABILITAR el control de filas plegables:
  const dtConfig = {
    responsive: {
      details: false // Desactiva el icono "+"
    },
    dom:
      "<'row'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'f>>" +
      "rt" +
      "<'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-end'p>>"
  };

  function createModal(modalId, title, bodyContent, footerButtons, isForm = false) {
    $('.modal-backdrop').remove();
    const formStart = isForm ? '<form id="form-' + modalId + '">' : '';
    const formEnd = isForm ? '</form>' : '';
    const modalHtml = `
      <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            ${formStart}
              <div class="modal-header">
                <h5 class="modal-title" id="${modalId}Label">${title}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                ${bodyContent}
              </div>
              <div class="modal-footer">
                ${footerButtons}
              </div>
            ${formEnd}
          </div>
        </div>
      </div>`;
    $('body').append(modalHtml);

    const modal = new bootstrap.Modal(document.getElementById(modalId), { backdrop: false });
    $(`#${modalId}`).on('hidden.bs.modal', function() {
      $(this).remove();
      $('.modal-backdrop').remove();
    });
    return modal;
  }

  // ==========================
  //         USUARIOS
  // ==========================
  function loadUsuarios() {
    if (currentDataTable) {
      currentDataTable.destroy();
      $('#admin-content').empty();
    }

    let cardHtml = `
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

    $('#admin-content').html(cardHtml);

    currentDataTable = $('#tabla-usuarios').DataTable({
      ...dtConfig,
      ajax: {
        url: 'index.php?controller=admin&action=getUsuariosJSON',
        type: 'GET',
        dataSrc: ''
      },
      columns: [
        { data: 'id_usuario' },
        { data: 'nombre_completo' },
        { data: 'email' },
        { data: 'rol' },
        { data: 'telefono' },
        {
          data: null,
          render: function(data, type, row) {
            return `
              <button class="btn btn-warning btn-sm btn-editar-usuario" data-id="${row.id_usuario}">Editar</button>
              <button class="btn btn-danger btn-sm btn-borrar-usuario" data-id="${row.id_usuario}" data-nombre="${row.nombre_completo}">Borrar</button>
            `;
          }
        }
      ]
    });

    // Botón Crear Usuario
    $('#btn-crear-usuario').click(function() {
      const modal = createModal(
        'modalCrearUsuario',
        'Crear Usuario',
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
        true // Indica que este modal contiene un formulario
      );
      modal.show();

      // Manejar el envío del formulario de creación
      $('#form-modalCrearUsuario').off('submit').on('submit', function (e) {
        e.preventDefault();

        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();

        // Validar que las contraseñas coincidan
        if (password !== confirmPassword) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Las contraseñas no coinciden.'
          });
          return;
        }

        // Deshabilitar el botón de guardar para prevenir múltiples clics
        const $submitButton = $('#save-user');
        $submitButton.prop('disabled', true).text('Guardando...');

        // Solicitud AJAX para crear el usuario
        $.ajax({
            url: 'index.php?controller=admin&action=createUsuario',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
              if (response.status === 'ok') {
                  // Mostrar un mensaje de éxito con SweetAlert2
                  Swal.fire({
                      icon: 'success',
                      title: '¡Éxito!',
                      text: response.message,
                      timer: 1500,
                      showConfirmButton: false
                  }).then(() => {
                      modal.hide();
                      currentDataTable.ajax.reload(null, false);
                  });
              } else if (response.status === 'error') {
                  if (response.errors) {
                      const errorMessages = Object.values(response.errors).join('<br>');
                      Swal.fire({
                          icon: 'error',
                          title: 'Errores de Validación',
                          html: errorMessages
                      });
                  } else {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: response.message
                      });
                  }
                  $submitButton.prop('disabled', false).text('Guardar');
              }
            },
            error: function (xhr, status, error) {
              console.error('Error AJAX:', status, error);
              console.error('Respuesta del servidor:', xhr.responseText);
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Ocurrió un error inesperado.'
              });
              $submitButton.prop('disabled', false).text('Guardar');
          }
        });
      });
    });


    // Delegación de eventos para botones de editar usuario
    $('#tabla-usuarios tbody').on('click', '.btn-editar-usuario', function() {
      const userId = $(this).data('id');
      const rowData = currentDataTable.row($(this).parents('tr')).data();

      const modal = createModal(
        'modalEditarUsuario',
        'Editar Usuario',
        `
          <input type="hidden" id="id_usuario" name="id_usuario" value="${rowData.id_usuario}">
          <div class="mb-3">
            <label for="nombre_editar" class="form-label">Nombre Completo</label>
            <input type="text" class="form-control" id="nombre_editar" name="nombre_completo" value="${rowData.nombre_completo}" required>
          </div>
          <div class="mb-3">
            <label for="email_editar" class="form-label">Email</label>
            <input type="email" class="form-control" id="email_editar" name="email" value="${rowData.email}" required>
          </div>
          <div class="mb-3">
            <label for="rol_editar" class="form-label">Rol</label>
            <select class="form-select" id="rol_editar" name="rol" required>
              <option value="admin" ${rowData.rol === 'admin' ? 'selected' : ''}>Admin</option>
              <option value="usuario" ${rowData.rol === 'usuario' ? 'selected' : ''}>Usuario</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="telefono_editar" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono_editar" name="telefono" value="${rowData.telefono}">
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
        true // Indica que este modal contiene un formulario
      );
      modal.show();

      // Manejar el envío del formulario de edición
      $('#form-modalEditarUsuario').on('submit', function(e) {
        e.preventDefault();

        // Validar que las contraseñas coincidan si se han ingresado
        const password = $('#password_editar').val();
        const confirmPassword = $('#confirm_password_editar').val();
        if (password !== confirmPassword) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Las contraseñas no coinciden.'
          });
          return;
        }

        // Deshabilitar el botón de actualizar para prevenir múltiples envíos
        const $submitButton = $(this).find('button[type="submit"]');
        $submitButton.prop('disabled', true).text('Actualizando...');

        // Enviar los datos al servidor vía AJAX
        $.ajax({
          url: 'index.php?controller=admin&action=updateUsuario',
          type: 'POST',
          data: $(this).serialize(),
          dataType: 'json',
          success: function(response) {
            if (response.status === 'ok') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    modal.hide();
                    currentDataTable.ajax.reload(null, false); // Recargar sin reiniciar la paginación
                });
            } else {
                if (response.errors) {
                    const errorMessages = Object.values(response.errors).join('<br>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de Validación',
                        html: errorMessages
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
                $submitButton.prop('disabled', false).text('Actualizar');
            }
          },
          error: function(xhr, status, error) {
              console.error('Error AJAX:', status, error);
              console.error('Respuesta del servidor:', xhr.responseText);
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Ocurrió un error al procesar la solicitud. Inténtalo de nuevo.'
              });
              $submitButton.prop('disabled', false).text('Actualizar');
          }
        });
      });
    });

    // Delegación de eventos para botones de borrar usuario
    $('#tabla-usuarios tbody').on('click', '.btn-borrar-usuario', function() {
      const userId = $(this).data('id');
      const userName = $(this).data('nombre');

      Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas borrar al usuario "${userName}" (ID: ${userId})? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          // Enviar solicitud AJAX para borrar el usuario
          $.ajax({
            url: 'index.php?controller=admin&action=deleteUsuario',
            type: 'POST',
            data: { id_usuario: userId },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'ok') {
                Swal.fire({
                  icon: 'success',
                  title: '¡Borrado!',
                  text: response.message,
                  timer: 1500,
                  showConfirmButton: false
                });
                currentDataTable.ajax.reload();
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: response.message
                });
              }
            },
            error: function(error) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al borrar el usuario. Inténtalo de nuevo.'
              });
              console.error('Error AJAX:', error);
            }
          });
        }
      });
    });
  }

  // ==========================
  //         PEDIDOS
  // ==========================
  function loadPedidos() {
    if (currentDataTable) {
      currentDataTable.destroy();
      $('#admin-content').empty();
    }

    let cardHtml = `
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

    $('#admin-content').html(cardHtml);

    currentDataTable = $('#tabla-pedidos').DataTable({
      ...dtConfig,
      ajax: {
        url: 'index.php?controller=admin&action=getPedidosJSON',
        type: 'GET',
        dataSrc: ''
      },
      columns: [
        { data: 'id_pedido' },
        { data: 'id_usuario' },
        { data: 'fecha_pedido' },
        { data: 'total' },
        { data: 'direccion' },
        {
          data: null,
          render: function(data, type, row) {
            return `
              <button class="btn btn-warning btn-sm btn-editar-pedido" data-id="${row.id_pedido}">Editar</button>
              <button class="btn btn-danger btn-sm btn-borrar-pedido" data-id="${row.id_pedido}">Borrar</button>
            `;
          }
        }
      ]
    });

    // Botón Crear Pedido
    $('#btn-crear-pedido').click(function() {
      const modal = createModal(
        'modalCrearPedido',
        'Crear Pedido',
        `
          <div class="mb-3">
            <label for="id_usuario" class="form-label">ID Usuario</label>
            <input type="number" class="form-control" id="id_usuario" name="id_usuario" required>
          </div>
          <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" required>
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
            <label for="metodo_pago" class="form-label">Método de Pago</label>
            <input type="text" class="form-control" id="metodo_pago" name="metodo_pago">
          </div>
        `,
        `
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        `,
        true // Indica que este modal contiene un formulario
      );
      modal.show();

      // Manejar el envío del formulario de creación
      $('#form-modalCrearPedido').on('submit', function(e) {
        e.preventDefault();
        // Aquí puedes agregar la lógica para enviar los datos al servidor vía AJAX
        // Por ejemplo:
        /*
        $.ajax({
          url: 'index.php?controller=admin&action=crearPedido',
          type: 'POST',
          data: $(this).serialize(),
          success: function(response) {
            // Manejar la respuesta del servidor
            modal.hide();
            currentDataTable.ajax.reload();
          },
          error: function(error) {
            // Manejar errores
          }
        });
        */
        // Por ahora, simplemente cerramos el modal
        modal.hide();
        currentDataTable.ajax.reload();
      });
    });
  }

  // ==========================
  //        PRODUCTOS
  // ==========================
  function loadProductos() {
    if (currentDataTable) {
      currentDataTable.destroy();
      $('#admin-content').empty();
    }

    let cardHtml = `
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Gestión de Productos</h4>
          <button class="btn btn-success btn-sm" id="btn-crear-producto">
            <i class="bi bi-plus-circle"></i> Crear Producto
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <!-- Aquí tenemos la columna Acciones en el thead -->
            <table class="table table-striped table-bordered nowrap" style="width:100%" id="tabla-productos">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Precio Base</th>
                  <th>Tipo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>`;

    $('#admin-content').html(cardHtml);

    currentDataTable = $('#tabla-productos').DataTable({
      ...dtConfig,
      ajax: {
        url: 'index.php?controller=admin&action=getProductosJSON',
        type: 'GET',
        dataSrc: ''
      },
      columns: [
        { data: 'id_producto' },
        { data: 'nombre' },
        { data: 'descripcion' },
        { data: 'precio_base' },
        { data: 'tipo' },
        {
          data: null,
          className: 'all',
          render: function(data, type, row) {
            return `
              <button class="btn btn-warning btn-sm btn-editar-producto" data-id="${row.id_producto}">Editar</button>
              <button class="btn btn-danger btn-sm btn-borrar-producto" data-id="${row.id_producto}">Borrar</button>
            `;
          }
        }
      ]
    });

    // Botón Crear Producto
    $('#btn-crear-producto').click(function() {
      const modal = createModal(
        'modalCrearProducto',
        'Crear Producto',
        `
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion">
          </div>
          <div class="mb-3">
            <label for="precio_base" class="form-label">Precio Base</label>
            <input type="number" step="0.01" class="form-control" id="precio_base" name="precio_base" required>
          </div>
          <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <input type="text" class="form-control" id="tipo" name="tipo">
          </div>
        `,
        `
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        `,
        true // Indica que este modal contiene un formulario
      );
      modal.show();

      // Manejar el envío del formulario de creación
      $('#form-modalCrearProducto').on('submit', function(e) {
        e.preventDefault();
        // Aquí puedes agregar la lógica para enviar los datos al servidor vía AJAX
        // Por ejemplo:
        /*
        $.ajax({
          url: 'index.php?controller=admin&action=crearProducto',
          type: 'POST',
          data: $(this).serialize(),
          success: function(response) {
            // Manejar la respuesta del servidor
            modal.hide();
            currentDataTable.ajax.reload();
          },
          error: function(error) {
            // Manejar errores
          }
        });
        */
        // Por ahora, simplemente cerramos el modal
        modal.hide();
        currentDataTable.ajax.reload();
      });
    });
  }

  // Listeners para los menús
  $('#btn-usuarios').click(loadUsuarios);
  $('#btn-pedidos').click(loadPedidos);
  $('#btn-productos').click(loadProductos);

  // Cargar la sección de usuarios por defecto al cargar la página
  loadUsuarios();
});
</script>

<?php
    include_once "views/Footer.php";
?>
