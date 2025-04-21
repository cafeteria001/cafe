/**
 * Main JavaScript for Bar/Cafetería
 * Este archivo contiene funciones generales utilizadas en todo el sitio web
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    // Inicializar componentes
    initMobileMenu();
    initAlerts();
    initScrollAnimation();
    initSmoothScroll();
    initFAQs();
});

/**
 * Inicializa el menú móvil
 */
function initMobileMenu() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const closeMenu = document.querySelector('.close-menu');
    
    if (menuToggle && mobileMenu && closeMenu) {
        // Abrir menú móvil
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevenir scroll del body
        });
        
        // Cerrar menú móvil
        closeMenu.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = ''; // Restaurar scroll del body
        });
        
        // Cerrar menú móvil al hacer clic en un enlace
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
        
        // Cerrar menú móvil al hacer clic fuera del menú
        document.addEventListener('click', function(event) {
            if (event.target !== mobileMenu && 
                !mobileMenu.contains(event.target) && 
                event.target !== menuToggle && 
                !menuToggle.contains(event.target)) {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
}

/**
 * Inicializa las alertas y permite cerrarlas
 */
function initAlerts() {
    const alerts = document.querySelectorAll('.alert');
    const closeButtons = document.querySelectorAll('.alert .close-alert');
    
    // Cerrar alertas al hacer clic en el botón de cierre
    if (closeButtons.length) {
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const alert = button.closest('.alert');
                if (alert) {
                    alert.style.display = 'none';
                }
            });
        });
    }
    
    // Auto-cerrar alertas después de 5 segundos
    if (alerts.length) {
        setTimeout(function() {
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);
    }
}

/**
 * Inicializa animaciones al hacer scroll
 */
function initScrollAnimation() {
    // Elementos a animar
    const animatedElements = document.querySelectorAll('.fade-in, .fade-up, .fade-right, .fade-left');
    
    if (animatedElements.length) {
        // Verificar si los elementos están en el viewport
        function checkInView() {
            animatedElements.forEach(function(element) {
                if (isElementInViewport(element) && !element.classList.contains('animated')) {
                    element.classList.add('animated');
                }
            });
        }
        
        // Ejecutar al cargar y al hacer scroll
        checkInView();
        window.addEventListener('scroll', checkInView);
        window.addEventListener('resize', checkInView);
    }
    
    // Función auxiliar para verificar si un elemento está en el viewport
    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8 &&
            rect.bottom >= 0 &&
            rect.left <= (window.innerWidth || document.documentElement.clientWidth) &&
            rect.right >= 0
        );
    }
}

/**
 * Inicializa el scroll suave para enlaces internos
 */
function initSmoothScroll() {
    const anchors = document.querySelectorAll('a[href^="#"]:not([href="#"])');
    
    if (anchors.length) {
        anchors.forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    const offsetTop = targetElement.getBoundingClientRect().top + window.pageYOffset;
                    
                    window.scrollTo({
                        top: offsetTop - 80, // Offset para el header fijo
                        behavior: 'smooth'
                    });
                    
                    // Actualizar URL hash sin causar scroll
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    } else {
                        location.hash = targetId;
                    }
                }
            });
        });
    }
}

/**
 * Inicializa el comportamiento de acordeón para FAQs
 */
function initFAQs() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (faqItems.length) {
        faqItems.forEach(function(item) {
            const question = item.querySelector('.faq-question');
            
            if (question) {
                question.addEventListener('click', function() {
                    const isActive = item.classList.contains('active');
                    
                    // Cerrar todos los items
                    faqItems.forEach(function(faq) {
                        faq.classList.remove('active');
                    });
                    
                    // Abrir el actual (si no estaba abierto)
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            }
        });
    }
}

/**
 * Función para validar un formulario genérico
 * @param {HTMLFormElement} form - Elemento del formulario a validar
 * @returns {boolean} - Verdadero si el formulario es válido
 */
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    // Limpiar mensajes de error previos
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(function(error) {
        error.remove();
    });
    
    // Verificar campos requeridos
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            isValid = false;
            showError(field, 'Este campo es obligatorio');
        } else if (field.type === 'email' && !isValidEmail(field.value)) {
            isValid = false;
            showError(field, 'Por favor, introduce un email válido');
        } else if (field.type === 'tel' && !isValidPhone(field.value)) {
            isValid = false;
            showError(field, 'Por favor, introduce un teléfono válido');
        }
    });
    
    return isValid;
}

