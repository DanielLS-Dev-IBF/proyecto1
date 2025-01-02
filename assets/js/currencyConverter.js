// assets/js/currencyConverter.js

const CurrencyConverter = (function () {
  let currencyRates = {};
  let currencySymbols = {};

  // Función para obtener las tasas de cambio desde el apiController
  async function fetchCurrencyRates() {
    try {
      const response = await fetch(
        "index.php?controller=api&action=getCurrencyRates",
        {
          method: "GET",
          headers: {
            "X-Requested-With": "XMLHttpRequest", // Para que el controlador reconozca que es una solicitud AJAX
          },
        }
      );

      if (!response.ok) {
        throw new Error("Error al obtener las tasas de cambio");
      }

      const data = await response.json();

      if (data.status !== "ok") {
        throw new Error(data.message || "Error desconocido");
      }

      currencyRates = data.data; // { CAD: 1.487..., EUR: 1, USD: 1.035... }
      return currencyRates;
    } catch (error) {
      console.error("Error al obtener las tasas de cambio:", error);
      Swal.fire("Error", "No se pudo obtener las tasas de cambio", "error");
      return null;
    }
  }

  // Función para obtener la lista de monedas y sus símbolos
  async function fetchCurrenciesList() {
    try {
      const response = await fetch(
        "index.php?controller=api&action=getCurrenciesList",
        {
          method: "GET",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        }
      );

      if (!response.ok) {
        throw new Error("Error al obtener la lista de monedas");
      }

      const data = await response.json();

      if (data.status !== "ok") {
        throw new Error(data.message || "Error desconocido");
      }

      const currencies = data.data;

      // Construir el mapeo de símbolos
      currencies.forEach((currency) => {
        currencySymbols[currency.code] = currency.symbol;
      });

      return currencies;
    } catch (error) {
      console.error("Error al obtener la lista de monedas:", error);
      Swal.fire("Error", "No se pudo obtener la lista de monedas", "error");
      return null;
    }
  }

  // Función para actualizar los precios en elementos específicos
  function actualizarPrecios(selector, moneda) {
    // Verificar si las tasas de cambio están disponibles
    if (!currencyRates || Object.keys(currencyRates).length === 0) {
      Swal.fire("Error", "Las tasas de cambio no están disponibles", "error");
      return;
    }

    // Iterar sobre cada elemento seleccionado y actualizar el precio
    $(selector).each(function () {
      const precioBase = parseFloat($(this).data("eur"));
      let nuevoPrecio = precioBase;

      if (moneda !== "EUR") {
        const rate = currencyRates[moneda];
        if (!rate) {
          Swal.fire("Error", `No se encontró la tasa para ${moneda}`, "error");
          return;
        }
        nuevoPrecio = precioBase * rate;
      }

      // Obtener el símbolo de la moneda
      let simbolo = currencySymbols[moneda] || moneda + " ";

      // Formatear el precio con dos decimales y el símbolo de la moneda
      $(this).text(`${nuevoPrecio.toFixed(2)} ${simbolo}`);
    });
  }

  return {
    fetchCurrencyRates,
    fetchCurrenciesList,
    actualizarPrecios,
  };
})();
