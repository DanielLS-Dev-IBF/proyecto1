<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Greeny: Inicio</title>
    
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
    <!-- Enlace a Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/Greeny.css">
    
    <!-- Incluir jQuery desde CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
        // Asegurarse de que la sesión está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        // Incluye la vista específica
        if (isset($view)) {
            include_once($view);
        } else {
            echo "Vista no encontrada.";
        }

        // Puedes incluir el footer aquí si lo deseas
        // include_once "Footer.php"; 
    ?>
    
    <!-- Scripts de Bootstrap JS (después de incluir jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html> 