/**
 * Muestra un mensaje de error debajo de un campo
 * @param {HTMLElement} field - Campo con error
 * @param {string} message - Mensaje de error
 */
function showError(field, message) {
    // Quitar clases de error previas
    field.classList.add('is-invalid');
    
    // Crear y añadir mensaje de error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    
    // Insertar después del campo
    field.parentNode.insertBefore(errorDiv, field.nextSibling);
}

/**
 * Valida un email
 * @param {string} email - Email a validar
 * @returns {boolean} - Verdadero si el email es válido
 */
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Valida un número de teléfono
 * @param {string} phone - Teléfono a validar
 * @returns {boolean} - Verdadero si el teléfono es válido
 */
function isValidPhone(phone) {
    const re = /^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,4}[-\s.]?[0-9]{1,9}$/;
    return re.test(String(phone));
}

/**
 * Formatea un número como moneda
 * @param {number} amount - Cantidad a formatear
 * @returns {string} - Cantidad formateada como moneda
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
        minimumFractionDigits: 2
    }).format(amount);
}

/**
 * Formatea una fecha
 * @param {string} dateString - Fecha en formato YYYY-MM-DD
 * @param {string} format - Formato deseado (short, medium, long)
 * @returns {string} - Fecha formateada
 */
function formatDate(dateString, format = 'medium') {
    const date = new Date(dateString);
    
    const options = {
        short: { day: '2-digit', month: '2-digit', year: 'numeric' },
        medium: { day: '2-digit', month: 'long', year: 'numeric' },
        long: { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }
    };
    
    return date.toLocaleDateString('es-ES', options[format]);
}

/**
 * Trunca un texto a una longitud máxima
 * @param {string} text - Texto a truncar
 * @param {number} maxLength - Longitud máxima
 * @returns {string} - Texto truncado
 */
function truncateText(text, maxLength) {
    if (text.length <= maxLength) {
        return text;
    }
    return text.substring(0, maxLength) + '...';
}

/**
 * Crea un elemento de "loading" para mostrar durante la carga de datos
 * @param {HTMLElement} container - Contenedor donde mostrar el loader
 * @returns {HTMLElement} - Elemento del loader
 */
function showLoader(container) {
    // Crear elemento de carga
    const loader = document.createElement('div');
    loader.className = 'loader';
    loader.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
    
    // Agregar al contenedor
    container.appendChild(loader);
    
    return loader;
}

/**
 * Elimina un elemento de "loading" de la página
 * @param {HTMLElement} loader - Elemento del loader a remover
 */
function hideLoader(loader) {
    if (loader && loader.parentNode) {
        loader.parentNode.removeChild(loader);
    }
}

/**
 * Realiza una petición AJAX con Fetch API
 * @param {string} url - URL a donde realizar la petición
 * @param {object} options - Opciones de la petición
 * @returns {Promise} - Promesa con la respuesta
 */
async function fetchData(url, options = {}) {
    try {
        const response = await fetch(url, options);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Error en la petición:', error);
        throw error;
    }
}

/**
 * Crea una notificación toast para mostrar mensajes temporales
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, error, warning, info)
 * @param {number} duration - Duración en ms (por defecto 3000)
 */
