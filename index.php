<?php
// Incluir archivos necesarios
include_once 'includes/config.php';
include_once 'includes/header.php';
?>

<!-- Banner Principal -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Bienvenido a nuestro Café & Bar</h1>
            <p>Un espacio donde la gastronomía y el buen ambiente se combinan para brindarte una experiencia única.</p>
            <div class="hero-buttons">
                <a href="carta.php" class="btn btn-secondary">Ver Carta</a>
                <a href="reservas.php" class="btn">Reservar Mesa</a>
            </div>
        </div>
    </div>
</section>

<!-- Características -->
<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature">
                <i class="fas fa-coffee"></i>
                <h3>Café de Especialidad</h3>
                <p>Los mejores granos seleccionados para brindarte una experiencia única.</p>
            </div>
            <div class="feature">
                <i class="fas fa-bread-slice"></i>
                <h3>Panadería Artesanal</h3>
                <p>Pan y repostería elaborados diariamente con ingredientes naturales.</p>
            </div>
            <div class="feature">
                <i class="fas fa-music"></i>
                <h3>Eventos en Vivo</h3>
                <p>Disfruta de música y recitales en un ambiente acogedor.</p>
            </div>
            <div class="feature">
                <i class="fas fa-utensils"></i>
                <h3>Variedad Gastronómica</h3>
                <p>Desde desayunos hasta cenas, tenemos opciones para todos los gustos.</p>
            </div>
        </div>
    </div>
</section>

<!-- Acerca de -->
<section class="about">
    <div class="container">
        <div class="about-content">
            <div class="about-image">
                <img src="assets/img/about.jpg" alt="Sobre nosotros">
            </div>
            <div class="about-text">
                <h2>Nuestra Historia</h2>
                <p>Fundado en 2015, <?php echo SITE_NAME; ?> nació con la visión de crear un espacio donde la comunidad pudiera reunirse para disfrutar de buenos momentos acompañados de excelente comida y bebida.</p>
                <p>Nos enorgullecemos de ofrecer productos de la más alta calidad, elaborados con ingredientes locales y técnicas tradicionales que resaltan los sabores auténticos.</p>
                <a href="#" class="btn btn-outline">Conocer Más</a>
            </div>
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<section class="featured-products">
    <div class="container">
        <div class="section-header">
            <h2>Nuestros Destacados</h2>
            <p>Descubre nuestros productos más populares</p>
        </div>
        
        <div class="products-grid">
            <?php if (!empty($productos_destacados)): ?>
                <?php foreach ($productos_destacados as $producto): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($producto['imagen'])): ?>
                                <img src="<?php echo url('uploads/productos/' . $producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>">
                            <?php else: ?>
                                <img src="<?php echo url('assets/img/product-default.jpg'); ?>" alt="<?php echo $producto['nombre']; ?>">
                            <?php endif; ?>
                            <div class="product-category"><?php echo $producto['categoria']; ?></div>
                        </div>
                        <div class="product-info">
                            <h3><?php echo $producto['nombre']; ?></h3>
                            <p><?php echo substr($producto['descripcion'], 0, 80) . (strlen($producto['descripcion']) > 80 ? '...' : ''); ?></p>
                            <div class="product-price">$<?php echo number_format($producto['precio'], 2); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No hay productos destacados disponibles actualmente.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center">
            <a href="<?php echo url('carta.php'); ?>" class="btn btn-secondary">Ver Carta Completa</a>
        </div>
    </div>
</section>

<!-- Eventos -->
<section class="events">
    <div class="container">
        <div class="section-header">
            <h2>Próximos Eventos</h2>
            <p>No te pierdas nuestras actividades y presentaciones en vivo</p>
        </div>
        
        <div class="events-grid">
            <?php if (!empty($proximos_eventos)): ?>
                <?php foreach ($proximos_eventos as $evento): ?>
                    <div class="event-card">
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
                        </div>
                        <div class="event-info">
                            <h3><?php echo $evento['titulo']; ?></h3>
                            <div class="event-meta">
                                <span><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora'])); ?></span>
                                <?php if ($evento['precio_entrada'] > 0): ?>
                                    <span><i class="fas fa-ticket-alt"></i> $<?php echo number_format($evento['precio_entrada'], 2); ?></span>
                                <?php else: ?>
                                    <span><i class="fas fa-ticket-alt"></i> Entrada libre</span>
                                <?php endif; ?>
                            </div>
                            <p><?php echo substr($evento['descripcion'], 0, 100) . (strlen($evento['descripcion']) > 100 ? '...' : ''); ?></p>
                            <a href="<?php echo url('eventos.php?id=' . $evento['id']); ?>" class="btn btn-sm">Más Información</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No hay eventos programados próximamente.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center">
            <a href="<?php echo url('eventos.php'); ?>" class="btn btn-secondary">Ver Todos los Eventos</a>
        </div>
    </div>
