<?php
session_start();


// Verificar si ya existe la sesión del carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Obtener el ID del producto
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Agregar el producto al carrito
if ($product_id > 0) {
    if (isset($_SESSION['carrito'][$product_id])) {
        $_SESSION['carrito'][$product_id]++;
    } else {
        $_SESSION['carrito'][$product_id] = 1;
    }
}

// Redirigir de vuelta
echo "Producto agregado al carrito";
?>

