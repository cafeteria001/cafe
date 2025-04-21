/**
 * JavaScript para el sistema de reservas
 * Este archivo contiene funciones específicas para el sistema de reservas de mesas
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    // Inicializar componentes
    initReservationForm();
    initDatePicker();
    initTimePicker();
    initPersonsCounter();
    initReservationValidation();
});

/**
 * Inicializa el formulario de reservas
 */
function initReservationForm() {
    const reservationForm = document.getElementById('reservationForm');
    
    if (reservationForm) {
        // Inicializar validación del formulario
        reservationForm.addEventListener('submit', function(e) {
            // La validación se maneja en initReservationValidation()
        });
        
        // Verificar disponibilidad al cambiar fecha, hora o personas
        const dateInput = document.getElementById('fecha');
        const timeSelect = document.getElementById('hora');
        const personsSelect = document.getElementById('personas');
        
        if (dateInput && timeSelect && personsSelect) {
            // Verificar disponibilidad cuando cambian los valores
            const checkAvailability = debounce(function() {
                const date = dateInput.value;
                const time = timeSelect.value;
                const persons = personsSelect.value;
                
                if (date && time && persons) {
                    verifyAvailability(date, time, persons);
                }
            }, 500);
            
            dateInput.addEventListener('change', checkAvailability);
            timeSelect.addEventListener('change', checkAvailability);
            personsSelect.addEventListener('change', checkAvailability);
        }
    }
}

/**
 * Verifica la disponibilidad de mesas para una fecha, hora y número de personas
 * @param {string} date - Fecha de reserva (YYYY-MM-DD)
 * @param {string} time - Hora de reserva (HH:MM)
 * @param {number} persons - Número de personas
 */
function verifyAvailability(date, time, persons) {
    // Elemento donde mostrar el resultado
    const availabilityMessage = document.getElementById('availabilityMessage');
    
    if (!availabilityMessage) {
        // Crear elemento para mostrar el mensaje si no existe
        const container = document.querySelector('.reservation-form-container');
        const form = document.getElementById('reservationForm');
        
        if (container && form) {
            const message = document.createElement('div');
            message.id = 'availabilityMessage';
            message.className = 'availability-message';
            container.insertBefore(message, form);
        }
    }
    
    // Mostrar loader durante la verificación
    const container = document.querySelector('.reservation-form-container');
    const loader = showLoader(container);
    
    // Simular verificación (en producción se haría una petición AJAX)
    setTimeout(function() {
        hideLoader(loader);
        
        // Obtener el elemento de mensaje (pudo haberse creado después de iniciar la verificación)
        const messageElement = document.getElementById('availabilityMessage');
        
        if (messageElement) {
            // Simulamos diferentes estados de disponibilidad (en producción vendría del servidor)
            const availability = Math.random();
            
            if (availability > 0.7) {
                // Alta disponibilidad
                messageElement.className = 'availability-message available';
                messageElement.innerHTML = '<i class="fas fa-check-circle"></i> ¡Tenemos disponibilidad para esa fecha y hora!';
            } else if (availability > 0.3) {
                // Disponibilidad limitada
                messageElement.className = 'availability-message limited';
                messageElement.innerHTML = '<i class="fas fa-exclamation-circle"></i> Disponibilidad limitada. ¡Reserva pronto!';
            } else {
                // Sin disponibilidad
                messageElement.className = 'availability-message unavailable';
                messageElement.innerHTML = '<i class="fas fa-times-circle"></i> Lo sentimos, no hay disponibilidad para esa fecha y hora. Prueba con otra opción.';
            }
        }
    }, 1000);
}

/**
 * Inicializa el selector de fecha
 */
function initDatePicker() {
    const dateInput = document.getElementById('fecha');
    
    if (dateInput) {
        // Establecer fecha mínima (hoy)
        const today = new Date();
        const todayFormatted = today.toISOString().split('T')[0];
        dateInput.setAttribute('min', todayFormatted);
        
        // Establecer fecha máxima (30 días después)
        const maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 30);
        const maxDateFormatted = maxDate.toISOString().split('T')[0];
        dateInput.setAttribute('max', maxDateFormatted);
        
        // Si no hay fecha seleccionada, establecer hoy como valor por defecto
        if (!dateInput.value) {
            dateInput.value = todayFormatted;
        }
        
        // Mostrar calendario personalizado en móviles (opcional, se puede usar el nativo)
        if (isMobileDevice()) {
            // Aquí se podría inicializar un calendario personalizado para móviles
        }
    }
}

/**
 * Inicializa el selector de hora
 */
function initTimePicker() {
    const timeSelect = document.getElementById('hora');
    
    if (timeSelect) {
        // Limpiar opciones existentes
        timeSelect.innerHTML = '<option value="">Seleccionar hora...</option>';
        
        // Generar horas disponibles (de 08:00 a 22:00 cada 30 minutos)
        const startHour = 8; // 8 AM
        const endHour = 22; // 10 PM
        
        for (let hour = startHour; hour <= endHour; hour++) {
            // Formato de 12 horas para mostrar
            const period = hour < 12 ? 'AM' : 'PM';
            const displayHour = hour <= 12 ? hour : hour - 12;
            
            // Añadir opción para hora en punto
            const valueHour = hour.toString().padStart(2, '0') + ':00';
            const textHour = displayHour + ':00 ' + period;
            
            const optionHour = document.createElement('option');
            optionHour.value = valueHour;
            optionHour.textContent = textHour;
            timeSelect.appendChild(optionHour);
            
            // Añadir opción para media hora si no es la última hora
            if (hour < endHour) {
                const valueHalfHour = hour.toString().padStart(2, '0') + ':30';
                const textHalfHour = displayHour + ':30 ' + period;
                
                const optionHalfHour = document.createElement('option');
                optionHalfHour.value = valueHalfHour;
                optionHalfHour.textContent = textHalfHour;
                timeSelect.appendChild(optionHalfHour);
            }
        }
        
        // Predeterminar un horario popular (por ejemplo, 20:00)
        timeSelect.value = '20:00';
    }
}

