<?php
// Solo inicia la sesión si no ha sido iniciada previamente
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Asegúrate de tener la conexión a la base de datos
require_once 'config/database.php';
$db = new Database();
$con = $db->conectar();
include 'recibo.php';

// Verifica si hay productos en el carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    // Si el carrito está vacío, redirigir al catálogo
    header('Location: index.html');
    exit();
}

// Función para obtener un producto por su ID
function obtenerProductoPorId($id, $con) {
    $stmt = $con->prepare("SELECT * FROM productos WHERE ID_Producto = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['realizar_pago'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $metodo_pago = $_POST['metodo_pago'];
    $fecha_compra = date('Y-m-d H:i:s');

    // Cálculo del total de la compra
    $total_compra = 0;
    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        $producto = obtenerProductoPorId($id, $con);
        $total_compra += $producto['Precio_Venta'] * $cantidad;
    }

    // Inserta la compra en la tabla "compras"
    $stmt_compra = $con->prepare("INSERT INTO compras (Fecha_Compra, Total_Compra) VALUES (?, ?)");
    $stmt_compra->execute([$fecha_compra, $total_compra]);

    // Obtiene el ID de la compra recién insertada
    $id_compra = $con->lastInsertId();

    // Inserta cada detalle de compra en "detalle_compras"
    foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
        $producto = obtenerProductoPorId($id_producto, $con);
        $precio = $producto['Precio_Venta'];
        
        $stmt_detalle = $con->prepare("INSERT INTO detalle_compras (ID_Compra, ID_Producto, Cantidad_Comprada, Precio_Compra) VALUES (?, ?, ?, ?)");
        $stmt_detalle->execute([$id_compra, $id_producto, $cantidad, $precio]);
    }

    // Guardar detalles en variables de sesión para el recibo
    $_SESSION['id_compra'] = $id_compra;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['total'] = $total_compra;
    $_SESSION['metodo_pago'] = $metodo_pago;
    
    if ($metodo_pago == "tarjeta") {
        $_SESSION['numero_tarjeta'] = $_POST['numero_tarjeta'];
        $_SESSION['nombre_tarjeta'] = $_POST['nombre_tarjeta'];
    }

    // Limpia el carrito después de la compra
    unset($_SESSION['carrito']);

    // Redirigir a la página del recibo
    header("Location: recibo.php");
    exit();
}


// Función para actualizar la cantidad de un producto en el carrito
if (isset($_POST['actualizar_cantidad'])) {
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    if ($cantidad > 0) {
        $_SESSION['carrito'][$id_producto] = $cantidad;
    } else {
        unset($_SESSION['carrito'][$id_producto]); // Eliminar producto si la cantidad es 0
    }
    header("Location: checkout.php");
    exit();
}

// Función para eliminar un producto del carrito
if (isset($_GET['eliminar'])) {
    $id_producto = $_GET['eliminar'];
    unset($_SESSION['carrito'][$id_producto]);
    header("Location: checkout.php");
    exit();
}

$total = 0;
$productos = []; // Array para almacenar los productos en el carrito

