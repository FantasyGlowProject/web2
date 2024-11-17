<?php
require 'config/configu.php';
require 'config/database.php';
session_start();

// Conexión a la base de datos
$db = new Database();
$con = $db->conectar();

// Obtener el ID del producto desde la URL
$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Consultar el producto en la base de datos
$sql = $con->prepare("SELECT * FROM productos WHERE ID_Producto = :id_producto");
$sql->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
$sql->execute();
$product = $sql->fetch(PDO::FETCH_ASSOC);

// Si el producto no existe, redirigir al catálogo
if (!$product) {
    header('Location: index.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .product-details {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            background-color: #42A5F5;
            border-color: #42A5F5;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
        }

        .btn-custom:hover {
            background-color: #3b90d6;
            border-color: #3b90d6;
        }

        .btn-danger-custom {
            background-color: #e53935;
            border-color: #e53935;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
        }

        .btn-danger-custom:hover {
            background-color: #d32f2f;
            border-color: #d32f2f;
        }

        /* Estilo para el ícono del carrito */
        .cart-icon {
            font-size: 1.5rem;
            color: white;
        }

        .cart-icon .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e53935;
            color: white;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 50%;
        }
        .btn {
            background-color: #B39DDB;
            border-color: #42A5F5;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación superior con ícono de carrito -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.html">Fantasy Glow</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                            <a href="index.html" class="nav-link">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a href="contacto.html" class="nav-link">Contacto</a>
                        </li>
                    </ul>

                    <!-- Carrito de compras -->
                    <a href="checkout.php" class="nav-link position-relative">
                        <i class="fas fa-shopping-cart cart-icon"></i>
                        <span class="badge">
                            <?php 
                            // Mostrar el número de productos en el carrito
                            echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0;
                            ?>
                        </span>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mt-5">
        <div class="row">
            <!-- Imagen del producto -->
            <div class="col-md-6">
                <?php 
                // Ruta de la imagen del producto
                $imagen_producto = "images/productos/{$product['ID_Producto']}/princi.jpg";
                
                // Si la imagen no existe, mostrar una imagen por defecto
                if (!file_exists($imagen_producto)) {
                    $imagen_producto = "images/no-photo.jpg"; // Imagen por defecto
                }
                ?>
                <img src="<?php echo $imagen_producto; ?>" alt="<?php echo $product['Nombre_Producto']; ?>" class="product-image">
            </div>

            <!-- Detalles del producto -->
            <div class="col-md-6">
                <div class="product-details">
                    <h2><?php echo $product['Nombre_Producto']; ?></h2>
                    <p><strong>Precio:</strong> $<?php echo number_format($product['Precio_Venta'], 2); ?></p>
                    <p><strong>Descripción:</strong></p>
                    <p><?php echo $product['Descripción']; ?></p>
                    <div class="d-flex justify-content-between mt-4">
                        <!-- Botón Agregar al Carrito -->
                        <a href="carrito.php?id=<?php echo $product['ID_Producto']; ?>" class="btn btn-custom">Agregar al carrito</a>

                

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



