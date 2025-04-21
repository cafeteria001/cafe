</main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">
                        <img src="<?php echo url('assets/img/logo-white.png'); ?>" alt="<?php echo SITE_NAME; ?>">
                    </div>
                    <p>Un lugar acogedor donde disfrutar de los mejores cafés, panes artesanales y un ambiente inigualable.</p>
                    <div class="social-media">
                        <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="footer-contact">
                    <h3>Contacto</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Av. Principal 123, Ciudad</li>
                        <li><i class="fas fa-phone"></i> +123 456 7890</li>
                        <li><i class="fas fa-envelope"></i> info@barcafe.com</li>
                    </ul>
                </div>
                
                <div class="footer-hours">
                    <h3>Horario</h3>
                    <ul>
                        <li><span>Lunes - Viernes:</span> 8:00 AM - 10:00 PM</li>
                        <li><span>Sábado:</span> 9:00 AM - 11:00 PM</li>
                        <li><span>Domingo:</span> 9:00 AM - 8:00 PM</li>
                    </ul>
                </div>
                
                <div class="footer-newsletter">
                    <h3>Suscríbete</h3>
                    <p>Recibe nuestras novedades y promociones</p>
                    <form action="#" method="post" class="newsletter-form">
                        <input type="email" name="email" placeholder="Tu correo electrónico" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos los derechos reservados.</p>
                <ul>
                    <li><a href="#">Política de Privacidad</a></li>
                    <li><a href="#">Términos y Condiciones</a></li>
                </ul>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?php echo url('assets/js/main.js'); ?>"></script>
    
    <?php if (isset($js_adicional) && is_array($js_adicional)): ?>
        <?php foreach ($js_adicional as $js): ?>
            <script src="<?php echo url('assets/js/' . $js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Cerrar alerta
        document.querySelectorAll('.close-alert').forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.closest('.alert').style.display = 'none';
            });
        });
        
        // Menu móvil
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.mobile-menu').classList.add('active');
        });
        
        document.querySelector('.close-menu').addEventListener('click', function() {
            document.querySelector('.mobile-menu').classList.remove('active');
        });
    </script>
</body>
</html>