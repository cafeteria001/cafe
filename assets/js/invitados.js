/**
 * JavaScript para el sistema de gestión de invitados
 * Este archivo contiene funciones específicas para administrar la lista de invitados a eventos
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    // Inicializar componentes
    initGuestModals();
    initGuestFilters();
    initBulkActions();
    initGuestSearch();
    initGuestCheckIn();
});

/**
 * Inicializa los modales para agregar, editar y eliminar invitados
 */
function initGuestModals() {
    // Modal de agregar invitado
    const btnAgregarInvitado = document.getElementById('btnAgregarInvitado');
    const modalAgregarInvitado = document.getElementById('modalAgregarInvitado');
    
    if (btnAgregarInvitado && modalAgregarInvitado) {
        // Abrir modal
        btnAgregarInvitado.addEventListener('click', function() {
            modalAgregarInvitado.classList.add('active');
        });
        
        // Cerrar modal
        const closeButtons = modalAgregarInvitado.querySelectorAll('.close-modal');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                modalAgregarInvitado.classList.remove('active');
                
                // Resetear formulario
                const form = modalAgregarInvitado.querySelector('form');
                if (form) form.reset();
            });
        });
        
        // Cerrar modal al hacer clic fuera del contenido
        modalAgregarInvitado.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
                
                // Resetear formulario
                const form = this.querySelector('form');
                if (form) form.reset();
            }
        });
    }
    
    // Modal de editar invitado
    const btnEditarInvitados = document.querySelectorAll('.btn-edit');
    const modalEditarInvitado = document.getElementById('modalEditarInvitado');
    
    if (btnEditarInvitados.length && modalEditarInvitado) {
        // Abrir modal
        btnEditarInvitados.forEach(button => {
            button.addEventListener('click', function() {
                // Obtener datos del invitado
                const invitadoId = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const email = this.getAttribute('data-email');
                const telefono = this.getAttribute('data-telefono') || '';
                const estado = this.getAttribute('data-estado');
                
                // Rellenar formulario
                const form = modalEditarInvitado.querySelector('form');
                
                if (form) {
                    form.querySelector('#edit_invitado_id').value = invitadoId;
                    form.querySelector('#edit_nombre').value = nombre;
                    form.querySelector('#edit_email').value = email;
                    form.querySelector('#edit_telefono').value = telefono;
                    form.querySelector('#edit_estado').value = estado;
                }
                
                // Mostrar modal
                modalEditarInvitado.classList.add('active');
            });
        });
        
        // Cerrar modal
        const closeButtons = modalEditarInvitado.querySelectorAll('.close-modal');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                modalEditarInvitado.classList.remove('active');
            });
        });
        
        // Cerrar modal al hacer clic fuera del contenido
        modalEditarInvitado.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    }
    
    // Modal de eliminar invitado
    const btnEliminarInvitados = document.querySelectorAll('.btn-delete');
    const modalEliminarInvitado = document.getElementById('modalEliminarInvitado');
    
    if (btnEliminarInvitados.length && modalEliminarInvitado) {
        // Abrir modal
        btnEliminarInvitados.forEach(button => {
            button.addEventListener('click', function() {
                // Obtener ID del invitado
                const invitadoId = this.getAttribute('data-id');
                
                // Establecer ID en el formulario
                const form = modalEliminarInvitado.querySelector('form');
                if (form) form.querySelector('#delete_invitado_id').value = invitadoId;
                
                // Mostrar modal
                modalEliminarInvitado.classList.add('active');
            });
        });
        
        // Cerrar modal
        const closeButtons = modalEliminarInvitado.querySelectorAll('.close-modal');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                modalEliminarInvitado.classList.remove('active');
            });
        });
        
        // Cerrar modal al hacer clic fuera del contenido
        modalEliminarInvitado.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    }
}

/**
 * Inicializa los filtros para la lista de invitados
 */
