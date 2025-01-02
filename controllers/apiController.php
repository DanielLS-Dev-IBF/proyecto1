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

        // Ruta del cache y tiempo de expiración
        $cacheFile = __DIR__ . '/../cache/currencyRates.json';
        $cacheTime = 3600; // 1 hora en segundos

        // Verificar si el cache existe y no está expirado
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $rates = json_decode(file_get_contents($cacheFile), true);
        } else {
            // Obtener las tasas desde FreeCurrencyAPI
            $apiKey = 'fca_live_LxL6h02iHdHgyYarFMJqgF32uGa19ElC2IgkWiIb'; // Mantén esto seguro
            $currencies = ''; // Dejar vacío para obtener todas las monedas
            $base_currency = 'EUR';
            $url = "https://api.freecurrencyapi.com/v1/latest?apikey={$apiKey}&currencies={$currencies}&base_currency={$base_currency}";

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

    // Método para obtener la lista de monedas y sus símbolos
    public function getCurrenciesList() {
        // Verificar si la solicitud es AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
            exit;
        }

        // Lista estática de símbolos de monedas
        $currencySymbols = [
            "EUR" => "€",
            "USD" => "$",
            "CAD" => "C$",
            "GBP" => "£",
            "JPY" => "¥",
            "AUD" => "A$",
            "CHF" => "CHF",
            "CNY" => "¥",
            "SEK" => "kr",
            "NZD" => "NZ$",
            // Agrega más símbolos según sea necesario
        ];

        // Obtener las tasas de cambio para obtener las monedas disponibles
        $currencyRates = $this->getAllCurrencyRates(); // Método auxiliar para obtener tasas sin salida

        if (!$currencyRates) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'No se pudo obtener las tasas de cambio']);
            exit;
        }

        // Generar la lista de monedas con símbolos
        $currenciesList = [];
        foreach ($currencyRates as $code => $rate) {
            $symbol = isset($currencySymbols[$code]) ? $currencySymbols[$code] : $code . " ";
            $currenciesList[] = [
                'code' => $code,
                'symbol' => $symbol
            ];
        }

        // Retornar la lista de monedas
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'data' => $currenciesList]);
        exit;
    }

    // Método auxiliar para obtener tasas sin salida
    private function getAllCurrencyRates() {
        // Ruta del cache y tiempo de expiración
        $cacheFile = __DIR__ . '/../cache/currencyRates.json';
        $cacheTime = 3600; // 1 hora en segundos

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $rates = json_decode(file_get_contents($cacheFile), true);
            return $rates;
        } else {
            // Obtener las tasas desde FreeCurrencyAPI
            $apiKey = 'fca_live_LxL6h02iHdHgyYarFMJqgF32uGa19ElC2IgkWiIb'; // Mantén esto seguro
            $currencies = ''; // Dejar vacío para obtener todas las monedas
            $base_currency = 'EUR';
            $url = "https://api.freecurrencyapi.com/v1/latest?apikey={$apiKey}&currencies={$currencies}&base_currency={$base_currency}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Tiempo de espera de 10 segundos

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode !== 200) {
                return false;
            }

            $data = json_decode($response, true);
            if (!isset($data['data'])) {
                return false;
            }

            $rates = $data['data'];

            // Guardar las tasas en el cache
            if (!is_dir(__DIR__ . '/../cache')) {
                mkdir(__DIR__ . '/../cache', 0755, true);
            }
            file_put_contents($cacheFile, json_encode($rates));

            return $rates;
        }
    }
}
?>
