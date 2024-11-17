<?php
require 'config/configu.php';
require 'config/database.php';

// Conectar a la base de datos
$db = new Database();
$con = $db->conectar();

// Obtener los productos desde la base de datos
$sql = $con->prepare("SELECT ID_Producto, Nombre_Producto, Precio_Venta FROM productos");
$sql->execute();
$productos = $sql->fetchAll(PDO::FETCH_ASSOC);

// Devolver los productos como JSON
echo json_encode($productos);
?>

