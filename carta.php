<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Definir variables para el header
$titulo_pagina = 'Nuestra Carta';
$descripcion_pagina = 'Descubre nuestra amplia selección de cafés, panes, pasteles y más.';
$css_adicional = ['carta.css'];
$js_adicional = ['carta.js'];

// Obtener todas las categorías
$sql_categorias = "SELECT * FROM categorias ORDER BY nombre ASC";
$resultado_categorias = ejecutarConsulta($conn, $sql_categorias);
$categorias = obtenerFilas($resultado_categorias);

// Obtener filtro de categoría si existe
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

// Consulta para productos
$sql_productos = "SELECT p.*, c.nombre as categoria FROM productos p 
                 INNER JOIN categorias c ON p.categoria_id = c.id 
                 WHERE p.disponible = 1 ";

// Agregar filtro por categoría si está seleccionada
if ($categoria_id > 0) {
    $sql_productos .= " AND p.categoria_id = $categoria_id ";
}

// Ordenar productos
$sql_productos .= " ORDER BY c.nombre ASC, p.nombre ASC";

$resultado_productos = ejecutarConsulta($conn, $sql_productos);
$productos = obtenerFilas($resultado_productos);

// Agrupar productos por categoría
$productos_por_categoria = [];
foreach ($productos as $producto) {
    $productos_por_categoria[$producto['categoria_id']][] = $producto;
}

// Incluir el header
include_once 'includes/header.php';
?>

<!-- Banner de la Carta -->
<section class="page-banner" style="background-image: url('assets/img/menu-banner.jpg');">
    <div class="container">
        <div class="banner-content">
            <h1>Nuestra Carta</h1>
            <p>Descubre el sabor de nuestros productos artesanales</p>
        </div>
    </div>
</section>

<!-- Filtros de Categorías -->
<section class="category-filter">
    <div class="container">
        <div class="filter-buttons">
            <a href="<?php echo url('carta.php'); ?>" class="filter-btn <?php echo $categoria_id === 0 ? 'active' : ''; ?>">Todos</a>
            <?php foreach ($categorias as $categoria): ?>
                <a href="<?php echo url('carta.php?categoria=' . $categoria['id']); ?>" 
                   class="filter-btn <?php echo $categoria_id === (int)$categoria['id'] ? 'active' : ''; ?>">
                    <?php echo $categoria['nombre']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Productos por Categoría -->
<section class="menu-section">
    <div class="container">
        <?php if (empty($productos)): ?>
            <div class="no-results">
                <p>No hay productos disponibles en esta categoría.</p>
            </div>
        <?php else: ?>
            <?php if ($categoria_id === 0): ?>
                <!-- Mostrar por categorías cuando no hay filtro -->
                <?php foreach ($categorias as $categoria): ?>
                    <?php if (isset($productos_por_categoria[$categoria['id']])): ?>
                        <div class="menu-category">
                            <h2><?php echo $categoria['nombre']; ?></h2>
                            <?php if (!empty($categoria['descripcion'])): ?>
                                <p class="category-desc"><?php echo $categoria['descripcion']; ?></p>
                            <?php endif; ?>
                            
                            <div class="products-list">
                                <?php foreach ($productos_por_categoria[$categoria['id']] as $producto): ?>
                                    <div class="product-item">
                                        <div class="product-image">
                                            <?php if (!empty($producto['imagen'])): ?>
                                                <img src="<?php echo url('uploads/productos/' . $producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>">
                                            <?php else: ?>
                                                <img src="<?php echo url('assets/img/product-default.jpg'); ?>" alt="<?php echo $producto['nombre']; ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-details">
                                            <h3><?php echo $producto['nombre']; ?></h3>
                                            <p class="product-description"><?php echo $producto['descripcion']; ?></p>
                                            <span class="product-price">$<?php echo number_format($producto['precio'], 2); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Mostrar solo los productos de la categoría seleccionada -->
                <div class="menu-category">
                    <?php 
                    // Obtener nombre de la categoría seleccionada
                    $categoria_seleccionada = '';
                    foreach ($categorias as $cat) {
                        if ($cat['id'] == $categoria_id) {
                            $categoria_seleccionada = $cat;
                            break;
                        }
                    }
                    ?>
                    
                    <h2><?php echo $categoria_seleccionada['nombre']; ?></h2>
                    <?php if (!empty($categoria_seleccionada['descripcion'])): ?>
                        <p class="category-desc"><?php echo $categoria_seleccionada['descripcion']; ?></p>
                    <?php endif; ?>
                    
                    <div class="products-list">
                        <?php foreach ($productos as $producto): ?>
                            <div class="product-item">
                                <div class="product-image">
                                    <?php if (!empty($producto['imagen'])): ?>
                                        <img src="<?php echo url('uploads/productos/' . $producto['imagen']); ?>" alt="<?php echo $producto['nombre']; ?>">
                                    <?php else: ?>
                                        <img src="<?php echo url('assets/img/product-default.jpg'); ?>" alt="<?php echo $producto['nombre']; ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="product-details">
                                    <h3><?php echo $producto['nombre']; ?></h3>
                                    <p class="product-description"><?php echo $producto['descripcion']; ?></p>
                                    <span class="product-price">$<?php echo number_format($producto['precio'], 2); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Promociones Especiales -->
<section class="special-offers">
    <div class="container">
        <div class="section-header">
            <h2>Promociones Especiales</h2>
            <p>Aprovecha nuestras ofertas por tiempo limitado</p>
        </div>
        
        <div class="offers-grid">
            <div class="offer-card">
                <div class="offer-image">
                    <img src="assets/img/promo-1.jpg" alt="Promoción Desayunos">
                    <div class="offer-tag">Lunes a Viernes</div>
                </div>
                <div class="offer-content">
                    <h3>Desayuno Completo</h3>
                    <p>Café, zumo y croissant o tostada por solo $5.99. De 8:00 AM a 11:00 AM.</p>
                    <a href="#" class="btn btn-outline">Ver Detalles</a>
                </div>
            </div>
            
            <div class="offer-card">
                <div class="offer-image">
                    <img src="assets/img/promo-2.jpg" alt="Promoción Happy Hour">
                    <div class="offer-tag">Jueves y Viernes</div>
                </div>
                <div class="offer-content">
                    <h3>Happy Hour</h3>
                    <p>2x1 en bebidas selectas. De 7:00 PM a 9:00 PM.</p>
                    <a href="#" class="btn btn-outline">Ver Detalles</a>
                </div>
            </div>
            
            <div class="offer-card">
                <div class="offer-image">
                    <img src="assets/img/promo-3.jpg" alt="Promoción Fin de Semana">
                    <div class="offer-tag">Sábado y Domingo</div>
                </div>
                <div class="offer-content">
                    <h3>Brunch Familiar</h3>
                    <p>20% de descuento en el menú de brunch para familias. De 11:00 AM a 3:00 PM.</p>
                    <a href="#" class="btn btn-outline">Ver Detalles</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Llamado a la Acción de Reserva -->
<section class="menu-cta" style="background-image: url('assets/img/menu-cta-bg.jpg');">
    <div class="container">
        <div class="cta-content">
            <h2>¿Te ha gustado nuestra carta?</h2>
            <p>Reserva tu mesa ahora mismo y disfruta de una experiencia gastronómica única.</p>
            <a href="<?php echo url('reservas.php'); ?>" class="btn btn-primary">Reservar Mesa</a>
        </div>
    </div>
</section>

<?php
// Cerrar conexión a la base de datos
cerrarConexion($conn);

// Incluir el footer
include_once 'includes/footer.php';
?>