function initGuestFilters() {
    const statusFilter = document.getElementById('guestStatusFilter');
    const sortFilter = document.getElementById('guestSortFilter');
    
    if (statusFilter || sortFilter) {
        // Obtener todos los invitados
        const guestTable = document.querySelector('.data-table');
        
        if (!guestTable) return;
        
        const guestRows = guestTable.querySelectorAll('tbody tr');
        
        // Filtrar por estado
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                const selectedStatus = this.value;
                
                guestRows.forEach(row => {
                    const statusCell = row.querySelector('td:nth-child(4)');
                    
                    if (!statusCell) return;
                    
                    const statusText = statusCell.textContent.trim().toLowerCase();
                    
                    if (selectedStatus === 'todos' || statusText.includes(selectedStatus)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
        
        // Ordenar invitados
        if (sortFilter) {
            sortFilter.addEventListener('change', function() {
                const selectedSort = this.value;
                const tbody = guestTable.querySelector('tbody');
                
                if (!tbody) return;
                
                // Convertir NodeList a Array para poder ordenar
                const rowsArray = Array.from(guestRows);
                
                // Ordenar según la opción seleccionada
                switch (selectedSort) {
                    case 'nombre_asc':
                        rowsArray.sort((a, b) => {
                            const nameA = a.querySelector('td:first-child').textContent.trim();
                            const nameB = b.querySelector('td:first-child').textContent.trim();
                            return nameA.localeCompare(nameB);
                        });
                        break;
                    case 'nombre_desc':
                        rowsArray.sort((a, b) => {
                            const nameA = a.querySelector('td:first-child').textContent.trim();
                            const nameB = b.querySelector('td:first-child').textContent.trim();
                            return nameB.localeCompare(nameA);
                        });
                        break;
                    case 'fecha_asc':
                        rowsArray.sort((a, b) => {
                            const dateA = new Date(a.querySelector('td:nth-child(6)').textContent.trim());
                            const dateB = new Date(b.querySelector('td:nth-child(6)').textContent.trim());
                            return dateA - dateB;
                        });
                        break;
                    case 'fecha_desc':
                        rowsArray.sort((a, b) => {
                            const dateA = new Date(a.querySelector('td:nth-child(6)').textContent.trim());
                            const dateB = new Date(b.querySelector('td:nth-child(6)').textContent.trim());
                            return dateB - dateA;
                        });
                        break;
                    case 'estado':
                        rowsArray.sort((a, b) => {
                            const statusA = a.querySelector('td:nth-child(4)').textContent.trim();
                            const statusB = b.querySelector('td:nth-child(4)').textContent.trim();
                            return statusA.localeCompare(statusB);
                        });
                        break;
                }
                
                // Limpiar tbody
                tbody.innerHTML = '';
                
                // Agregar filas ordenadas
                rowsArray.forEach(row => {
                    tbody.appendChild(row);
                });
            });
        }
    }
}

/**
 * Inicializa las acciones masivas para la lista de invitados
 */
function initBulkActions() {
    const checkAllCheckbox = document.getElementById('checkAllGuests');
    const guestCheckboxes = document.querySelectorAll('.guest-checkbox');
    const bulkActionsContainer = document.querySelector('.bulk-actions');
    const bulkActionButtons = document.querySelectorAll('.bulk-action-btn');
    
    if (checkAllCheckbox && guestCheckboxes.length && bulkActionsContainer) {
        // Seleccionar/deseleccionar todos
        checkAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            
            guestCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            // Mostrar/ocultar acciones masivas
            if (isChecked) {
                bulkActionsContainer.classList.add('active');
                updateBulkSelectedCount(guestCheckboxes.length);
            } else {
                bulkActionsContainer.classList.remove('active');
            }
        });
        
        // Selección individual
        guestCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Contar cuántos están seleccionados
                const selectedCount = document.querySelectorAll('.guest-checkbox:checked').length;
                
                // Actualizar estado de "seleccionar todos"
                checkAllCheckbox.checked = selectedCount === guestCheckboxes.length;
                
                // Mostrar/ocultar acciones masivas
                if (selectedCount > 0) {
                    bulkActionsContainer.classList.add('active');
                    updateBulkSelectedCount(selectedCount);
                } else {
                    bulkActionsContainer.classList.remove('active');
                }
            });
        });
        
        // Acciones masivas
        if (bulkActionButtons.length) {
            bulkActionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    const selectedGuests = getSelectedGuestIds();
                    
                    if (selectedGuests.length === 0) {
                        showToast('No hay invitados seleccionados', 'warning');
                        return;
                    }
                    
                    // Ejecutar acción según el botón
                    switch (action) {
                        case 'confirm':
                            bulkChangeStatus(selectedGuests, 'confirmado');
                            break;
                        case 'cancel':
                            bulkChangeStatus(selectedGuests, 'cancelado');
                            break;
                        case 'pending':
                            bulkChangeStatus(selectedGuests, 'pendiente');
                            break;
                        case 'delete':
                            bulkDeleteGuests(selectedGuests);
                            break;
                        case 'export':
                            exportGuestList(selectedGuests);
                            break;
                        case 'email':
                            sendEmailToGuests(selectedGuests);
                            break;
                    }
                });
            });
        }
    }
}

