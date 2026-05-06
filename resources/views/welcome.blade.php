<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vistas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
    <script>
        <?php if(session('usuario')): ?>
        localStorage.setItem('user', JSON.stringify(<?php echo json_encode(session('usuario')); ?>));
        <?php endif; ?>
    </script>
</body>
</html>