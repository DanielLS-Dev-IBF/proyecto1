
    <h1>Crear Producto</h1>

    <form action="?controller=producto&action=store" method="POST">

        <input type="text" name = "nombre" id = "nombre">
        <select name = "talla">
            <option value="XXL">XXL</option>
            <option value="XL">XL</option>
            <option value="L">L</option>
            <option value="M">M</option>
            <option value="S">S</option>
        </select>
        <input type="number" name="precio" id="precio">
        <button type="submit">Guardar</button>
    </form>