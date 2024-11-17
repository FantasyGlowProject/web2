<?php
session_start();

// Verificar si el carrito existe
if (isset($_SESSION['carrito'])) {
    // Contar la cantidad total de productos en el carrito
    echo array_sum($_SESSION['carrito']);
} else {
    echo 0;
}
?>
