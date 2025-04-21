/**
 * JavaScript para el sistema de eventos
 * Este archivo contiene funciones específicas para la gestión de eventos y recitales
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    // Inicializar componentes
    initEventFilters();
    initEventRegistrationForm();
    initEventGallery();
    initEventSubscription();
    initShareButtons();
});

/**
 * Inicializa los filtros de eventos
 */
function initEventFilters() {
    const filterButtons = document.querySelectorAll('.event-filter-btn');
    const eventItems = document.querySelectorAll('.event-item');
    
    if (filterButtons.length && eventItems.length) {
        // Agregar evento click a los botones de filtro
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remover clase activa de todos los botones
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Agregar clase activa al botón clickeado
                this.classList.add('active');
                
                // Obtener categoría a filtrar
                const filterValue = this.getAttribute('data-filter');
                
                // Mostrar u ocultar eventos según el filtro
                eventItems.forEach(item => {
                    if (filterValue === 'all' || item.classList.contains(filterValue)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }
    
    // Filtro por fecha (mes y año)
    const monthFilter = document.getElementById('eventMonthFilter');
    const yearFilter = document.getElementById('eventYearFilter');
    
    if (monthFilter && yearFilter && eventItems.length) {
        const filterByDate = function() {
            const selectedMonth = parseInt(monthFilter.value);
            const selectedYear = parseInt(yearFilter.value);
            
            eventItems.forEach(item => {
                const eventDate = new Date(item.getAttribute('data-date'));
                const eventMonth = eventDate.getMonth() + 1; // getMonth() es zero-based
                const eventYear = eventDate.getFullYear();
                
                // Si el mes es 0 (todos) o coincide, y el año es 0 (todos) o coincide
                if ((selectedMonth === 0 || eventMonth === selectedMonth) && 
                    (selectedYear === 0 || eventYear === selectedYear)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        };
        
        // Agregar eventos change a los selectores
        monthFilter.addEventListener('change', filterByDate);
        yearFilter.addEventListener('change', filterByDate);
    }
}

/**
 * Inicializa el formulario de registro a eventos
 */
function initEventRegistrationForm() {
    const registrationForm = document.querySelector('.registration-form');
    
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar el formulario
            if (validateRegistrationForm(this)) {
                // Enviar formulario si es válido
                submitEventRegistration(this);
            }
        });
    }
}

/**
 * Valida el formulario de registro a eventos
 * @param {HTMLFormElement} form - Formulario a validar
 * @returns {boolean} - Verdadero si el formulario es válido
 */
function validateRegistrationForm(form) {
    // Usar la función de validación general
    return validateForm(form);
}

/**
 * Envía el formulario de registro a eventos
 * @param {HTMLFormElement} form - Formulario a enviar
 */
function submitEventRegistration(form) {
    // Mostrar loader durante el envío
    const container = form.closest('.event-registration');
    const loader = showLoader(container);
    
    // Obtener datos del formulario
    const formData = new FormData(form);
    
    // Preparar opciones para la petición fetch
    const options = {
        method: 'POST',
        body: formData
    };
    
    // Enviar petición AJAX (simulado)
    setTimeout(function() {
        hideLoader(loader);
        
        // Simular respuesta exitosa
        const successMessage = document.createElement('div');
        successMessage.className = 'registration-success';
        successMessage.innerHTML = `
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>¡Registro Completado!</h3>
            <p>Te has registrado correctamente al evento. Te hemos enviado un email con la confirmación y los detalles.</p>
            <p>Tu código de acceso es: <strong>${generateRandomCode()}</strong></p>
        `;
        
        // Reemplazar formulario con mensaje de éxito
        form.style.display = 'none';
        container.appendChild(successMessage);
    }, 1500);
}

/**
 * Genera un código aleatorio para acceso a eventos
 * @returns {string} - Código aleatorio
 */
function generateRandomCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    
    for (let i = 0; i < 8; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    return code;
}

/**
 * Inicializa la galería de fotos de eventos pasados
 */
function initEventGallery() {
    const galleryItems = document.querySelectorAll('.event-gallery-item');
    const lightbox = document.getElementById('eventLightbox');
    
    if (galleryItems.length && lightbox) {
        // Abrir lightbox al hacer clic en una imagen
        galleryItems.forEach(item => {
            item.addEventListener('click', function() {
                const imgSrc = this.querySelector('img').getAttribute('src');
                const imgCaption = this.getAttribute('data-caption');
                
                // Establecer imagen y caption en el lightbox
                const lightboxImg = lightbox.querySelector('.lightbox-img');
                const lightboxCaption = lightbox.querySelector('.lightbox-caption');
                
                lightboxImg.setAttribute('src', imgSrc);
                lightboxCaption.textContent = imgCaption;
                
                // Mostrar lightbox
                lightbox.classList.add('active');
            });
        });
        
        // Cerrar lightbox
        const closeButton = lightbox.querySelector('.lightbox-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                lightbox.classList.remove('active');
            });
        }
        
        // Cerrar lightbox al hacer clic fuera de la imagen
        lightbox.addEventListener('click', function(e) {
            if (e.target === this) {
                lightbox.classList.remove('active');
            }
        });
    }
}

/**
 * Inicializa el formulario de suscripción a eventos
 */
function initEventSubscription() {
    const subscribeForm = document.querySelector('.subscribe-form');
    
    if (subscribeForm) {
        subscribeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar email
            const emailInput = this.querySelector('input[type="email"]');
            
            if (!emailInput || !emailInput.value.trim()) {
                showError(emailInput, 'Por favor, introduce tu email');
                return;
            }
            
            if (!isValidEmail(emailInput.value)) {
                showError(emailInput, 'Por favor, introduce un email válido');
                return;
            }
            
            // Si es válido, procesar suscripción
            subscribeToEvents(emailInput.value);
        });
    }
}

