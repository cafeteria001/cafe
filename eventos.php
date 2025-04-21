<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Definir variables para el header
$titulo_pagina = 'Eventos y Recitales';
$descripcion_pagina = 'Descubre todos los eventos, recitales y actividades que tenemos preparados para ti.';
$css_adicional = ['eventos.css'];
$js_adicional = ['eventos.js'];

// Verificar si se está viendo un evento específico
$evento_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fecha actual para filtrar eventos
$fecha_actual = date('Y-m-d');

if ($evento_id > 0) {
    // Obtener detalles del evento específico
    $sql_evento = "SELECT * FROM eventos WHERE id = $evento_id";
    $resultado_evento = ejecutarConsulta($conn, $sql_evento);
    $evento = obtenerFila($resultado_evento);
    
    // Si no existe el evento, redirigir a la página de eventos
    if (!$evento) {
        redirect('eventos.php');
    }
    
    // Actualizar título de la página
    $titulo_pagina = $evento['titulo'] . ' - Eventos';
    
    // Incluir el header
    include_once 'includes/header.php';
    
    // Mostrar detalle del evento
    ?>
    
    <!-- Detalle del Evento -->
    <section class="event-detail">
        <div class="container">
            <div class="breadcrumb">
                <a href="<?php echo url(); ?>">Inicio</a> &gt; 
                <a href="<?php echo url('eventos.php'); ?>">Eventos</a> &gt; 
                <span><?php echo $evento['titulo']; ?></span>
            </div>
            
            <div class="event-content">
                <div class="event-header">
                    <h1><?php echo $evento['titulo']; ?></h1>
                    <div class="event-meta">
                        <span><i class="far fa-calendar"></i> <?php echo date('d/m/Y', strtotime($evento['fecha'])); ?></span>
                        <span><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora'])); ?></span>
                        <?php if ($evento['precio_entrada'] > 0): ?>
                            <span><i class="fas fa-ticket-alt"></i> $<?php echo number_format($evento['precio_entrada'], 2); ?></span>
                        <?php else: ?>
                            <span><i class="fas fa-ticket-alt"></i> Entrada libre</span>
                        <?php endif; ?>
                        <?php if ($evento['cupo_maximo']): ?>
                            <span><i class="fas fa-users"></i> Cupo: <?php echo $evento['cupo_maximo']; ?> personas</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="event-main">
                    <div class="event-image">
                        <?php if (!empty($evento['imagen'])): ?>
                            <img src="<?php echo url('uploads/eventos/' . $evento['imagen']); ?>" alt="<?php echo $evento['titulo']; ?>">
                        <?php else: ?>
                            <img src="<?php echo url('assets/img/event-default.jpg'); ?>" alt="<?php echo $evento['titulo']; ?>">
                        <?php endif; ?>
                    </div>
                    
                    <div class="event-description">
                        <?php echo nl2br($evento['descripcion']); ?>
                    </div>
                </div>
                
                <!-- Formulario para inscripción a evento -->
                <?php if (strtotime($evento['fecha']) >= strtotime($fecha_actual)): ?>
                    <div class="event-registration">
                        <h3>¿Quieres asistir a este evento?</h3>
                        <p>Completa el formulario para registrarte como invitado.</p>
                        
                        <form action="proceso_invitado.php" method="post" class="registration-form">
                            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                            <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nombre">Nombre Completo *</label>
                                    <input type="text" id="nombre" name="nombre" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Correo Electrónico *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono">
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Registrarme al Evento</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="event-passed">
                        <p>Este evento ya ha finalizado.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Otros eventos recomendados -->
            <?php 
            // Obtener otros eventos próximos
            $sql_otros = "SELECT * FROM eventos 
                         WHERE id != $evento_id AND fecha >= '$fecha_actual' 
                         ORDER BY fecha ASC LIMIT 3";
            $resultado_otros = ejecutarConsulta($conn, $sql_otros);
            $otros_eventos = obtenerFilas($resultado_otros);
            
            if (!empty($otros_eventos)):
            ?>
            <div class="other-events">
                <h3>También te puede interesar</h3>
                <div class="events-grid">
                    <?php foreach ($otros_eventos as $otro): ?>
                        <div class="event-card">
                            <div class="event-image">
                                <?php if (!empty($otro['imagen'])): ?>
                                    <img src="<?php echo url('uploads/eventos/' . $otro['imagen']); ?>" alt="<?php echo $otro['titulo']; ?>">
                                <?php else: ?>
                                    <img src="<?php echo url('assets/img/event-default.jpg'); ?>" alt="<?php echo $otro['titulo']; ?>">
                                <?php endif; ?>
                                <div class="event-date">
                                    <span class="day"><?php echo date('d', strtotime($otro['fecha'])); ?></span>
                                    <span class="month"><?php echo date('M', strtotime($otro['fecha'])); ?></span>
                                </div>
                            </div>
                            <div class="event-info">
                                <h3><?php echo $otro['titulo']; ?></h3>
                                <div class="event-meta">
                                    <span><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($otro['hora'])); ?></span>
                                </div>
                                <p><?php echo substr($otro['descripcion'], 0, 100) . (strlen($otro['descripcion']) > 100 ? '...' : ''); ?></p>
                                <a href="<?php echo url('eventos.php?id=' . $otro['id']); ?>" class="btn btn-outline">Ver Detalles</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
