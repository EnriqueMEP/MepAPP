<?php
// config/app.php

// -------------------------------------------------
// Entorno de la aplicación: 'development' o 'production'
// -------------------------------------------------
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// -------------------------------------------------
// Nombre y versión de la aplicación
// -------------------------------------------------
define('APP_NAME', 'MEP-Projects');
define('APP_VERSION', '1.0.0');

// -------------------------------------------------
// Ruta base de la aplicación (con slash inicial y final)
// Por ejemplo, si accedes en local con
// http://localhost/mep-projects/  → BASE_URL = '/mep-projects/'
// -------------------------------------------------
define('BASE_URL', '/mep-projects/');

// -------------------------------------------------
// URLs de assets (CSS, JS, imágenes)
// -------------------------------------------------
define('CSS_URL', BASE_URL . 'assets/css/');
define('JS_URL',  BASE_URL . 'assets/js/');
define('IMG_URL', BASE_URL . 'assets/img/');

// -------------------------------------------------
// Zona horaria por defecto
// -------------------------------------------------
date_default_timezone_set('Europe/Madrid');
