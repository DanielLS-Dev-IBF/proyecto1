<?php
// controllers/apiController.php

class apiController {
    
    // Método para obtener las tasas de cambio
    public function getCurrencyRates() {
        // Verificar si la solicitud es AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
            exit;
        }

        // Ruta del cache (puede ser un archivo o una base de datos)
        $cacheFile = __DIR__ . '/../cache/currencyRates.json';
        $cacheTime = 3600; // 1 hora en segundos

        // Verificar si el cache existe y no está expirado
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $rates = json_decode(file_get_contents($cacheFile), true);
        } else {
            // Obtener las tasas desde FreeCurrencyAPI
            $apiKey = 'fca_live_LxL6h02iHdHgyYarFMJqgF32uGa19ElC2IgkWiIb'; // Asegúrate de mantener esto seguro
            $currencies = 'EUR,USD,CAD'; // Añade más monedas si es necesario
            $url = "https://api.freecurrencyapi.com/v1/latest?apikey={$apiKey}&currencies=" . urlencode($currencies) . "&base_currency=EUR";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Tiempo de espera de 10 segundos

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode !== 200) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener tasas de cambio']);
                exit;
            }

            $data = json_decode($response, true);
            if (!isset($data['data'])) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Respuesta inválida de la API']);
                exit;
            }

            $rates = $data['data'];

            // Guardar las tasas en el cache
            if (!is_dir(__DIR__ . '/../cache')) {
                mkdir(__DIR__ . '/../cache', 0755, true);
            }
            file_put_contents($cacheFile, json_encode($rates));
        }

        // Retornar las tasas en formato JSON
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'data' => $rates]);
        exit;
    }
}
?>
