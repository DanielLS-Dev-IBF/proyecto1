<?php
// autoload.php

spl_autoload_register(function ($class_name) {
    // Define los directorios base para las clases
    $directories = [
        'controllers/',
        'models/',
        'config/',
        'views',
        // Añade más directorios según tu estructura
    ];

    // Itera sobre los directorios para buscar la clase
    foreach ($directories as $directory) {
        $file = __DIR__ . '/' . $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