function showToast(message, type = 'info', duration = 3000) {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.querySelector('.toast-container');
    
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
    
    // Crear toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Agregar ícono según el tipo
    let icon = '';
    switch (type) {
        case 'success':
            icon = '<i class="fas fa-check-circle"></i>';
            break;
        case 'error':
            icon = '<i class="fas fa-times-circle"></i>';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-triangle"></i>';
            break;
        default:
            icon = '<i class="fas fa-info-circle"></i>';
    }
    
    // Contenido del toast
    toast.innerHTML = `
        <div class="toast-content">
            ${icon}
            <span>${message}</span>
        </div>
        <button class="toast-close"><i class="fas fa-times"></i></button>
    `;
    
    // Agregar al contenedor
    toastContainer.appendChild(toast);
    
    // Botón para cerrar
    const closeButton = toast.querySelector('.toast-close');
    closeButton.addEventListener('click', () => {
        toast.classList.add('toast-hide');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    });
    
    // Mostrar toast con animación
    setTimeout(() => {
        toast.classList.add('toast-show');
    }, 10);
    
    // Auto-cerrar después de la duración especificada
    setTimeout(() => {
        toast.classList.add('toast-hide');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, duration);
}

/**
 * Inicializa un slider/carrusel básico
 * @param {string} selector - Selector del contenedor del slider
 */
function initSlider(selector) {
    const slider = document.querySelector(selector);
    
    if (!slider) return;
    
    const slides = slider.querySelectorAll('.slide');
    const dotsContainer = slider.querySelector('.slider-dots');
    const prevButton = slider.querySelector('.slider-prev');
    const nextButton = slider.querySelector('.slider-next');
    
    let currentSlide = 0;
    const slideCount = slides.length;
    
    // Crear indicadores (dots) si existe el contenedor
    if (dotsContainer && slideCount > 1) {
        for (let i = 0; i < slideCount; i++) {
            const dot = document.createElement('span');
            dot.classList.add('slider-dot');
            if (i === 0) dot.classList.add('active');
            dot.dataset.slide = i;
            dotsContainer.appendChild(dot);
            
            // Evento click para navegar a un slide específico
            dot.addEventListener('click', () => {
                goToSlide(i);
            });
        }
    }
    
    // Mostrar un slide específico
    function goToSlide(index) {
        // Ocultar slide actual
        slides[currentSlide].classList.remove('active');
        
        // Actualizar dots
        if (dotsContainer) {
            const dots = dotsContainer.querySelectorAll('.slider-dot');
            dots[currentSlide].classList.remove('active');
            dots[index].classList.add('active');
        }
        
        // Mostrar nuevo slide
        currentSlide = index;
        slides[currentSlide].classList.add('active');
    }
    
    // Ir al siguiente slide
    function nextSlide() {
        let next = currentSlide + 1;
        if (next >= slideCount) next = 0;
        goToSlide(next);
    }
    
    // Ir al slide anterior
    function prevSlide() {
        let prev = currentSlide - 1;
        if (prev < 0) prev = slideCount - 1;
        goToSlide(prev);
    }
    
    // Eventos para botones
    if (prevButton) prevButton.addEventListener('click', prevSlide);
    if (nextButton) nextButton.addEventListener('click', nextSlide);
    
    // Auto-play si tiene la clase .autoplay
    if (slider.classList.contains('autoplay')) {
        setInterval(nextSlide, 5000);
    }
}

/**
 * Convierte un formulario en un objeto JavaScript
 * @param {HTMLFormElement} form - Formulario a convertir
 * @returns {object} - Objeto con los valores del formulario
 */
function formToObject(form) {
    const formData = new FormData(form);
    const obj = {};
    
    for (const [key, value] of formData.entries()) {
        obj[key] = value;
    }
    
    return obj;
}

/**
 * Debounce para limitar la frecuencia de ejecución de funciones
 * @param {Function} func - Función a ejecutar
 * @param {number} wait - Tiempo de espera en ms
 * @returns {Function} - Función con debounce aplicado
 */
function debounce(func, wait = 300) {
    let timeout;
    
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Detecta si se está navegando en un dispositivo móvil
 * @returns {boolean} - Verdadero si es un dispositivo móvil
 */
function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

/**
 * Detecta si se está navegando en modo oscuro
 * @returns {boolean} - Verdadero si está en modo oscuro
 */
function isDarkMode() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
}

/**
 * Aplica máscara a un número de teléfono mientras se escribe
 * @param {HTMLInputElement} input - Campo de entrada de teléfono
 */
function phoneMask(input) {
    input.addEventListener('input', function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    });
}