/**
 * Actualiza el contador de invitados seleccionados
 * @param {number} count - Cantidad de invitados seleccionados
 */
function updateBulkSelectedCount(count) {
    const countElement = document.querySelector('.bulk-selected-count');
    
    if (countElement) {
        countElement.textContent = count;
    }
}

/**
 * Obtiene los IDs de los invitados seleccionados
 * @returns {Array} - Array con los IDs de los invitados seleccionados
 */
function getSelectedGuestIds() {
    const selectedCheckboxes = document.querySelectorAll('.guest-checkbox:checked');
    const ids = [];
    
    selectedCheckboxes.forEach(checkbox => {
        const id = checkbox.getAttribute('data-id');
        if (id) ids.push(id);
    });
    
    return ids;
}

/**
 * Cambia el estado de múltiples invitados
 * @param {Array} guestIds - Array con los IDs de los invitados
 * @param {string} status - Nuevo estado (confirmado, pendiente, cancelado)
 */
function bulkChangeStatus(guestIds, status) {
    // Mostrar loader
    const container = document.querySelector('.guests-list');
    const loader = showLoader(container);
    
    // Simulación de cambio de estado (en producción sería una petición AJAX)
    setTimeout(() => {
        hideLoader(loader);
        
        // Actualizar interfaz
        guestIds.forEach(id => {
            const row = document.querySelector(`.guest-checkbox[data-id="${id}"]`).closest('tr');
            
            if (row) {
                const statusCell = row.querySelector('td:nth-child(4)');
                
                if (statusCell) {
                    // Eliminar clases de estado actuales
                    statusCell.querySelector('.status-badge').classList.remove('status-confirmado', 'status-pendiente', 'status-cancelado');
                    
                    // Agregar nueva clase de estado
                    statusCell.querySelector('.status-badge').classList.add(`status-${status}`);
                    
                    // Actualizar texto e ícono
                    let statusText = '';
                    let statusIcon = '';
                    
                    switch (status) {
                        case 'confirmado':
                            statusText = 'Confirmado';
                            statusIcon = '<i class="fas fa-check-circle"></i>';
                            break;
                        case 'pendiente':
                            statusText = 'Pendiente';
                            statusIcon = '<i class="fas fa-clock"></i>';
                            break;
                        case 'cancelado':
                            statusText = 'Cancelado';
                            statusIcon = '<i class="fas fa-times-circle"></i>';
                            break;
                    }
                    
                    statusCell.querySelector('.status-badge').innerHTML = `${statusIcon} ${statusText}`;
                }
            }
        });
        
        // Mostrar mensaje de éxito
        showToast(`Se ha actualizado el estado de ${guestIds.length} invitados a "${status}"`, 'success');
        
        // Desmarcar checkboxes
        document.querySelectorAll('.guest-checkbox:checked').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Ocultar acciones masivas
        document.querySelector('.bulk-actions').classList.remove('active');
    }, 1000);
}

/**
 * Elimina múltiples invitados
 * @param {Array} guestIds - Array con los IDs de los invitados a eliminar
 */
function bulkDeleteGuests(guestIds) {
    // Mostrar confirmación
    if (!confirm(`¿Estás seguro de que deseas eliminar ${guestIds.length} invitados? Esta acción no se puede deshacer.`)) {
        return;
    }
    
    // Mostrar loader
    const container = document.querySelector('.guests-list');
    const loader = showLoader(container);
    
    // Simulación de eliminación (en producción sería una petición AJAX)
    setTimeout(() => {
        hideLoader(loader);
        
        // Eliminar filas de la tabla
        guestIds.forEach(id => {
            const row = document.querySelector(`.guest-checkbox[data-id="${id}"]`).closest('tr');
            
            if (row) {
                row.remove();
            }
        });
        
        // Mostrar mensaje de éxito
        showToast(`Se han eliminado ${guestIds.length} invitados correctamente`, 'success');
        
        // Ocultar acciones masivas
        document.querySelector('.bulk-actions').classList.remove('active');
    }, 1000);
}

/**
 * Exporta la lista de invitados seleccionados
 * @param {Array} guestIds - Array con los IDs de los invitados a exportar
 */
