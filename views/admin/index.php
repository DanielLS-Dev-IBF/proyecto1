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
      <form id="form-modalCrearPedido">
        <div class="mb-3">
          <label for="id_usuario" class="form-label">ID Usuario <span class="text-danger">*</span></label>
          <input type="number" class="form-control" id="id_usuario" name="id_usuario" required>
        </div>
        <div class="mb-3">
          <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
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
              <th>Precio Unitario ($)</th>
              <th>Cantidad</th>
              <th>Total ($)</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Filas dinámicas de productos -->
          </tbody>
        </table>
        <div class="text-end">
          <h5>Total Pedido: $<span id="total_pedido">0.00</span></h5>
        </div>
      </form>
    `,
    `
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      <button type="submit" class="btn btn-primary">Guardar</button>
    `,
    true // Indica que este modal contiene un formulario
  );
  modal.show();

  // Manejar la lógica de agregar productos
  $('#form-modalCrearPedido').on('click', '#btn-agregar-producto-pedido', function() {
    // Obtener la lista de productos disponibles via AJAX o tenerlos precargados
    $.ajax({
      url: 'index.php?controller=admin&action=getProductosJSON',
      type: 'GET',
      dataType: 'json',
      success: function(productos) {
        let options = '<option value="">Selecciona un producto</option>';
        productos.forEach(producto => {
          options += `<option value="${producto.id_producto}" data-precio="${producto.precio_base}">${producto.nombre}</option>`;
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
        $('#tabla-productos-pedido tbody').append(fila);
      },
      error: function(error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo cargar la lista de productos.'
        });
      }
    });
  });

  // Delegación de eventos para cambios en la selección de productos y cantidades
  $('#form-modalCrearPedido').on('change', '.producto-select', function() {
    const precio = parseFloat($(this).find('option:selected').data('precio')) || 0;
    const fila = $(this).closest('tr');
    fila.find('.precio-unitario').val(precio.toFixed(2));
    const cantidad = parseInt(fila.find('.cantidad').val()) || 0;
    const total = precio * cantidad;
    fila.find('.total-producto').val(total.toFixed(2));
    actualizarTotalPedido();
  });

  $('#form-modalCrearPedido').on('input', '.cantidad', function() {
    const fila = $(this).closest('tr');
    const precio = parseFloat(fila.find('.precio-unitario').val()) || 0;
    const cantidad = parseInt($(this).find('.cantidad').val()) || 0;
    const total = precio * cantidad;
    fila.find('.total-producto').val(total.toFixed(2));
    actualizarTotalPedido();
  });

  // Delegación de eventos para eliminar productos
  $('#form-modalCrearPedido').on('click', '.btn-eliminar-producto', function() {
    $(this).closest('tr').remove();
    actualizarTotalPedido();
  });

  // Función para actualizar el total del pedido
  function actualizarTotalPedido() {
    let total = 0;
    $('#tabla-productos-pedido tbody tr').each(function() {
      const totalProducto = parseFloat($(this).find('.total-producto').val()) || 0;
      total += totalProducto;
    });
    $('#total_pedido').text(total.toFixed(2));
  }

  // Manejar el envío del formulario de creación
  $('#form-modalCrearPedido').on('submit', function(e) {
    e.preventDefault();

    // Validaciones adicionales
    const id_usuario = $('#id_usuario').val().trim();
    const direccion = $('#direccion').val().trim();
    const metodo_pago = $('#metodo_pago').val();
    const total_pedido = parseFloat($('#total_pedido').text()) || 0;

    if (!id_usuario) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'El ID de usuario es obligatorio.'
      });
      return;
    }

    if (!direccion) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'La dirección es obligatoria.'
      });
      return;
    }

    if (!metodo_pago) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Debes seleccionar un método de pago.'
      });
      return;
    }

    if ($('#tabla-productos-pedido tbody tr').length === 0) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Debes agregar al menos un producto al pedido.'
      });
      return;
    }

    // Construir la lista de productos
    let productos = [];
    let valid = true;
    $('#tabla-productos-pedido tbody tr').each(function(index) {
      const id_producto = $(this).find('.producto-select').val();
      const nombre_producto = $(this).find('.producto-select option:selected').text();
      const precio_unitario = parseFloat($(this).find('.precio-unitario').val()) || 0;
      const cantidad = parseInt($(this).find('.cantidad').val()) || 0;
      const total_producto = parseFloat($(this).find('.total-producto').val()) || 0;

      if (!id_producto) {
        valid = false;
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: `El producto en la fila ${index + 1} no está seleccionado.`
        });
        return false; // Break loop
      }

      if (cantidad <= 0) {
        valid = false;
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: `La cantidad en la fila ${index + 1} debe ser mayor a cero.`
        });
        return false; // Break loop
      }

      productos.push({
        id_producto,
        nombre_producto,
        precio_unitario,
        cantidad,
        total_producto
      });
    });

    if (!valid) return;

    // Deshabilitar el botón de guardar para prevenir múltiples clics
    const $submitButton = $(this).find('button[type="submit"]');
    $submitButton.prop('disabled', true).text('Guardando...');

    // Construir el objeto de pedido
    const pedidoData = {
      id_usuario,
      direccion,
      telefono: $('#telefono').val().trim(),
      correo: $('#correo').val().trim(),
      metodo_pago,
      productos
    };

    // Solicitud AJAX para crear el pedido
    $.ajax({
      url: 'index.php?controller=admin&action=createPedido',
      type: 'POST',
      data: JSON.stringify(pedidoData),
      contentType: 'application/json',
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
      error: function(xhr, status, error) {
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

    // Delegación de eventos para botones de editar pedido
    $('#tabla-pedidos tbody').on('click', '.btn-editar-pedido', function() {
      const pedidoId = $(this).data('id');

      // Obtener los datos del pedido via AJAX para prellenar el formulario
      $.ajax({
        url: 'index.php?controller=admin&action=getPedidoDetallesJSON&id_pedido=' + pedidoId,
        type: 'GET',
        dataType: 'json',
        success: function(pedido) {
          if (!pedido) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Pedido no encontrado.'
            });
            return;
          }

          const modal = createModal(
            'modalEditarPedido',
            'Editar Pedido',
            `
              <input type="hidden" id="id_pedido" name="id_pedido" value="${pedido.id_pedido}">
              <div class="mb-3">
                <label for="id_usuario_editar" class="form-label">ID Usuario <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="id_usuario_editar" name="id_usuario" value="${pedido.id_usuario}" required>
              </div>
              <div class="mb-3">
                <label for="direccion_editar" class="form-label">Dirección <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="direccion_editar" name="direccion" value="${pedido.direccion}" required>
              </div>
              <div class="mb-3">
                <label for="telefono_editar" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono_editar" name="telefono" value="${pedido.telefono}">
              </div>
              <div class="mb-3">
                <label for="correo_editar" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo_editar" name="correo" value="${pedido.correo}">
              </div>
              <div class="mb-3">
                <label for="metodo_pago_editar" class="form-label">Método de Pago</label>
                <input type="text" class="form-control" id="metodo_pago_editar" name="metodo_pago" value="${pedido.metodo_pago}">
              </div>
              <hr>
              <h5>Productos</h5>
              <button type="button" class="btn btn-primary btn-sm mb-2" id="btn-agregar-producto-pedido-editar">Agregar Producto</button>
              <table class="table table-bordered" id="tabla-productos-pedido-editar">
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
                <h5>Total Pedido: $<span id="total_pedido_editar">0.00</span></h5>
              </div>
            `,
            `
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Actualizar</button>
            `,
            true // Indica que este modal contiene un formulario
          );
          modal.show();

          // Población de productos existentes
          pedido.productos.forEach(producto => {
            const fila = `
              <tr>
                <td>
                  <select class="form-select producto-select" required>
                    <option value="">Selecciona un producto</option>
                    ${pedido.productosDisponibles.map(p => `
                      <option value="${p.id_producto}" data-precio="${p.precio_base}" ${p.id_producto == producto.id_producto ? 'selected' : ''}>
                        ${p.nombre}
                      </option>
                    `).join('')}
                  </select>
                </td>
                <td>
                  <input type="number" class="form-control precio-unitario" readonly value="${producto.precio_unitario.toFixed(2)}">
                </td>
                <td>
                  <input type="number" class="form-control cantidad" min="1" value="${producto.cantidad}" required>
                </td>
                <td>
                  <input type="number" class="form-control total-producto" readonly value="${producto.total_producto.toFixed(2)}">
                </td>
                <td>
                  <button type="button" class="btn btn-danger btn-sm btn-eliminar-producto">Eliminar</button>
                </td>
              </tr>
            `;
            $('#tabla-productos-pedido-editar tbody').append(fila);
          });

          actualizarTotalPedidoEditar();

          // Manejar la lógica de agregar productos
          $('#form-modalEditarPedido').on('click', '#btn-agregar-producto-pedido-editar', function() {
            // Obtener la lista de productos disponibles via AJAX o tenerlos precargados
            $.ajax({
              url: 'index.php?controller=admin&action=getProductosJSON',
              type: 'GET',
              dataType: 'json',
              success: function(productos) {
                let options = '<option value="">Selecciona un producto</option>';
                productos.forEach(producto => {
                  options += `<option value="${producto.id_producto}" data-precio="${producto.precio_base}">${producto.nombre}</option>`;
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
                $('#tabla-productos-pedido-editar tbody').append(fila);
              },
              error: function(error) {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'No se pudo cargar la lista de productos.'
                });
              }
            });
          });

          // Delegación de eventos para cambios en la selección de productos y cantidades
          $('#admin-content').on('change', '.producto-select', function() {
            const precio = $(this).find('option:selected').data('precio') || 0;
            const fila = $(this).closest('tr');
            fila.find('.precio-unitario').val(precio.toFixed(2));
            const cantidad = parseInt(fila.find('.cantidad').val()) || 0;
            const total = precio * cantidad;
            fila.find('.total-producto').val(total.toFixed(2));
            actualizarTotalPedidoEditar();
          });

          $('#admin-content').on('input', '.cantidad', function() {
            const fila = $(this).closest('tr');
            const precio = parseFloat(fila.find('.precio-unitario').val()) || 0;
            const cantidad = parseInt($(this).find('.cantidad').val()) || 0;
            const total = precio * cantidad;
            fila.find('.total-producto').val(total.toFixed(2));
            actualizarTotalPedidoEditar();
          });

          // Delegación de eventos para eliminar productos
          $('#admin-content').on('click', '.btn-eliminar-producto', function() {
            $(this).closest('tr').remove();
            actualizarTotalPedidoEditar();
          });

          // Función para actualizar el total del pedido
          function actualizarTotalPedidoEditar() {
            let total = 0;
            $('#tabla-productos-pedido-editar tbody tr').each(function() {
              const totalProducto = parseFloat($(this).find('.total-producto').val()) || 0;
              total += totalProducto;
            });
            $('#total_pedido_editar').text(total.toFixed(2));
          }

          // Manejar el envío del formulario de edición
          $('#form-modalEditarPedido').on('submit', function(e) {
            e.preventDefault();

            // Validaciones adicionales
            const id_pedido = $('#id_pedido').val().trim();
            const id_usuario = $('#id_usuario_editar').val().trim();
            const direccion = $('#direccion_editar').val().trim();
            const total_pedido = parseFloat($('#total_pedido_editar').text()) || 0;

            if (!id_pedido) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ID del pedido no válido.'
              });
              return;
            }

            if (!id_usuario) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El ID de usuario es obligatorio.'
              });
              return;
            }

            if (!direccion) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La dirección es obligatoria.'
              });
              return;
            }

            if ($('#tabla-productos-pedido-editar tbody tr').length === 0) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes agregar al menos un producto al pedido.'
              });
              return;
            }

            // Construir la lista de productos
            let productos = [];
            let valid = true;
            $('#tabla-productos-pedido-editar tbody tr').each(function() {
              const id_producto = $(this).find('.producto-select').val();
              const nombre_producto = $(this).find('.producto-select option:selected').text();
              const precio_unitario = parseFloat($(this).find('.precio-unitario').val()) || 0;
              const cantidad = parseInt($(this).find('.cantidad').val()) || 0;
              const total_producto = parseFloat($(this).find('.total-producto').val()) || 0;

              if (!id_producto) {
                valid = false;
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Todos los productos deben estar seleccionados.'
                });
                return false; // Break loop
              }

              if (cantidad <= 0) {
                valid = false;
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Las cantidades deben ser mayores a cero.'
                });
                return false; // Break loop
              }

              productos.push({
                id_producto,
                nombre_producto,
                precio_unitario,
                cantidad,
                total_producto
              });
            });

            if (!valid) return;

            // Deshabilitar el botón de actualizar para prevenir múltiples envíos
            const $submitButton = $(this).find('button[type="submit"]');
            $submitButton.prop('disabled', true).text('Actualizando...');

            // Construir el objeto de pedido
            const pedidoData = {
              id_pedido,
              id_usuario,
              direccion,
              telefono: $('#telefono_editar').val().trim(),
              correo: $('#correo_editar').val().trim(),
              metodo_pago: $('#metodo_pago_editar').val().trim(),
              productos
            };

            // Solicitud AJAX para actualizar el pedido
            $.ajax({
              url: 'index.php?controller=admin&action=updatePedido',
              type: 'POST',
              data: JSON.stringify(pedidoData),
              contentType: 'application/json',
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
                  $submitButton.prop('disabled', false).text('Actualizar');
                }
              },
              error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Ocurrió un error inesperado.'
                });
                $submitButton.prop('disabled', false).text('Actualizar');
              }
            });
          });
        },
        error: function(error) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo obtener los detalles del pedido.'
          });
        }
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

  $('#admin-content').html(cardHtml);

  currentDataTable = $('#tabla-productos').DataTable({
    ...dtConfig,
    responsive: true, // Activar la funcionalidad responsiva
    ajax: {
      url: 'index.php?controller=admin&action=getProductosJSON',
      type: 'GET',
      dataSrc: ''
    },
    columns: [
      { 
        data: 'id_producto',
        responsivePriority: 1,
        width: '5%'
      },
      { 
        data: 'nombre',
        responsivePriority: 2,
        width: '15%'
      },
      { 
        data: 'descripcion',
        responsivePriority: 3, // Baja prioridad
        width: '30%',
        render: function(data, type, row) {
          const maxLength = 100;
          if (data.length > maxLength) {
            return data.substr(0, maxLength) + '...';
          }
          return data;
        }
      },
      { 
        data: 'precio_base',
        responsivePriority: 2,
        width: '10%',
      },
      { 
        data: 'tipo',
        responsivePriority: 2,
        width: '10%'
      },
      { 
        data: 'img',
        responsivePriority: 1,
        width: '10%',
        render: function(data, type, row) {
          if (data) {
            return `<img src="${data}" alt="Imagen" class="img-thumbnail" width="50">`;
          } else {
            return 'No disponible';
          }
        },
        orderable: false,
        searchable: false
      },
      {
        data: null,
        responsivePriority: 1,
        className: 'all',
        width: '15%',
        render: function(data, type, row) {
          return `
            <button class="btn btn-warning btn-sm btn-editar-producto" data-id="${row.id_producto}">Editar</button>
            <button class="btn btn-danger btn-sm btn-borrar-producto" data-id="${row.id_producto}">Borrar</button>
          `;
        },
        orderable: false,
        searchable: false
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
      true // Indica que este modal contiene un formulario
    );
    modal.show();

    // Manejar el envío del formulario de creación
    $('#form-modalCrearProducto').off('submit').on('submit', function(e) {
      e.preventDefault();

      // Validaciones adicionales
      const nombre = $('#nombre').val().trim();
      const precio_base = $('#precio_base').val();
      const tipo = $('#tipo').val();

      if (!nombre) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'El nombre del producto es obligatorio.'
        });
        return;
      }

      if (!precio_base || isNaN(precio_base) || precio_base <= 0) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'El precio base debe ser un número positivo.'
        });
        return;
      }

      if (!tipo) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Debes seleccionar un tipo de producto.'
        });
        return;
      }

      // Deshabilitar el botón de guardar para prevenir múltiples clics
      const $submitButton = $('#save-producto');
      $submitButton.prop('disabled', true).text('Guardando...');

      // Enviar los datos al servidor vía AJAX serializados
      $.ajax({
        url: 'index.php?controller=admin&action=createProducto',
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
        error: function(xhr, status, error) {
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

  // Delegación de eventos para botones de editar producto
  $('#tabla-productos tbody').on('click', '.btn-editar-producto', function() {
    const productoId = $(this).data('id');
    
    // Obtener los datos del producto desde la tabla
    const rowData = currentDataTable.row($(this).parents('tr')).data();

    const modal = createModal(
      'modalEditarProducto',
      'Editar Producto',
      `
        <input type="hidden" id="id_producto" name="id_producto" value="${rowData.id_producto}">
        <div class="mb-3">
          <label for="nombre_editar" class="form-label">Nombre <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="nombre_editar" name="nombre" value="${rowData.nombre}" required>
        </div>
        <div class="mb-3">
          <label for="descripcion_editar" class="form-label">Descripción</label>
          <input type="text" class="form-control" id="descripcion_editar" name="descripcion" value="${rowData.descripcion}">
        </div>
        <div class="mb-3">
          <label for="precio_base_editar" class="form-label">Precio Base <span class="text-danger">*</span></label>
          <input type="number" step="0.01" class="form-control" id="precio_base_editar" name="precio_base" value="${rowData.precio_base}" required>
        </div>
        <div class="mb-3">
          <label for="tipo_editar" class="form-label">Tipo <span class="text-danger">*</span></label>
          <select class="form-select" id="tipo_editar" name="tipo" required>
            <option value="">Selecciona un tipo</option>
            <option value="Bowl" ${rowData.tipo === 'Bowl' ? 'selected' : ''}>Bowl</option>
            <option value="Postre" ${rowData.tipo === 'Postre' ? 'selected' : ''}>Postre</option>
            <option value="Bebida" ${rowData.tipo === 'Bebida' ? 'selected' : ''}>Bebida</option>
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
      true // Indica que este modal contiene un formulario
    );
    modal.show();

    // Manejar el envío del formulario de edición
    $('#form-modalEditarProducto').off('submit').on('submit', function(e) {
      e.preventDefault();

      const nombre = $('#nombre_editar').val().trim();
      const precio_base = $('#precio_base_editar').val();
      const tipo = $('#tipo_editar').val();

      if (!nombre) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'El nombre del producto es obligatorio.'
        });
        return;
      }

      if (!precio_base || isNaN(precio_base) || precio_base <= 0) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'El precio base debe ser un número positivo.'
        });
        return;
      }

      if (!tipo) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Debes seleccionar un tipo de producto.'
        });
        return;
      }

      // Deshabilitar el botón de actualizar para prevenir múltiples envíos
      const $submitButton = $('#update-producto');
      $submitButton.prop('disabled', true).text('Actualizando...');

      // Enviar los datos al servidor vía AJAX serializados
      $.ajax({
        url: 'index.php?controller=admin&action=updateProducto',
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
            $submitButton.prop('disabled', false).text('Actualizar');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error AJAX:', status, error);
          console.error('Respuesta del servidor:', xhr.responseText);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error inesperado.'
          });
          $submitButton.prop('disabled', false).text('Actualizar');
        }
      });
    });
  });

  // Delegación de eventos para botones de borrar producto
  $('#tabla-productos tbody').on('click', '.btn-borrar-producto', function() {
    const productoId = $(this).data('id');

    Swal.fire({
      title: '¿Estás seguro?',
      text: `¿Deseas borrar el producto con ID: ${productoId}? Esta acción no se puede deshacer.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, borrar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Enviar solicitud AJAX para borrar el producto
        $.ajax({
          url: 'index.php?controller=admin&action=deleteProducto',
          type: 'POST',
          data: { id_producto: productoId },
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
              text: 'Hubo un error al borrar el producto. Inténtalo de nuevo.'
            });
            console.error('Error AJAX:', error);
          }
        });
      }
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
