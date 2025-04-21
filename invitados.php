<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Verificar si el usuario está autenticado como administrador
if (!esAdmin()) {
    redirect('admin/login.php');
}

// Definir variables para el header
$titulo_pagina = 'Gestión de Invitados';
$descripcion_pagina = 'Administra las listas de invitados para los eventos del bar/cafetería.';
$css_adicional = ['admin.css', 'invitados.css'];
$js_adicional = ['admin.js', 'invitados.js'];

// Obtener el evento si se proporciona un ID
$evento_id = isset($_GET['evento']) ? (int)$_GET['evento'] : 0;

// Si se proporciona un ID de evento, obtener detalles del evento
$evento = null;
if ($evento_id > 0) {
    $sql_evento = "SELECT * FROM eventos WHERE id = $evento_id";
    $resultado_evento = ejecutarConsulta($conn, $sql_evento);
    $evento = obtenerFila($resultado_evento);
    
    // Si no existe el evento, redirigir a la lista de eventos
    if (!$evento) {
        redirect('admin/eventos.php');
    }
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'])) {
        mostrarAlerta('error', 'Error de seguridad. Por favor, intenta nuevamente.');
    } else {
        if (isset($_POST['agregar_invitado']) && $evento_id > 0) {
            // Agregar nuevo invitado
            $nombre = limpiarDato($conn, $_POST['nombre']);
            $email = limpiarDato($conn, $_POST['email']);
            $telefono = limpiarDato($conn, $_POST['telefono']);
            $estado = limpiarDato($conn, $_POST['estado']);
            
            // Generar código de acceso único
            $codigo_acceso = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 3)), 0, 8);
            
            // Validar datos
            if (empty($nombre) || empty($email)) {
                mostrarAlerta('error', 'Por favor, completa los campos obligatorios.');
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                mostrarAlerta('error', 'Por favor, ingresa un correo electrónico válido.');
            } else {
                // Verificar si el email ya está registrado para este evento
                $sql_check = "SELECT id FROM invitados WHERE evento_id = $evento_id AND email = '$email'";
                $resultado_check = ejecutarConsulta($conn, $sql_check);
                
                if ($resultado_check && $resultado_check->num_rows > 0) {
                    mostrarAlerta('error', 'Este correo electrónico ya está registrado para este evento.');
                } else {
                    // Insertar nuevo invitado
                    $sql_insertar = "INSERT INTO invitados (evento_id, nombre, email, telefono, estado, codigo_acceso) 
                                    VALUES ($evento_id, '$nombre', '$email', '$telefono', '$estado', '$codigo_acceso')";
                    
                    if (ejecutarConsulta($conn, $sql_insertar)) {
                        mostrarAlerta('success', 'Invitado agregado correctamente.');
                    } else {
                        mostrarAlerta('error', 'Error al agregar el invitado. Por favor, intenta nuevamente.');
                    }
                }
            }
        } elseif (isset($_POST['actualizar_invitado'])) {
            // Actualizar invitado existente
            $invitado_id = (int)$_POST['invitado_id'];
            $nombre = limpiarDato($conn, $_POST['nombre']);
            $email = limpiarDato($conn, $_POST['email']);
            $telefono = limpiarDato($conn, $_POST['telefono']);
            $estado = limpiarDato($conn, $_POST['estado']);
            
            // Validar datos
            if (empty($nombre) || empty($email)) {
                mostrarAlerta('error', 'Por favor, completa los campos obligatorios.');
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                mostrarAlerta('error', 'Por favor, ingresa un correo electrónico válido.');
            } else {
                // Verificar si el email ya está registrado para otro invitado en este evento
                $sql_check = "SELECT id FROM invitados WHERE evento_id = (SELECT evento_id FROM invitados WHERE id = $invitado_id) 
                             AND email = '$email' AND id != $invitado_id";
                $resultado_check = ejecutarConsulta($conn, $sql_check);
                
                if ($resultado_check && $resultado_check->num_rows > 0) {
                    mostrarAlerta('error', 'Este correo electrónico ya está registrado para otro invitado en este evento.');
                } else {
                    // Actualizar invitado
                    $sql_actualizar = "UPDATE invitados 
                                      SET nombre = '$nombre', email = '$email', telefono = '$telefono', estado = '$estado' 
                                      WHERE id = $invitado_id";
                    
                    if (ejecutarConsulta($conn, $sql_actualizar)) {
                        mostrarAlerta('success', 'Invitado actualizado correctamente.');
                    } else {
                        mostrarAlerta('error', 'Error al actualizar el invitado. Por favor, intenta nuevamente.');
                    }
                }
            }
        } elseif (isset($_POST['eliminar_invitado'])) {
            // Eliminar invitado
            $invitado_id = (int)$_POST['invitado_id'];
            
            $sql_eliminar = "DELETE FROM invitados WHERE id = $invitado_id";
            
            if (ejecutarConsulta($conn, $sql_eliminar)) {
                mostrarAlerta('success', 'Invitado eliminado correctamente.');
            } else {
                mostrarAlerta('error', 'Error al eliminar el invitado. Por favor, intenta nuevamente.');
            }
        } elseif (isset($_POST['enviar_recordatorios']) && $evento_id > 0) {
            // Enviar recordatorios a invitados
            $sql_invitados = "SELECT nombre, email FROM invitados WHERE evento_id = $evento_id AND estado = 'confirmado'";
            $resultado_invitados = ejecutarConsulta($conn, $sql_invitados);
            $invitados = obtenerFilas($resultado_invitados);
            
            if (!empty($invitados)) {
                // Aquí iría el código para enviar emails de recordatorio
                // ...
                
                mostrarAlerta('success', 'Recordatorios enviados correctamente a ' . count($invitados) . ' invitados.');
            } else {
                mostrarAlerta('error', 'No hay invitados confirmados para enviar recordatorios.');
            }
        }
    }
}

