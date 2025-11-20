<?php
session_start();

$enlace_meet = '';
if (isset($_SESSION['enlace_meet'])) {
    $enlace_meet = $_SESSION['enlace_meet'];
} else {
    header("Location: bienvenida.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceder a la Consulta</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Su Consulta Virtual</h1>
        <p>Haga clic en el botón de abajo para unirse a su videollamada con Google Meet.</p>
        <?php if (!empty($enlace_meet)): ?>
            <a href="<?php echo htmlspecialchars($enlace_meet); ?>" target="_blank" class="button primary">Unirse a la Videollamada (Google Meet)</a>
        <?php else: ?>
            <div class="message error">
                <p>No se ha encontrado un enlace de videollamada. Por favor, vuelva a validar su cita.</p>
            </div>
        <?php endif; ?>
        <p class="small-text">Asegúrese de tener una buena conexión a internet y un navegador compatible.</p>
        <hr>
        <p>Una vez finalizada la consulta, puede cerrar la ventana de Google Meet y luego hacer clic en "Finalizar Consulta y Salir".</p>
        <a href="agradecimiento.php" class="button secondary">Finalizar Consulta y Salir</a>
    </div>
</body>
</html>