<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Definir variables para el header
$titulo_pagina = 'Reserva de Mesas';
$descripcion_pagina = 'Reserva tu mesa en nuestro bar/cafetería de manera fácil y rápida.';
$css_adicional = ['reservas.css'];
$js_adicional = ['reservas.js'];

// Verificar si se ha enviado el formulario
$reserva_realizada = false;
$error_reserva = false;
$mensaje_reserva = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservar'])) {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'])) {
        $error_reserva = true;
        $mensaje_reserva = 'Error de seguridad. Por favor, intenta nuevamente.';
    } else {
        // Limpiar y validar datos
        $nombre = limpiarDato($conn, $_POST['nombre']);
        $email = limpiarDato($conn, $_POST['email']);
        $telefono = limpiarDato($conn, $_POST['telefono']);
        $fecha = limpiarDato($conn, $_POST['fecha']);
        $hora = limpiarDato($conn, $_POST['hora']);
        $personas = (int)$_POST['personas'];
        $notas = limpiarDato($conn, $_POST['notas']);
        
        // Validaciones básicas
        if (empty($nombre) || empty($email) || empty($telefono) || empty($fecha) || empty($hora) || $personas < 1) {
            $error_reserva = true;
            $mensaje_reserva = 'Por favor, completa todos los campos obligatorios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_reserva = true;
            $mensaje_reserva = 'Por favor, ingresa un correo electrónico válido.';
        } elseif (strtotime($fecha) < strtotime(date('Y-m-d'))) {
            $error_reserva = true;
            $mensaje_reserva = 'La fecha de reserva debe ser futura.';
        } else {
            // Buscar mesa disponible según el número de personas
            $sql_mesa = "SELECT id FROM mesas 
                        WHERE capacidad >= $personas 
                        AND id NOT IN (
                            SELECT mesa_id FROM reservas 
                            WHERE fecha = '$fecha' 
                            AND hora = '$hora' 
                            AND estado != 'cancelada'
                        )
                        ORDER BY capacidad ASC LIMIT 1";
            
            $resultado_mesa = ejecutarConsulta($conn, $sql_mesa);
            $mesa = obtenerFila($resultado_mesa);
            
            if ($mesa) {
                $mesa_id = $mesa['id'];
                
                // Insertar la reserva
                $sql_reserva = "INSERT INTO reservas (mesa_id, nombre, email, telefono, fecha, hora, num_personas, notas) 
                               VALUES ($mesa_id, '$nombre', '$email', '$telefono', '$fecha', '$hora', $personas, '$notas')";
                
                if (ejecutarConsulta($conn, $sql_reserva)) {
                    $reserva_id = ultimoIdInsertado($conn);
                    $reserva_realizada = true;
                    $mensaje_reserva = 'Tu reserva ha sido realizada con éxito. Te hemos enviado un correo de confirmación.';
                    
                    // Aquí iría el código para enviar el email de confirmación
                    // ...
                    
                    // Limpiar datos del formulario
                    $nombre = $email = $telefono = $fecha = $hora = $notas = '';
                    $personas = 1;
                } else {
                    $error_reserva = true;
                    $mensaje_reserva = 'Error al procesar la reserva. Por favor, intenta nuevamente.';
                }
            } else {
                $error_reserva = true;
                $mensaje_reserva = 'Lo sentimos, no hay mesas disponibles para la fecha y hora seleccionadas con esa cantidad de personas.';
            }
        }
    }
}

// Incluir el header
include_once 'includes/header.php';
?>

<!-- Banner de Reservas -->
<section class="page-banner" style="background-image: url('assets/img/reservation-banner.jpg');">
    <div class="container">
        <div class="banner-content">
            <h1>Reserva tu Mesa</h1>
            <p>Asegura tu lugar en nuestro establecimiento de forma fácil y rápida</p>
        </div>
    </div>
</section>

