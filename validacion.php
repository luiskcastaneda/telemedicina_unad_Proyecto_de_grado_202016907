<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de Cita</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Verificación de Cita</h1>
        <form action="validacion.php" method="POST">
            <label for="documento">Número de Documento:</label>
            <input type="text" id="documento" name="documento" required>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

            <button type="submit" class="button">Verificar Cita</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $usuarios_con_cita = [
                [
                    'documento' => '123456789',
                    'fecha_nacimiento' => '1990-01-15',
                    'enlace_meet' => 'https://meet.google.com/abc-defg-hij'
                ],
                [
                    'documento' => '987654321',
                    'fecha_nacimiento' => '1985-05-20',
                    'enlace_meet' => 'https://meet.google.com/klm-nopq-rst'
                ],
                [
                    'documento' => '112233445',
                    'fecha_nacimiento' => '2000-11-30',
                    'enlace_meet' => 'https://meet.google.com/uvw-xyza-bcd'
                ]
            ];

            $documento_ingresado = $_POST['documento'];
            $fecha_nacimiento_ingresada = $_POST['fecha_nacimiento'];
            $cita_encontrada = false;
            $enlace_meet_activo = '';

            foreach ($usuarios_con_cita as $usuario) {
                if ($usuario['documento'] == $documento_ingresado && $usuario['fecha_nacimiento'] == $fecha_nacimiento_ingresada) {
                    $cita_encontrada = true;
                    $enlace_meet_activo = $usuario['enlace_meet'];
                    break;
                }
            }

            if ($cita_encontrada) {
                session_start();
                $_SESSION['enlace_meet'] = $enlace_meet_activo;

                echo "<div class='message success'>";
                echo "<p>¡Cita activa encontrada!</p>";
                echo "<a href='consulta.php' class='button'>Acceder a la Consulta</a>";
                echo "</div>";
            } else {
                echo "<div class='message error'>";
                echo "<p>No se encontró una cita activa con la información proporcionada.</p>";
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>