function exportGuestList(guestIds) {
    // Mostrar loader
    const container = document.querySelector('.guests-list');
    const loader = showLoader(container);
    
    // Simulación de exportación (en producción sería una petición AJAX)
    setTimeout(() => {
        hideLoader(loader);
        
        // Mostrar mensaje de éxito
        showToast(`Lista de ${guestIds.length} invitados exportada correctamente`, 'success');
        
        // Desmarcar checkboxes
        document.querySelectorAll('.guest-checkbox:checked').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Ocultar acciones masivas
        document.querySelector('.bulk-actions').classList.remove('active');
    }, 1000);
}

/**
 * Envía email a múltiples invitados
 * @param {Array} guestIds - Array con los IDs de los invitados
 */
function sendEmailToGuests(guestIds) {
    // Modal para redactar email
    const modalComposeEmail = document.getElementById('modalComposeEmail');
    
    if (modalComposeEmail) {
        // Mostrar modal
        modalComposeEmail.classList.add('active');
        
        // Actualizar contador de destinatarios
        const recipientsCount = modalComposeEmail.querySelector('.recipients-count');
        if (recipientsCount) {
            recipientsCount.textContent = guestIds.length;
        }
        
        // Establecer IDs en campo oculto
        const recipientsInput = modalComposeEmail.querySelector('input[name="recipient_ids"]');
        if (recipientsInput) {
            recipientsInput.value = guestIds.join(',');
        }
    }
}

/**
 * Inicializa la búsqueda de invitados
 */
function initGuestSearch() {
    const searchInput = document.getElementById('guestSearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const query = this.value.trim().toLowerCase();
            
            // Si no hay texto, mostrar todos los invitados
            if (query === '') {
                document.querySelectorAll('.data-table tbody tr').forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            // Filtrar invitados
            document.querySelectorAll('.data-table tbody tr').forEach(row => {
                const name = row.querySelector('td:first-child').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const phone = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                if (name.includes(query) || email.includes(query) || phone.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }, 300));
    }
}

/**
 * Inicializa el sistema de check-in de invitados
 */
function initGuestCheckIn() {
    const checkInForm = document.getElementById('guestCheckInForm');
    
    if (checkInForm) {
        checkInForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const codeInput = this.querySelector('input[name="codigo_acceso"]');
            const code = codeInput.value.trim();
            
            if (!code) {
                showToast('Por favor, introduce un código de acceso', 'warning');
                return;
            }
            
            // Mostrar loader
            const container = this.closest('.check-in-container');
            const loader = showLoader(container);
            
            // Simular verificación (en producción sería una petición AJAX)
            setTimeout(() => {
                hideLoader(loader);
                
                // Simular diferentes resultados
                const random = Math.random();
                
                if (random > 0.3) {
                    // Invitado válido
                    showGuestInfo({
                        nombre: 'Juan Pérez',
                        email: 'juan@ejemplo.com',
                        evento: 'Noche de Jazz en Vivo',
                        fecha: '15/12/2023',
                        estado: 'confirmado'
                    });
                } else {
                    // Código inválido
                    showToast('Código de acceso inválido o expirado', 'error');
                }
                
                // Limpiar formulario
                codeInput.value = '';
            }, 1000);
        });
    }
}

/**
 * Muestra información del invitado durante el check-in
 * @param {object} guest - Datos del invitado
 */
function showGuestInfo(guest) {
    const checkInContainer = document.querySelector('.check-in-container');
    const checkInForm = document.getElementById('guestCheckInForm');
    
    if (!checkInContainer || !checkInForm) return;
    
    // Ocultar formulario
    checkInForm.style.display = 'none';
    
    // Crear elemento de información del invitado
    const guestInfoElement = document.createElement('div');
    guestInfoElement.className = 'guest-info';
    
    // Definir clase según el estado
    const statusClass = `status-${guest.estado}`;
    
    // Crear contenido del elemento
    guestInfoElement.innerHTML = `
        <div class="guest-info-header ${statusClass}">
            <i class="fas fa-check-circle"></i>
            <h3>¡Invitado Verificado!</h3>
        </div>
        <div class="guest-info-content">
            <div class="guest-detail">
                <span class="label">Nombre:</span>
                <span class="value">${guest.nombre}</span>
            </div>
            <div class="guest-detail">
                <span class="label">Email:</span>
                <span class="value">${guest.email}</span>
            </div>
            <div class="guest-detail">
                <span class="label">Evento:</span>
                <span class="value">${guest.evento}</span>
            </div>
            <div class="guest-detail">
                <span class="label">Fecha:</span>
                <span class="value">${guest.fecha}</span>
            </div>
            <div class="guest-detail">
                <span class="label">Estado:</span>
                <span class="value ${statusClass}">${guest.estado}</span>
            </div>
        </div>
        <div class="guest-info-actions">
            <button type="button" id="completeCheckIn" class="btn btn-primary">Completar Check-in</button>
            <button type="button" id="cancelCheckIn" class="btn btn-secondary">Cancelar</button>
        </div>
    `;
    
    // Añadir elemento al contenedor
    checkInContainer.appendChild(guestInfoElement);
    
    // Añadir eventos a los botones
    const completeButton = document.getElementById('completeCheckIn');
    const cancelButton = document.getElementById('cancelCheckIn');
    
    if (completeButton) {
        completeButton.addEventListener('click', function() {
            // Mostrar loader
            const loader = showLoader(checkInContainer);
            
            // Simular completado de check-in
            setTimeout(() => {
                hideLoader(loader);
                
                // Eliminar información del invitado
                if (guestInfoElement.parentNode) {
                    guestInfoElement.parentNode.removeChild(guestInfoElement);
                }
                
                // Mostrar formulario nuevamente
                checkInForm.style.display = 'block';
                
                // Mostrar mensaje de éxito
                showToast('Check-in completado correctamente', 'success');
            }, 1000);
        });
    }
    
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            // Eliminar información del invitado
            if (guestInfoElement.parentNode) {
                guestInfoElement.parentNode.removeChild(guestInfoElement);
            }
            
            // Mostrar formulario nuevamente
            checkInForm.style.display = 'block';
        });
    }
}