// Recorre el carrito y obtiene los productos
if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        $producto = obtenerProductoPorId($id, $con); 
        $productos[] = $producto;
        $total += $producto['Precio_Venta'] * $cantidad;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fantasy Glow - Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .navbar {
            background-color: #333;
        }

        .cart-item img {
            max-width: 80px;
            height: auto;
            margin-right: 15px;
        }

        .cart-item {
            margin-bottom: 1rem;
        }

        .checkout-button {
            background-color: #42A5F5;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.2rem;
            width: 100%;
            cursor: pointer;
            margin-top: 20px;
        }

        .checkout-button:hover {
            background-color: #1e88e5;
        }

        .hero {
            text-align: center;
            padding: 40px;
            background: #66d4c0;
            color: #fff;
        }

        .cart-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-summary h4 {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .cart-item h5 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .cart-item p {
            margin: 0;
            font-size: 0.9rem;
            color: #555;
        }

        .cart-total {
            font-size: 1.3rem;
            font-weight: bold;
        }

        .form-control {
            margin-bottom: 10px;
        }
        /* Estilo para las imágenes en el Checkout */
.cart-item img {
    width: 100px;  /* Establecemos un ancho fijo */
    height: 100px; /* Y una altura fija para hacerlas uniformes */
    object-fit: cover; /* Mantiene la proporción sin distorsionar */
    border-radius: 8px; /* Opcional: hace que las imágenes tengan bordes redondeados */
}

/* Si el checkout tiene más detalles */
.checkout-img {
    width: 150px;  /* Puedes ajustar el tamaño si es necesario */
    height: 150px;
    object-fit: cover;
    margin-right: 15px;
}
.btn-xs {
            font-size: 0.65rem;  /* Fuente aún más pequeña */
            padding: 0.15rem 0.3rem;  /* Padding reducido */
        }
        main{
            margin-top: 20px;
        }
        


    </style>
</head>
<body>

    <!-- Navbar -->
    <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a href="index.html" class="navbar-brand">Fantasy Glow</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarHeader">
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
                        <span class="badge" id="carrito-count">
                            <?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h2>Resumen de tu compra</h2>
        <p>¡Revisa tu carrito y procede al pago!</p>
    </section>

    <!-- Carrito -->
    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="cart-summary">
                        <h3>Artículos en tu carrito</h3>

                        <!-- Lista de productos en el carrito -->
                        <?php foreach ($productos as $producto): ?>
                            <div class="cart-item d-flex">
                                <img src="images/productos/<?php echo $producto['ID_Producto']; ?>/princi.jpg" alt="<?php echo $producto['Nombre_Producto']; ?>">
                                <div>
                                    <h5><?php echo $producto['Nombre_Producto']; ?></h5>
                                    <p>Cantidad: <?php echo $_SESSION['carrito'][$producto['ID_Producto']]; ?></p>
                                    <p>Precio: $<?php echo $producto['Precio_Venta']; ?></p>
                                    <!-- Formulario para actualizar la cantidad -->
                                    <form method="POST" action="checkout.php">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['ID_Producto']; ?>">
                                        <input type="number" name="cantidad" value="<?php echo $_SESSION['carrito'][$producto['ID_Producto']]; ?>" min="1" class="form-control" style="width: 100px;">
                                        <button type="submit" name="actualizar_cantidad" class="btn btn-primary btn-xs">Actualizar</button>
                                    </form>
                                    <!-- Enlace para eliminar el producto -->
                                    <a href="checkout.php?eliminar=<?php echo $producto['ID_Producto']; ?>" class="btn btn-danger btn-xs">Eliminar</a>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Total -->
                        <h4 class="cart-total">Total: $<?php echo number_format($total, 2); ?></h4>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="cart-summary">
                    <h3>Detalles de Facturación</h3>
                        <!-- Formulario de facturación y selección de método de pago -->
<form method="POST" action="checkout.php">
    <div class="form-group">
        <label for="nombre">Nombre Completo</label>
        <input type="text" id="nombre" name="nombre" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="direccion">Dirección de Envío</label>
        <input type="text" id="direccion" name="direccion" class="form-control" required>
    </div>

    <!-- Selección del método de pago -->
    <div class="form-group">
        <label for="metodo_pago">Método de pago</label>
        <select name="metodo_pago" id="metodo_pago" class="form-control" required>
            <option value="" disabled selected>...</option>
            <option value="tarjeta">Tarjeta de Crédito/Débito</option>
            <option value="transferencia">Transferencia Bancaria</option>
        </select>
    </div>
    <!-- Campos adicionales para tarjeta de crédito/débito -->
    <div id="campos-tarjeta" style="display: none;">
                                <div class="form-group">
                                    <label for="numero_tarjeta">Número de Tarjeta</label>
                                    <input type="text" id="numero_tarjeta" name="numero_tarjeta" class="form-control" pattern="\d{16}" placeholder="1234 5678 9012 3456" maxlength="16">
                                </div>
                                <div class="form-group">
                                    <label for="nombre_tarjeta">Nombre en la Tarjeta</label>
                                    <input type="text" id="nombre_tarjeta" name="nombre_tarjeta" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="nip_tarjeta">NIP</label>
                                    <input type="password" id="nip_tarjeta" name="nip_tarjeta" class="form-control" maxlength="4" placeholder="**" pattern="\d{4}">
                                </div>
                                <div class="form-group">
                                    <label for="ccv">CCV</label>
                                    <input type="text" id="ccv" name="ccv" class="form-control" maxlength="3" placeholder="123" pattern="\d{3}">
                                </div>
                        </div>
                        <!-- Campos adicionales para Transferencia -->
    <div id="campos_transferencia" style="display: none;">
        <p>Instrucciones: Realiza una transferencia a la cuenta N°: 1234567890.</p>
    </div>

    <button type="submit" name="realizar_pago" class="btn btn-primary">Realizar el Pago</button>
</form>
    </div>
</form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
    // Mostrar u ocultar los campos según el método de pago seleccionado
    document.getElementById('metodo_pago').addEventListener('change', function () {
        var metodoPago = this.value;
        
        if (metodoPago == 'tarjeta') {
            document.getElementById('campos-tarjeta').style.display = 'block';
            document.getElementById('campos_transferencia').style.display = 'none';
        } else if (metodoPago == 'transferencia') {
            document.getElementById('campos-tarjeta').style.display = 'none';
            document.getElementById('campos_transferencia').style.display = 'block';
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
