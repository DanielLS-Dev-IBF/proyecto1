<!-- views/admin/index.php -->
<?php
    // Podrías incluir un topnav si quieres
    include_once "views/TopNav.php";
?>
<div class="container my-5">

<h2>Panel de Administración (Menú para distintas tablas)</h2>
<hr>

<!-- Menú de Botones (o tabs) -->
<div class="mb-4">
  <button class="btn btn-primary" id="btn-usuarios">Usuarios</button>
  <button class="btn btn-primary" id="btn-pedidos">Pedidos</button>
  <button class="btn btn-primary" id="btn-productos">Productos</button>
</div>

<!-- Contenedor donde inyectamos la tabla que DataTables manejará -->
<div id="admin-content"></div>

<script>
$(document).ready(function() {
  let currentDataTable = null; // Referencia a la DataTable activa

  // 1. Función para cargar Usuarios
  function loadUsuarios() {
    if (currentDataTable) {
      currentDataTable.destroy();
      $('#admin-content').empty();
    }

    // Insertar la card con la tabla
    let cardHtml = `
    <div class="card">
      <div class="card-header">
        <h4>Gestión de Usuarios</h4>
        <button class="btn btn-success btn-sm float-end" id="btn-crear-usuario">
          <i class="bi bi-plus-circle"></i> Crear Usuario
        </button>
      </div>
      <div class="card-body">
        <table class="table table-striped" id="tabla-usuarios">
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
    `;

    $('#admin-content').html(cardHtml);

    currentDataTable = $('#tabla-usuarios').DataTable({
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
              <button class="btn btn-danger btn-sm btn-borrar-usuario" data-id="${row.id_usuario}">Borrar</button>
            `;
          }
        }
      ]
    });

    // Manejar clicks (Borrar / Editar / Crear)
    $('#btn-crear-usuario').click(function() {
      // Abre modal de crear
      $('#modalCrearUsuario').modal('show');
    });

    $('#tabla-usuarios tbody').on('click', '.btn-borrar-usuario', function() {
      let id = $(this).data('id');
      if (confirm("¿Borrar usuario ID=" + id + "?")) {
        $.post('index.php?controller=admin&action=deleteUsuario', { id_usuario: id }, function(resp) {
          if (resp.status === 'ok') {
            currentDataTable.ajax.reload();
            alert(resp.message);
          } else {
            alert(resp.message);
          }
        }, 'json');
      }
    });

    // Similar para editar...
  }


  // 2. Función para cargar Pedidos
  function loadPedidos() {
    if (currentDataTable) {
      currentDataTable.destroy();
      $('#admin-content').empty();
    }

    let tableHtml = `
      <table class="table table-striped" id="tabla-pedidos">
        <thead>
          <tr>
            <th>ID Pedido</th>
            <th>ID Usuario</th>
            <th>Fecha Pedido</th>
            <th>Total</th>
            <th>Direccion</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
    `;
    $('#admin-content').html(tableHtml);

    currentDataTable = $('#tabla-pedidos').DataTable({
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
              <button class="btn btn-danger btn-sm btn-borrar-pedido" data-id="${row.id_pedido}">Borrar</button>
            `;
          }
        }
      ]
    });

    // Manejo click Borrar
    $('#tabla-pedidos tbody').on('click', '.btn-borrar-pedido', function() {
      let id = $(this).data('id');
      if (confirm("¿Borrar pedido ID=" + id + "?")) {
        $.post('index.php?controller=admin&action=deletePedido', { id_pedido: id }, function(resp) {
          if (resp.status === 'ok') {
            currentDataTable.ajax.reload();
            alert(resp.message);
          } else {
            alert(resp.message);
          }
        }, 'json');
      }
    });
  }

  // 3. Función para cargar Productos
  function loadProductos() {
    if (currentDataTable) {
      currentDataTable.destroy();
      $('#admin-content').empty();
    }

    let tableHtml = `
      <table class="table table-striped" id="tabla-productos">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Tipo</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
    `;
    $('#admin-content').html(tableHtml);

    currentDataTable = $('#tabla-productos').DataTable({
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
          render: function(data, type, row) {
            return `
              <button class="btn btn-warning btn-sm btn-editar-producto" data-id="${row.id_producto}">Editar</button>
              <button class="btn btn-danger btn-sm btn-borrar-producto" data-id="${row.id_producto}">Borrar</button>
            `;
          }
        }
      ]
    });

    // Manejo click Borrar
    $('#tabla-productos tbody').on('click', '.btn-borrar-producto', function() {
      let id = $(this).data('id');
      if (confirm("¿Borrar producto ID=" + id + "?")) {
        $.post('index.php?controller=admin&action=deleteProducto', { id_producto: id }, function(resp) {
          if (resp.status === 'ok') {
            currentDataTable.ajax.reload();
            alert(resp.message);
          } else {
            alert(resp.message);
          }
        }, 'json');
      }
    });
  }

  // Asignar eventos a los botones
  $('#btn-usuarios').click(loadUsuarios);
  $('#btn-pedidos').click(loadPedidos);
  $('#btn-productos').click(loadProductos);

  // Opcional: por defecto, cargar Usuarios
  // loadUsuarios();
});
</script>

</div>
<?php
    // Footer si es necesario
    include_once "views/Footer.php";
?>