/**
 * Genera un código QR para el invitado
 * @param {string} code - Código de acceso del invitado
 * @param {string} containerId - ID del elemento donde mostrar el QR
 */
function generateGuestQR(code, containerId) {
    const container = document.getElementById(containerId);
    
    if (!container) return;
    
    // Aquí se implementaría la generación del QR
    // Para este ejemplo, usamos un QR de placeholder
    container.innerHTML = `
        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(code)}" alt="Código QR">
        </div>
        <p class="qr-code-text">Código: <strong>${code}</strong></p>
        <p class="qr-instructions">Presenta este código en la entrada del evento</p>
    `;
}

/**
 * Envía recordatorios a los invitados
 * @param {number} eventId - ID del evento
 */
function sendEventReminders(eventId) {
    // Mostrar loader
    const container = document.querySelector('.admin-section');
    const loader = showLoader(container);
    
    // Simular envío de recordatorios (en producción sería una petición AJAX)
    setTimeout(() => {
        hideLoader(loader);
        
        // Mostrar mensaje de éxito
        showToast('Recordatorios enviados correctamente a los invitados confirmados', 'success');
    }, 1500);
}

/**
 * Genera un reporte de asistencia para un evento
 * @param {number} eventId - ID del evento
 */
function generateAttendanceReport(eventId) {
    // Mostrar loader
    const container = document.querySelector('.admin-section');
    const loader = showLoader(container);
    
    // Simular generación de reporte (en producción sería una petición AJAX)
    setTimeout(() => {
        hideLoader(loader);
        
        // Simular descarga del reporte
        const link = document.createElement('a');
        link.href = '#';
        link.download = `reporte_asistencia_evento_${eventId}.pdf`;
        link.click();
        
        // Mostrar mensaje de éxito
        showToast('Reporte de asistencia generado correctamente', 'success');
    }, 1500);
}

/**
 * Valida un formulario de invitado
 * @param {HTMLFormElement} form - Formulario a validar
 * @returns {boolean} - Verdadero si el formulario es válido
 */
function validateGuestForm(form) {
    // Usar la función de validación general
    return validateForm(form);
}

/**
 * Envía un formulario de invitado (agregar o editar)
 * @param {HTMLFormElement} form - Formulario a enviar
 * @param {string} action - Acción a realizar (add, edit)
 */
function submitGuestForm(form, action) {
    // Validar formulario
    if (!validateGuestForm(form)) {
        return;
    }
    
    // Mostrar loader
    const modal = form.closest('.modal');
    const loader = showLoader(modal.querySelector('.modal-body'));
    
    // Simular envío (en producción sería una petición AJAX)
    setTimeout(() => {
        hideLoader(loader);
        
        // Cerrar modal
        modal.classList.remove('active');
        
        // Mostrar mensaje de éxito
        if (action === 'add') {
            showToast('Invitado agregado correctamente', 'success');
            
            // Recargar página para ver el nuevo invitado
            // En producción, se actualizaría la tabla mediante JS
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('Invitado actualizado correctamente', 'success');
            
            // Recargar página para ver los cambios
            // En producción, se actualizaría la tabla mediante JS
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }, 1000);
}