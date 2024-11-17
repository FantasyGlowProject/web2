<?php
// reiniciar_sesion.php

// Solo inicia la sesión si no ha sido iniciada previamente
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
session_destroy(); // Elimina todas las variables de sesión
header("Location: index.html"); // Redirige a la página de inicio
exit();
?>