<!-- Sistema de Reservas -->
<section class="reservation-section">
    <div class="container">
        <?php if ($reserva_realizada): ?>
            <div class="reservation-success">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>¡Reserva Confirmada!</h2>
                <p><?php echo $mensaje_reserva; ?></p>
                <p>Si tienes alguna duda o necesitas modificar tu reserva, por favor contáctanos.</p>
                <div class="success-actions">
                    <a href="<?php echo url(); ?>" class="btn btn-secondary">Volver al Inicio</a>
                    <a href="<?php echo url('carta.php'); ?>" class="btn btn-primary">Ver Nuestra Carta</a>
                </div>
            </div>
        <?php else: ?>
            <div class="reservation-content">
                <div class="reservation-info">
                    <h2>Información de Reservas</h2>
                    <p>Para brindarte el mejor servicio, te recomendamos reservar tu mesa con anticipación.</p>
                    
                    <div class="info-points">
                        <div class="info-point">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h3>Horarios de Reserva</h3>
                                <p>Lunes a Viernes: 8:00 AM - 9:00 PM<br>
                                   Sábados y Domingos: 9:00 AM - 10:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="info-point">
                            <i class="fas fa-users"></i>
                            <div>
                                <h3>Capacidad</h3>
                                <p>Contamos con mesas para grupos de 2 hasta 8 personas.<br>
                                   Para grupos más grandes, por favor contáctanos directamente.</p>
                            </div>
                        </div>
                        
                        <div class="info-point">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <h3>Anticipación</h3>
                                <p>Puedes realizar tu reserva con hasta 30 días de anticipación.<br>
                                   Para el mismo día, sujeto a disponibilidad.</p>
                            </div>
                        </div>
                        
                        <div class="info-point">
                            <i class="fas fa-phone-alt"></i>
                            <div>
                                <h3>Contacto Directo</h3>
                                <p>¿Prefieres reservar por teléfono?<br>
                                   Llámanos al: +123 456 7890</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="reservation-form-container">
                    <h2>Reserva tu Mesa</h2>
                    
                    <?php if ($error_reserva): ?>
                        <div class="alert alert-error">
                            <?php echo $mensaje_reserva; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="reservation-form" id="reservationForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                        
                        <div class="form-group">
                            <label for="nombre">Nombre Completo *</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo isset($nombre) ? $nombre : ''; ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Correo Electrónico *</label>
                                <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="telefono">Teléfono *</label>
                                <input type="tel" id="telefono" name="telefono" value="<?php echo isset($telefono) ? $telefono : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha">Fecha *</label>
                                <input type="date" id="fecha" name="fecha" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" value="<?php echo isset($fecha) ? $fecha : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="hora">Hora *</label>
                                <select id="hora" name="hora" required>
                                    <option value="">Seleccionar...</option>
                                    <?php
                                    // Generar opciones de hora (de 8:00 AM a 10:00 PM, cada 30 minutos)
                                    $start = strtotime('08:00');
                                    $end = strtotime('22:00');
                                    for ($time = $start; $time <= $end; $time += 1800) {
                                        $selected = (isset($hora) && $hora === date('H:i', $time)) ? 'selected' : '';
                                        echo '<option value="' . date('H:i', $time) . '"' . $selected . '>' . date('h:i A', $time) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="personas">Número de Personas *</label>
                            <select id="personas" name="personas" required>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo (isset($personas) && $personas === $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?> <?php echo $i === 1 ? 'persona' : 'personas'; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notas">Notas o Solicitudes Especiales</label>
                            <textarea id="notas" name="notas" rows="3"><?php echo isset($notas) ? $notas : ''; ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="reservar" class="btn btn-primary">Confirmar Reserva</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Preguntas Frecuentes -->
<section class="reservation-faq">
    <div class="container">
        <div class="section-header">
            <h2>Preguntas Frecuentes</h2>
            <p>Respuestas a las dudas más comunes sobre nuestro sistema de reservas</p>
        </div>
        
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>¿Puedo modificar o cancelar mi reserva?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Sí, puedes modificar o cancelar tu reserva hasta 2 horas antes del horario reservado. Para hacerlo, contáctanos por teléfono o correo electrónico proporcionando tu nombre y datos de la reserva.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>¿Cuánto tiempo se mantiene mi mesa reservada?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Tu mesa se mantiene reservada hasta 15 minutos después de la hora confirmada. Después de ese tiempo, liberamos la mesa para otros clientes. Si vas a llegar tarde, te recomendamos avisarnos.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>¿Se requiere un depósito para reservar?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Para reservas estándar no se requiere depósito. Sin embargo, para grupos grandes (más de 8 personas) o eventos especiales, podríamos solicitar un anticipo para confirmar la reserva.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>¿Puedo solicitar una mesa específica?</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Puedes indicar tus preferencias en el campo de notas especiales. Haremos lo posible por asignarte la mesa que prefieras, aunque esto estará sujeto a disponibilidad.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Cerrar conexión a la base de datos
cerrarConexion($conn);

// Incluir el footer
include_once 'includes/footer.php';
?>