/**
 * Inicializa el contador de personas
 */
function initPersonsCounter() {
    const personsSelect = document.getElementById('personas');
    
    if (personsSelect) {
        // No es necesario hacer nada adicional ya que usamos un select normal
        // Pero podríamos personalizarlo si fuera necesario
    }
}

/**
 * Inicializa la validación del formulario de reserva
 */
function initReservationValidation() {
    const reservationForm = document.getElementById('reservationForm');
    
    if (reservationForm) {
        reservationForm.addEventListener('submit', function(e) {
            // Prevenir envío por defecto
            e.preventDefault();
            
            // Validar el formulario
            if (validateReservationForm(this)) {
                // Si la validación es correcta, confirmar la reserva
                confirmReservation(this);
            }
        });
    }
}

/**
 * Valida el formulario de reserva
 * @param {HTMLFormElement} form - Formulario a validar
 * @returns {boolean} - Verdadero si el formulario es válido
 */
function validateReservationForm(form) {
    // Usar la función de validación general
    const isValid = validateForm(form);
    
    // Validaciones específicas para reservas
    const dateInput = form.querySelector('#fecha');
    const timeSelect = form.querySelector('#hora');
    
    if (isValid && dateInput) {
        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            showError(dateInput, 'La fecha no puede ser anterior a hoy');
            return false;
        }
    }
    
    // Verificar que se haya seleccionado una hora
    if (isValid && timeSelect && !timeSelect.value) {
        showError(timeSelect, 'Por favor, selecciona una hora');
        return false;
    }
    
    return isValid;
}

/**
 * Envía el formulario de reserva y muestra el resultado
 * @param {HTMLFormElement} form - Formulario a enviar
 */
function confirmReservation(form) {
    // Mostrar loader durante el envío
    const container = form.closest('.reservation-form-container');
    const loader = showLoader(container);
    
    // Simular envío (en producción se haría una petición AJAX)
    setTimeout(function() {
        hideLoader(loader);
        
        // Simular respuesta exitosa
        const formData = formToObject(form);
        
        // Guardar datos de la reserva en localStorage para uso futuro
        localStorage.setItem('lastReservation', JSON.stringify({
            nombre: formData.nombre,
            fecha: formData.fecha,
            hora: formData.hora,
            personas: formData.personas
        }));
        
        // Redireccionar a la página de confirmación o mostrar mensaje de éxito
        // En este caso, vamos a enviar el formulario real para que el backend lo procese
        form.submit();
    }, 1500);
}

/**
 * Actualiza las horas disponibles según la fecha seleccionada
 * @param {string} selectedDate - Fecha seleccionada (YYYY-MM-DD)
 */
function updateAvailableHours(selectedDate) {
    const timeSelect = document.getElementById('hora');
    
    if (!timeSelect) return;
    
    // Obtener día de la semana (0 = domingo, 6 = sábado)
    const date = new Date(selectedDate);
    const dayOfWeek = date.getDay();
    
    // Guardar la hora seleccionada actualmente
    const currentSelectedTime = timeSelect.value;
    
    // Limpiar opciones existentes
    timeSelect.innerHTML = '<option value="">Seleccionar hora...</option>';
    
    // Definir horas según el día de la semana
    let startHour, endHour;
    
    if (dayOfWeek === 0 || dayOfWeek === 6) {
        // Fin de semana (horas ampliadas)
        startHour = 9; // 9 AM
        endHour = 23; // 11 PM
    } else {
        // Lunes a viernes
        startHour = 8; // 8 AM
        endHour = 22; // 10 PM
    }
    
    // Generar horas disponibles
    for (let hour = startHour; hour <= endHour; hour++) {
        // Formato de 12 horas para mostrar
        const period = hour < 12 ? 'AM' : 'PM';
        const displayHour = hour <= 12 ? hour : hour - 12;
        
        // Añadir opción para hora en punto
        const valueHour = hour.toString().padStart(2, '0') + ':00';
        const textHour = displayHour + ':00 ' + period;
        
        const optionHour = document.createElement('option');
        optionHour.value = valueHour;
        optionHour.textContent = textHour;
        timeSelect.appendChild(optionHour);
        
        // Añadir opción para media hora si no es la última hora
        if (hour < endHour) {
            const valueHalfHour = hour.toString().padStart(2, '0') + ':30';
            const textHalfHour = displayHour + ':30 ' + period;
            
            const optionHalfHour = document.createElement('option');
            optionHalfHour.value = valueHalfHour;
            optionHalfHour.textContent = textHalfHour;
            timeSelect.appendChild(optionHalfHour);
        }
    }
    
    // Intentar restaurar la hora seleccionada previamente
    if (currentSelectedTime) {
        const option = timeSelect.querySelector(`option[value="${currentSelectedTime}"]`);
        
        if (option) {
            timeSelect.value = currentSelectedTime;
        }
    }
}

// Event listener para actualizar horas cuando cambia la fecha
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('fecha');
    
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            updateAvailableHours(this.value);
        });
        
        // Actualizar horas para la fecha inicial
        if (dateInput.value) {
            updateAvailableHours(dateInput.value);
        }
    }
});