/**
 * Procesa la suscripción a eventos
 * @param {string} email - Email para suscribir
 */
function subscribeToEvents(email) {
    // Mostrar loader
    const form = document.querySelector('.subscribe-form');
    const container = form.closest('.subscribe-content');
    const loader = showLoader(container);
    
    // Simular envío
    setTimeout(function() {
        hideLoader(loader);
        
        // Mostrar mensaje de éxito
        form.innerHTML = `
            <div class="subscription-success">
                <i class="fas fa-check-circle"></i>
                <p>¡Gracias por suscribirte! Te mantendremos informado sobre nuestros próximos eventos.</p>
            </div>
        `;
    }, 1500);
}

/**
 * Inicializa los botones para compartir eventos
 */
function initShareButtons() {
    const shareButtons = document.querySelectorAll('.event-share-btn');
    
    if (shareButtons.length) {
        shareButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const platform = this.getAttribute('data-platform');
                const eventId = this.closest('.event-actions').getAttribute('data-event-id');
                const eventTitle = this.closest('.event-actions').getAttribute('data-event-title');
                
                // Construir URL del evento
                const eventURL = `${window.location.origin}${window.location.pathname}?id=${eventId}`;
                
                // Compartir según la plataforma
                switch(platform) {
                    case 'facebook':
                        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(eventURL)}`, '_blank');
                        break;
                    case 'twitter':
                        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(eventURL)}&text=${encodeURIComponent(`¡Mira este evento: ${eventTitle}`)}`, '_blank');
                        break;
                    case 'whatsapp':
                        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(`¡Mira este evento: ${eventTitle} - ${eventURL}`)}`, '_blank');
                        break;
                    case 'email':
                        window.location.href = `mailto:?subject=${encodeURIComponent(`Te invito a un evento: ${eventTitle}`)}&body=${encodeURIComponent(`Hola,\n\nQuería compartir contigo este evento que puede interesarte:\n\n${eventTitle}\n\nPuedes ver todos los detalles aquí: ${eventURL}\n\n¡Espero verte allí!`)}`;
                        break;
                }
            });
        });
    }
}

/**
 * Agrega un evento al calendario del usuario
 * @param {object} eventData - Datos del evento a añadir
 */
function addToCalendar(eventData) {
    const { title, description, location, startDate, endDate } = eventData;
    
    // Formatear fechas para calendario
    const formatDate = (date) => {
        return date.toISOString().replace(/-|:|\.\d+/g, '');
    };
    
    const start = formatDate(new Date(startDate));
    const end = formatDate(new Date(endDate));
    
    // Crear URL para Google Calendar
    const googleCalendarUrl = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&details=${encodeURIComponent(description)}&location=${encodeURIComponent(location)}&dates=${start}/${end}`;
    
    // Abrir en nueva ventana
    window.open(googleCalendarUrl, '_blank');
}

/**
 * Busca eventos por nombre o descripción
 * @param {string} query - Texto a buscar
 */
function searchEvents(query) {
    // Mostrar loader
    const eventsContainer = document.querySelector('.events-list, .events-grid');
    
    if (!eventsContainer) return;
    
    const loader = showLoader(eventsContainer);
    
    // Limpiar búsqueda
    query = query.trim().toLowerCase();
    
    // Simular búsqueda
    setTimeout(function() {
        hideLoader(loader);
        
        // Obtener todos los eventos
        const eventItems = document.querySelectorAll('.event-large, .event-card');
        let found = 0;
        
        eventItems.forEach(item => {
            const title = item.querySelector('h3').textContent.toLowerCase();
            const description = item.querySelector('.event-description, p').textContent.toLowerCase();
            
            // Verificar si el texto coincide con la búsqueda
            if (title.includes(query) || description.includes(query)) {
                item.style.display = 'block';
                found++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no se encontraron resultados
        let noResultsElement = document.querySelector('.no-search-results');
        
        if (found === 0) {
            if (!noResultsElement) {
                noResultsElement = document.createElement('div');
                noResultsElement.className = 'no-search-results';
                noResultsElement.innerHTML = `
                    <p>No se encontraron eventos que coincidan con "<strong>${query}</strong>".</p>
                    <button class="btn btn-secondary reset-search">Ver todos los eventos</button>
                `;
                eventsContainer.appendChild(noResultsElement);
                
                // Botón para resetear búsqueda
                const resetButton = noResultsElement.querySelector('.reset-search');
                resetButton.addEventListener('click', function() {
                    document.querySelector('#eventSearch').value = '';
                    eventItems.forEach(item => item.style.display = 'block');
                    noResultsElement.style.display = 'none';
                });
            } else {
                noResultsElement.style.display = 'block';
            }
        } else if (noResultsElement) {
            noResultsElement.style.display = 'none';
        }
    }, 500);
}

// Inicializar búsqueda de eventos
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('eventSearch');
    
    if (searchInput) {
        // Debounce para no ejecutar la búsqueda con cada pulsación
        const debouncedSearch = debounce(function() {
            searchEvents(searchInput.value);
        }, 500);
        
        // Ejecutar búsqueda al escribir
        searchInput.addEventListener('input', debouncedSearch);
        
        // Ejecutar búsqueda al enviar el formulario
        const searchForm = searchInput.closest('form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                searchEvents(searchInput.value);
            });
        }
    }
});