<?php
} else {
    // Obtener próximos eventos
    $sql_proximos = "SELECT * FROM eventos 
                    WHERE fecha >= '$fecha_actual' 
                    ORDER BY fecha ASC";
    $resultado_proximos = ejecutarConsulta($conn, $sql_proximos);
    $proximos_eventos = obtenerFilas($resultado_proximos);
    
    // Obtener eventos pasados
    $sql_pasados = "SELECT * FROM eventos 
                   WHERE fecha < '$fecha_actual' 
                   ORDER BY fecha DESC LIMIT 6";
    $resultado_pasados = ejecutarConsulta($conn, $sql_pasados);
    $eventos_pasados = obtenerFilas($resultado_pasados);
    
    // Incluir el header
    include_once 'includes/header.php';
?>

<!-- Banner de Eventos -->
<section class="page-banner" style="background-image: url('assets/img/events-banner.jpg');">
    <div class="container">
        <div class="banner-content">
            <h1>Eventos y Recitales</h1>
            <p>Descubre todas las actividades que tenemos preparadas para ti</p>
        </div>
    </div>
</section>

<!-- Próximos Eventos -->
<section class="upcoming-events">
    <div class="container">
        <div class="section-header">
            <h2>Próximos Eventos</h2>
            <p>No te pierdas nuestras actividades programadas</p>
        </div>
        
        <?php if (!empty($proximos_eventos)): ?>
            <div class="events-list">
                <?php foreach ($proximos_eventos as $evento): ?>
                    <div class="event-large">
                        <div class="event-image">
                            <?php if (!empty($evento['imagen'])): ?>
                                <img src="<?php echo url('uploads/eventos/' . $evento['imagen']); ?>" alt="<?php echo $evento['titulo']; ?>">
                            <?php else: ?>
                                <img src="<?php echo url('assets/img/event-default.jpg'); ?>" alt="<?php echo $evento['titulo']; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="event-content">
                            <div class="event-date-large">
                                <span class="day"><?php echo date('d', strtotime($evento['fecha'])); ?></span>
                                <span class="month"><?php echo date('M', strtotime($evento['fecha'])); ?></span>
                                <span class="year"><?php echo date('Y', strtotime($evento['fecha'])); ?></span>
                            </div>
                            <div class="event-details">
                                <h3><?php echo $evento['titulo']; ?></h3>
                                <div class="event-meta">
                                    <span><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora'])); ?></span>
                                    <?php if ($evento['precio_entrada'] > 0): ?>
                                        <span><i class="fas fa-ticket-alt"></i> $<?php echo number_format($evento['precio_entrada'], 2); ?></span>
                                    <?php else: ?>
                                        <span><i class="fas fa-ticket-alt"></i> Entrada libre</span>
                                    <?php endif; ?>
                                    <?php if ($evento['cupo_maximo']): ?>
                                        <span><i class="fas fa-users"></i> Cupo: <?php echo $evento['cupo_maximo']; ?> personas</span>
                                    <?php endif; ?>
                                </div>
                                <p class="event-description"><?php echo substr($evento['descripcion'], 0, 200) . (strlen($evento['descripcion']) > 200 ? '...' : ''); ?></p>
                                <div class="event-actions">
                                    <a href="<?php echo url('eventos.php?id=' . $evento['id']); ?>" class="btn btn-primary">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <p>No hay eventos programados próximamente. ¡Vuelve pronto para más novedades!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Eventos Pasados -->
<section class="past-events">
    <div class="container">
        <div class="section-header">
            <h2>Eventos Anteriores</h2>
            <p>Revive algunos de nuestros mejores momentos</p>
        </div>
        
        <?php if (!empty($eventos_pasados)): ?>
            <div class="events-grid">
                <?php foreach ($eventos_pasados as $evento): ?>
                    <div class="event-card past">
                        <div class="event-image">
                            <?php if (!empty($evento['imagen'])): ?>
                                <img src="<?php echo url('uploads/eventos/' . $evento['imagen']); ?>" alt="<?php echo $evento['titulo']; ?>">
                            <?php else: ?>
                                <img src="<?php echo url('assets/img/event-default.jpg'); ?>" alt="<?php echo $evento['titulo']; ?>">
                            <?php endif; ?>
                            <div class="event-date">
                                <span class="day"><?php echo date('d', strtotime($evento['fecha'])); ?></span>
                                <span class="month"><?php echo date('M', strtotime($evento['fecha'])); ?></span>
                            </div>
                            <div class="past-overlay">
                                <span>Finalizado</span>
                            </div>
                        </div>
                        <div class="event-info">
                            <h3><?php echo $evento['titulo']; ?></h3>
                            <div class="event-meta">
                                <span><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora'])); ?></span>
                            </div>
                            <p><?php echo substr($evento['descripcion'], 0, 100) . (strlen($evento['descripcion']) > 100 ? '...' : ''); ?></p>
                            <a href="<?php echo url('eventos.php?id=' . $evento['id']); ?>" class="btn btn-outline">Ver Recuerdos</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <p>Aún no tenemos eventos pasados registrados.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Suscripción a Eventos -->
<section class="event-subscribe" style="background-image: url('assets/img/event-subscribe-bg.jpg');">
    <div class="container">
        <div class="subscribe-content">
            <h2>¿No te quieres perder ningún evento?</h2>
            <p>Suscríbete a nuestro boletín y recibe información sobre todos nuestros eventos.</p>
            <form action="#" method="post" class="subscribe-form">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <input type="email" name="email" placeholder="Tu correo electrónico" required>
                <button type="submit" class="btn btn-primary">Suscribirme</button>
            </form>
        </div>
    </div>
</section>

<?php
}
// Cerrar conexión a la base de datos
cerrarConexion($conn);

// Incluir el footer
include_once 'includes/footer.php';
?>