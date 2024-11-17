<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<body>
    
</body>
</html>
<?php

// recibo.php

// Solo inicia la sesión si no ha sido iniciada previamente
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
$db = new Database();
$con = $db->conectar();

function generarReciboTarjeta($id_compra, $nombre, $total, $numero_tarjeta, $nombre_tarjeta, $fecha_entrega, $hora_entrega_inicio, $hora_entrega_fin) {
    echo "
    <style>
        .recibo-container {
            max-width: 600px;
            margin: 50px auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }
        .recibo-header {
            background-color: #1e2a78;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .recibo-header h3 {
            margin: 0;
            font-size: 1.8em;
            font-weight: 700;
            letter-spacing: 1px;
        }
            .recibo-header .icono-tarjeta {
            font-size: 2.5em;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .recibo-body {
            padding: 20px;
            line-height: 1.6;
        }
        .recibo-body p {
            margin: 15px 0;
            font-size: 1.1em;
        }
        .recibo-footer {
            background-color: #f1f5fa;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
            color: #666;
            border-top: 1px solid #e0e6ed;
            border-radius: 0 0 10px 10px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .recibo-label {
            font-weight: bold;
            color: #555;
        }
        .success-message {
            color: #28a745;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
        .recibo-highlight {
            background-color: #e8eff5;
            padding: 10px;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 600;
        }
            .accept-button {
            background-color: #1e2a78;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .accept-button:hover {
            background-color: #143a56;
        }
            /* Estilos responsivos para pantallas pequeñas */
    @media (max-width: 768px) {
        .recibo-container {
            padding: 15px;
            font-size: 1.2em; /* Aumenta el tamaño de fuente en móviles */
        }

        .recibo-header h3 {
            font-size: 1.4em;
        }

        .info-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .recibo-label, .recibo-highlight {
            width: 100%;
            text-align: left;
        }

        .accept-button {
            padding: 15px;
            font-size: 1.1em;
        }
    }
    </style>
    
    <div class='recibo-container'>
        <div class='recibo-header'>
        <div class='icono-tarjeta'>💳</div>
            <h3>Recibo de Pago con Tarjeta</h3>
        </div>
        <div class='recibo-body'>
        <div class='info-section'>
                <span class='recibo-label'>Fecha de entrega estimada:</span>
                <span class='recibo-highlight'>$fecha_entrega</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Horas de entrega estimada:</span>
                <span class='recibo-highlight'>$hora_entrega_inicio-$hora_entrega_fin</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Compra N°:</span>
                <span class='recibo-highlight'>$id_compra</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Nombre del Cliente:</span>
                <span class='recibo-highlight'>$nombre</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Total:</span>
                <span class='recibo-highlight'>$" . number_format($total, 2) . "</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Número de Tarjeta:</span>
                <span class='recibo-highlight'>** " . substr($numero_tarjeta, -4) . "</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Nombre en la Tarjeta:</span>
                <span class='recibo-highlight'>$nombre_tarjeta</span>
            </div>
            <p class='success-message'>¡Pago realizado con éxito!</p>
        </div>
        <div class='recibo-footer'>
            <p>Fantasy Glow - Su tienda de confianza</p>
            <form action='reiniciar_sesion.php' method='POST'>
        <button class='accept-button' onclick='window.location.href=\"index.html\"'>Aceptar</button>
        </div>
    </div>
    </form>
    ";
}

function generarReciboTransferencia($id_compra, $nombre, $total, $fecha_entrega, $hora_entrega_fin, $hora_entrega_inicio) {
    echo "
    <style>
        .recibo-container {
            max-width: 600px;
            margin: 50px auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }
        .recibo-header {
            background-color: #1e7052;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .recibo-header h3 {
            margin: 0;
            font-size: 1.8em;
            font-weight: 700;
            letter-spacing: 1px;
        }
            .recibo-header .icono-banco {
            font-size: 2.5em;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .recibo-body {
            padding: 20px;
            line-height: 1.6;
        }
        .recibo-body p {
            margin: 15px 0;
            font-size: 1.1em;
        }
        .recibo-footer {
            background-color: #f1f5fa;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
            color: #666;
            border-top: 1px solid #e0e6ed;
            border-radius: 0 0 10px 10px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .recibo-label {
            font-weight: bold;
            color: #555;
        }
        .warning-message {
            color: #d9534f;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
        .recibo-highlight {
            background-color: #e8eff5;
            padding: 10px;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 600;
        }
            .accept-button {
            background-color: #1e2a78;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .accept-button:hover {
            background-color: #143a56;
        }
        /* Estilos responsivos para pantallas pequeñas */
    @media (max-width: 768px) {
        .recibo-container {
            padding: 15px;
            font-size: 1.2em; /* Aumenta el tamaño de fuente en móviles */
        }

        .recibo-header h3 {
            font-size: 1.4em;
        }

        .info-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .recibo-label, .recibo-highlight {
            width: 100%;
            text-align: left;
        }

        .accept-button {
            padding: 15px;
            font-size: 1.1em;
        }
    }
    </style>
    
    <div class='recibo-container'>
        <div class='recibo-header'>
        <div class='icono-banco'>🏦</div>
            <h3>Recibo de Pago por Transferencia</h3>
        </div>
        <div class='recibo-body'>
        <div class='info-section'>
                <span class='recibo-label'>Fecha de entrega estimada:</span>
                <span class='recibo-highlight'>$fecha_entrega</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Horas de entrega estimada:</span>
                <span class='recibo-highlight'>$hora_entrega_inicio-$hora_entrega_fin</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Compra N°:</span>
                <span class='recibo-highlight'>$id_compra</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Nombre del Cliente:</span>
                <span class='recibo-highlight'>$nombre</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Total:</span>
                <span class='recibo-highlight'>$" . number_format($total, 2) . "</span>
            </div>
            <div class='info-section'>
                <span class='recibo-label'>Número de Cuenta para Transferencia:</span>
                <span class='recibo-highlight'>1234 5678 9012 3456</span>
            </div>
            <p class='warning-message'>Realice el depósito y envíe el comprobante a nuestro correo electronico.</p>
        </div>
        <div class='recibo-footer'>
            <p>Fantasy Glow - Su tienda de confianza</p>
            <form action='reiniciar_sesion.php' method='POST'>
        <button class='accept-button' onclick='window.location.href=\"index.html\"'>Aceptar</button>
        </div>
    </div>
    </form>
    ";
}
// Definir la fecha y hora del pedido
$fecha_actual = date('Y-m-d'); // Fecha actual en formato 'YYYY-MM-DD'
$hora_actual = date('H:i');    // Hora actual en formato 'HH:MM'

// Agregar días a la fecha actual para obtener la fecha de entrega estimada
$dias_para_entrega = 5; // Número de días que deseas añadir (puedes cambiarlo)
$fecha_entrega = date('Y-m-d', strtotime("+$dias_para_entrega days", strtotime($fecha_actual)));

// Generar un rango aleatorio de horas para la entrega (entre 12:00 y 18:00)
$hora_inicio = rand(12, 17); // Hora de inicio aleatoria entre las 12 y 17
$minuto_inicio = rand(0, 59); // Minutos aleatorios
$hora_fin = $hora_inicio + rand(1, 3); // Hora de fin (al menos una hora después de la hora de inicio)

if ($hora_fin > 23) {
    $hora_fin = 23; // Aseguramos que no pase de las 23:00
}

// Formatear las horas y minutos en un formato de 2 dígitos (HH:MM)
$hora_entrega_inicio = sprintf("%02d:%02d", $hora_inicio, $minuto_inicio);
$hora_entrega_fin = sprintf("%02d:%02d", $hora_fin, rand(0, 59));


// Almacenar la fecha y hora de entrega en la sesión
$_SESSION['fecha_entrega'] = $fecha_entrega;
$_SESSION['hora_entrega_inicio'] = $hora_entrega_inicio;
$_SESSION['hora_entrega_fin'] = $hora_entrega_fin;


// Obtener los datos de la compra desde la sesión
$id_compra = $_SESSION['id_compra'] ?? 'N/A';
$nombre = $_SESSION['nombre'] ?? 'N/A';
$total = $_SESSION['total'] ?? 0;
$metodo_pago = $_SESSION['metodo_pago'] ?? 'N/A';
$fecha_entrega = $_SESSION['fecha_entrega'] ?? '';
$hora_entrega = $_SESSION['hora_entrega'] ?? '';
$numero_tarjeta = $_SESSION['numero_tarjeta'] ?? null;
$nombre_tarjeta = $_SESSION['nombre_tarjeta'] ?? null;

// Generar el recibo según el método de pago
if ($metodo_pago == "tarjeta") {
    generarReciboTarjeta($id_compra, $nombre, $total, $numero_tarjeta, $nombre_tarjeta,$fecha_entrega, $hora_entrega_fin, $hora_entrega_inicio);
} elseif ($metodo_pago == "transferencia") {
    generarReciboTransferencia($id_compra, $nombre, $total, $fecha_entrega, $hora_entrega_fin, $hora_entrega_inicio);
}

?>