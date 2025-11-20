<?php
session_start();

require_once 'db_config.php';
?>
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
        <form action="validacion_base.php" method="POST">
            <label for="documento">Número de Documento:</label>
            <input type="text" id="documento" name="documento" required>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

            <button type="submit" class="button">Verificar Cita</button>
        </form>

        <?php
        $cita_encontrada = false;
        $enlace_meet_activo = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $documento_ingresado = trim($_POST['documento']);
            $fecha_nacimiento_ingresada = trim($_POST['fecha_nacimiento']);

            $sql = "SELECT enlace_meet FROM citas WHERE documento = ? AND fecha_nacimiento = ?";

            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "ss", $param_documento, $param_fecha_nacimiento);

                $param_documento = $documento_ingresado;
                $param_fecha_nacimiento = $fecha_nacimiento_ingresada;

                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);

                    if(mysqli_stmt_num_rows($stmt) == 1){
                        mysqli_stmt_bind_result($stmt, $enlace_meet_activo);
                        mysqli_stmt_fetch($stmt);
                        $cita_encontrada = true;
                    }
                } else{
                    echo "<div class='message error'><p>¡Ups! Algo salió mal. Por favor, inténtelo de nuevo más tarde.</p></div>";
                }
                mysqli_stmt_close($stmt);
            }

            mysqli_close($link);

            if ($cita_encontrada) {
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