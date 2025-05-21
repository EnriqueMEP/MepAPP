<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEP-Projects | <?php echo isset($title) ? $title : 'Sistema de Gestión'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
 <link rel="stylesheet" href="<?= CSS_URL ?>styles.css?v=<?= APP_VERSION ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php echo $content; ?>
    
    <script>
        // Inicializar los iconos de Lucide
        lucide.createIcons();
    </script>
 <script src="<?= JS_URL ?>main.js?v=<?= APP_VERSION ?>"></script>
</body>
</html>