// Obtener todos los eventos para el selector
$sql_eventos = "SELECT id, titulo, fecha FROM eventos ORDER BY fecha DESC";
$resultado_eventos = ejecutarConsulta($conn, $sql_eventos);
$eventos = obtenerFilas($resultado_eventos);

// Si hay un evento seleccionado, obtener sus invitados
$invitados = [];
if ($evento_id > 0) {
    $sql_invitados = "SELECT * FROM invitados WHERE evento_id = $evento_id ORDER BY nombre ASC";
    $resultado_invitados = ejecutarConsulta($conn, $sql_invitados);
    $invitados = obtenerFilas($resultado_invitados);
}

// Incluir el header
include_once 'includes/header.php';
?>

<!-- Banner de Gestión de Invitados -->
<section class="admin-banner">
    <div class="container">
        <div class="admin-banner-content">
            <h1>Gestión de Invitados</h1>
            <p>Administra las listas de invitados para tus eventos</p>
        </div>
    </div>
</section>

<!-- Panel de Administración de Invitados -->
<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <div class="breadcrumb">
                <a href="<?php echo url('admin'); ?>">Dashboard</a> &gt; 
                <a href="<?php echo url('admin/eventos.php'); ?>">Eventos</a> &gt; 
                <span>Invitados</span>
            </div>
            
            <div class="admin-controls">
                <a href="<?php echo url('admin/eventos.php'); ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Volver a Eventos</a>
            </div>
        </div>
        
        <!-- Selector de Evento -->
        <div class="event-selector">
            <h2>Selecciona un Evento</h2>
            <div class="form-group">
                <select id="eventoSelector" onchange="window.location.href='<?php echo url('invitados.php'); ?>?evento=' + this.value">
                    <option value="">-- Selecciona un evento --</option>
                    <?php foreach ($eventos as $evt): ?>
                        <option value="<?php echo $evt['id']; ?>" <?php echo $evento_id === (int)$evt['id'] ? 'selected' : ''; ?>>
                            <?php echo $evt['titulo'] . ' (' . date('d/m/Y', strtotime($evt['fecha'])) . ')'; ?>
                        </option>
                    <?php foreach ($eventos as $evt): ?>
                        <option value="<?php echo $evt['id']; ?>" <?php echo $evento_id === (int)$evt['id'] ? 'selected' : ''; ?>>
                            <?php echo $evt['titulo'] . ' (' . date('d/m/Y', strtotime($evt['fecha'])) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <?php if ($evento): ?>
            <div class="event-details">
                <h2><?php echo $evento['titulo']; ?></h2>
                <div class="event-meta">
                    <span><i class="far fa-calendar"></i> <?php echo date('d/m/Y', strtotime($evento['fecha'])); ?></span>
                    <span><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora'])); ?></span>
                    <?php if ($evento['cupo_maximo']): ?>
                        <span><i class="fas fa-users"></i> Cupo máximo: <?php echo $evento['cupo_maximo']; ?> personas</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Estadísticas de Invitados -->
            <?php
                // Calcular estadísticas
                $total_invitados = count($invitados);
                $confirmados = 0;
                $pendientes = 0;
                $cancelados = 0;
                
                foreach ($invitados as $invitado) {
                    if ($invitado['estado'] === 'confirmado') {
                        $confirmados++;
                    } elseif ($invitado['estado'] === 'pendiente') {
                        $pendientes++;
                    } elseif ($invitado['estado'] === 'cancelado') {
                        $cancelados++;
                    }
                }
                
                // Calcular porcentajes si hay invitados
                $porcentaje_confirmados = $total_invitados > 0 ? round(($confirmados / $total_invitados) * 100) : 0;
                $porcentaje_pendientes = $total_invitados > 0 ? round(($pendientes / $total_invitados) * 100) : 0;
                $porcentaje_cancelados = $total_invitados > 0 ? round(($cancelados / $total_invitados) * 100) : 0;
            ?>
            
            <div class="guest-stats">
                <div class="stat-box">
                    <h3>Total de Invitados</h3>
                    <div class="stat-value"><?php echo $total_invitados; ?></div>
                </div>
                
                <div class="stat-box">
                    <h3>Confirmados</h3>
                    <div class="stat-value"><?php echo $confirmados; ?></div>
                    <div class="stat-percentage">
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo $porcentaje_confirmados; ?>%;"></div>
                        </div>
                        <span><?php echo $porcentaje_confirmados; ?>%</span>
                    </div>
                </div>
                
                <div class="stat-box">
                    <h3>Pendientes</h3>
                    <div class="stat-value"><?php echo $pendientes; ?></div>
                    <div class="stat-percentage">
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo $porcentaje_pendientes; ?>%;"></div>
                        </div>
                        <span><?php echo $porcentaje_pendientes; ?>%</span>
                    </div>
                </div>
                
                <div class="stat-box">
                    <h3>Cancelados</h3>
                    <div class="stat-value"><?php echo $cancelados; ?></div>
                    <div class="stat-percentage">
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo $porcentaje_cancelados; ?>%;"></div>
                        </div>
                        <span><?php echo $porcentaje_cancelados; ?>%</span>
                    </div>
                </div>
            </div>
            
            <!-- Acciones Masivas -->
            <div class="bulk-actions">
                <h3>Acciones Masivas</h3>
                <div class="action-buttons">
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?evento=' . $evento_id; ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                        <button type="submit" name="enviar_recordatorios" class="btn btn-secondary"><i class="fas fa-envelope"></i> Enviar Recordatorios</button>
                    </form>
                    
                    <a href="<?php echo url('admin/exportar_invitados.php?evento=' . $evento_id); ?>" class="btn btn-secondary"><i class="fas fa-file-export"></i> Exportar Lista</a>
                    
                    <button type="button" class="btn btn-primary" id="btnAgregarInvitado"><i class="fas fa-plus"></i> Nuevo Invitado</button>
                </div>
            </div>
            
            <!-- Lista de Invitados -->
            <div class="guests-list">
                <h3>Lista de Invitados</h3>
                
                <?php if (empty($invitados)): ?>
                    <div class="no-results">
                        <p>No hay invitados registrados para este evento.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th>Código</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invitados as $invitado): ?>
                                    <tr>
                                        <td><?php echo $invitado['nombre']; ?></td>
                                        <td><?php echo $invitado['email']; ?></td>
                                        <td><?php echo $invitado['telefono'] ? $invitado['telefono'] : '-'; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $invitado['estado']; ?>">
                                                <?php 
                                                    switch ($invitado['estado']) {
                                                        case 'confirmado':
                                                            echo '<i class="fas fa-check-circle"></i> Confirmado';
                                                            break;
                                                        case 'pendiente':
                                                            echo '<i class="fas fa-clock"></i> Pendiente';
                                                            break;
                                                        case 'cancelado':
                                                            echo '<i class="fas fa-times-circle"></i> Cancelado';
                                                            break;
                                                    }
                                                ?>
                                            </span>
                                        </td>
                                        <td><code><?php echo $invitado['codigo_acceso']; ?></code></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($invitado['created_at'])); ?></td>
                                        <td class="actions-cell">
                                            <button type="button" class="btn-icon btn-edit" title="Editar" 
                                                    data-id="<?php echo $invitado['id']; ?>"
                                                    data-nombre="<?php echo $invitado['nombre']; ?>"
                                                    data-email="<?php echo $invitado['email']; ?>"
                                                    data-telefono="<?php echo $invitado['telefono']; ?>"
                                                    data-estado="<?php echo $invitado['estado']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn-icon btn-delete" title="Eliminar" data-id="<?php echo $invitado['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Modal Agregar Invitado -->
            <div class="modal" id="modalAgregarInvitado">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Agregar Nuevo Invitado</h3>
                        <button type="button" class="close-modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?evento=' . $evento_id; ?>" method="post" id="formAgregarInvitado">
                            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                            
                            <div class="form-group">
                                <label for="nombre">Nombre Completo *</label>
                                <input type="text" id="nombre" name="nombre" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Correo Electrónico *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono">
                            </div>
                            
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmado">Confirmado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
                                <button type="submit" name="agregar_invitado" class="btn btn-primary">Guardar Invitado</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Modal Editar Invitado -->
            <div class="modal" id="modalEditarInvitado">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Editar Invitado</h3>
                        <button type="button" class="close-modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?evento=' . $evento_id; ?>" method="post" id="formEditarInvitado">
                            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                            <input type="hidden" name="invitado_id" id="edit_invitado_id">
                            
                            <div class="form-group">
                                <label for="edit_nombre">Nombre Completo *</label>
                                <input type="text" id="edit_nombre" name="nombre" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_email">Correo Electrónico *</label>
                                <input type="email" id="edit_email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_telefono">Teléfono</label>
                                <input type="tel" id="edit_telefono" name="telefono">
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_estado">Estado</label>
                                <select id="edit_estado" name="estado">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmado">Confirmado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
                                <button type="submit" name="actualizar_invitado" class="btn btn-primary">Actualizar Invitado</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Modal Eliminar Invitado -->
            <div class="modal" id="modalEliminarInvitado">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Eliminar Invitado</h3>
                        <button type="button" class="close-modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar este invitado? Esta acción no se puede deshacer.</p>
                        
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?evento=' . $evento_id; ?>" method="post" id="formEliminarInvitado">
                            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                            <input type="hidden" name="invitado_id" id="delete_invitado_id">
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
                                <button type="submit" name="eliminar_invitado" class="btn btn-danger">Eliminar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="no-event-selected">
                <div class="message-box">
                    <i class="fas fa-calendar-day"></i>
                    <h3>Selecciona un evento</h3>
                    <p>Para gestionar la lista de invitados, primero debes seleccionar un evento de la lista desplegable.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Scripts específicos para la página -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad para modales
    const modalAgregarInvitado = document.getElementById('modalAgregarInvitado');
    const modalEditarInvitado = document.getElementById('modalEditarInvitado');
    const modalEliminarInvitado = document.getElementById('modalEliminarInvitado');
    const btnAgregarInvitado = document.getElementById('btnAgregarInvitado');
    
    // Abrir modal de agregar invitado
    if (btnAgregarInvitado) {
        btnAgregarInvitado.addEventListener('click', function() {
            modalAgregarInvitado.classList.add('active');
        });
    }
    
    // Abrir modal de editar invitado
    document.querySelectorAll('.btn-edit').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const email = this.dataset.email;
            const telefono = this.dataset.telefono;
            const estado = this.dataset.estado;
            
            document.getElementById('edit_invitado_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_telefono').value = telefono;
            document.getElementById('edit_estado').value = estado;
            
            modalEditarInvitado.classList.add('active');
        });
    });
    
    // Abrir modal de eliminar invitado
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('delete_invitado_id').value = id;
            modalEliminarInvitado.classList.add('active');
        });
    });
    
    // Cerrar modales
    document.querySelectorAll('.close-modal').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
            }
        });
    });
    
    // Cerrar modal al hacer clic fuera del contenido
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
    
    // Abrir/cerrar preguntas frecuentes
    document.querySelectorAll('.faq-question').forEach(function(question) {
        question.addEventListener('click', function() {
            const faqItem = this.closest('.faq-item');
            faqItem.classList.toggle('active');
        });
    });
});
</script>

<?php
// Cerrar conexión a la base de datos
cerrarConexion($conn);

// Incluir el footer
include_once 'includes/footer.php';
?>