</section>

<!-- Reservaciones -->
<section class="reservation-cta" style="background-image: url('assets/img/reservation-bg.jpg');">
    <div class="container">
        <div class="reservation-content">
            <h2>¿Listo para visitarnos?</h2>
            <p>Reserva tu mesa ahora y disfruta de una experiencia gastronómica única.</p>
            <a href="<?php echo url('reservas.php'); ?>" class="btn btn-primary">Reservar Mesa</a>
        </div>
    </div>
</section>

<!-- Testimonios -->
<section class="testimonials">
    <div class="container">
        <div class="section-header">
            <h2>Lo que dicen nuestros clientes</h2>
            <p>Experiencias de quienes ya nos han visitado</p>
        </div>
        
        <div class="testimonials-slider">
            <div class="testimonial">
                <div class="testimonial-content">
                    <p>"Excelente lugar para disfrutar de un buen café. El ambiente es muy acogedor y el servicio es de primera."</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/img/testimonial-1.jpg" alt="Cliente">
                    <div>
                        <h4>María Rodríguez</h4>
                        <span>Cliente frecuente</span>
                    </div>
                </div>
            </div>
            
            <div class="testimonial">
                <div class="testimonial-content">
                    <p>"Los eventos en vivo son increíbles. Además, tienen la mejor repostería de la ciudad, todo fresco y delicioso."</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/img/testimonial-2.jpg" alt="Cliente">
                    <div>
                        <h4>Carlos Mendoza</h4>
                        <span>Cliente regular</span>
                    </div>
                </div>
            </div>
            
            <div class="testimonial">
                <div class="testimonial-content">
                    <p>"El sistema de reservas online es muy práctico y el personal siempre está atento a cualquier necesidad."</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/img/testimonial-3.jpg" alt="Cliente">
                    <div>
                        <h4>Laura Gómez</h4>
                        <span>Cliente nueva</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Instagram Feed -->
<section class="instagram-feed">
    <div class="container">
        <div class="section-header">
            <h2>Síguenos en Instagram</h2>
            <p>@cafebarpan</p>
        </div>
        
        <div class="instagram-grid">
            <a href="#" class="instagram-item" style="background-image: url('assets/img/instagram-1.jpg');"></a>
            <a href="#" class="instagram-item" style="background-image: url('assets/img/instagram-2.jpg');"></a>
            <a href="#" class="instagram-item" style="background-image: url('assets/img/instagram-3.jpg');"></a>
            <a href="#" class="instagram-item" style="background-image: url('assets/img/instagram-4.jpg');"></a>
            <a href="#" class="instagram-item" style="background-image: url('assets/img/instagram-5.jpg');"></a>
            <a href="#" class="instagram-item" style="background-image: url('assets/img/instagram-6.jpg');"></a>
        </div>
    </div>
</section>

<!-- Contacto Rápido -->
<section class="quick-contact">
    <div class="container">
        <div class="quick-contact-content">
            <div class="contact-info">
                <h2>¿Tienes alguna pregunta?</h2>
                <p>Contáctanos directamente o visítanos en nuestro local.</p>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> Av. Principal 123, Ciudad</li>
                    <li><i class="fas fa-phone"></i> +123 456 7890</li>
                    <li><i class="fas fa-envelope"></i> info@barcafe.com</li>
                </ul>
            </div>
            
            <form class="contact-form" action="#" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                <div class="form-group">
                    <input type="text" name="nombre" placeholder="Tu nombre" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Tu correo electrónico" required>
                </div>
                <div class="form-group">
                    <textarea name="mensaje" placeholder="Tu mensaje" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
            </form>
        </div>
    </div>
</section>

<?php
// Cerrar conexión a la base de datos
cerrarConexion($conn);

// Incluir el footer
include_once 'includes/footer.php';
?>