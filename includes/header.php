<?php
// Incluir configuración si no está incluida
if (!defined('SITE_NAME')) {
    require_once 'config.php';
}

// Determinar la página actual para el menú activo
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($descripcion_pagina) ? $descripcion_pagina : 'Café, bar y panadería con los mejores productos y ambientes para disfrutar.'; ?>">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo url('assets/img/favicon.ico'); ?>" type="image/x-icon">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/responsive.css'); ?>">
    
    <?php if (isset($css_adicional) && is_array($css_adicional)): ?>
        <?php foreach ($css_adicional as $css): ?>
            <link rel="stylesheet" href="<?php echo url('assets/css/' . $css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo url(); ?>">
                        <img src="<?php echo url('assets/img/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>">
                    </a>
                </div>
                
                <nav class="menu">
                    <ul>
                        <li class="<?php echo $pagina_actual === 'index.php' ? 'active' : ''; ?>">
                            <a href="<?php echo url(); ?>">Inicio</a>
                        </li>
                        <li class="<?php echo $pagina_actual === 'carta.php' ? 'active' : ''; ?>">
                            <a href="<?php echo url('carta.php'); ?>">Carta</a>
                        </li>
                        <li class="<?php echo $pagina_actual === 'eventos.php' ? 'active' : ''; ?>">
                            <a href="<?php echo url('eventos.php'); ?>">Eventos</a>
                        </li>
                        <li class="<?php echo $pagina_actual === 'reservas.php' ? 'active' : ''; ?>">
                            <a href="<?php echo url('reservas.php'); ?>">Reservar</a>
                        </li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <a href="#" class="btn-search"><i class="fas fa-search"></i></a>
                    <a href="<?php echo url('reservas.php'); ?>" class="btn btn-primary">Reservar Mesa</a>
                    <button class="menu-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="close-menu">
            <i class="fas fa-times"></i>
        </div>
        <ul>
            <li><a href="<?php echo url(); ?>">Inicio</a></li>
            <li><a href="<?php echo url('carta.php'); ?>">Carta</a></li>
            <li><a href="<?php echo url('eventos.php'); ?>">Eventos</a></li>
            <li><a href="<?php echo url('reservas.php'); ?>">Reservar</a></li>
        </ul>
    </div>
    
    <!-- Alerta -->
    <?php $alerta = obtenerAlerta(); ?>
    <?php if ($alerta): ?>
    <div class="alert alert-<?php echo $alerta['tipo']; ?>">
        <div class="container">
            <?php echo $alerta['mensaje']; ?>
            <button class="close-alert"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Contenido principal -->
    <main>