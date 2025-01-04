// assets/js/utils.js

/**
 * Función helper para realizar peticiones HTTP usando fetch.
 * @param {string} url - URL del endpoint.
 * @param {object} options - Opciones de configuración para fetch.
 * @returns {Promise<object>} - Respuesta en formato JSON.
 */
async function fetchRequest(url, options = {}) {
  try {
    const response = await fetch(url, options);

    // Verificar si la respuesta es exitosa (status en el rango 200-299)
    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(errorText || "Error en la petición");
    }

    // Asumimos que todas las respuestas son JSON
    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Fetch Error:", error);
    throw error;
  }
}
