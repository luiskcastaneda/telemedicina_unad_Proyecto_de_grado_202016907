<?php
session_start();
require_once 'db_config.php';

$mensaje = '';

// Lógica para Añadir o Editar una cita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $documento = trim($_POST['documento']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $enlace_meet = trim($_POST['enlace_meet']);

    if ($action == 'add') {
        $sql = "INSERT INTO citas (documento, fecha_nacimiento, enlace_meet) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $documento, $fecha_nacimiento, $enlace_meet);
            if (mysqli_stmt_execute($stmt)) {
                $mensaje = "<div class='message success'>Cita añadida exitosamente.</div>";
            } else {
                $mensaje = "<div class='message error'>Error al añadir cita: " . mysqli_error($link) . "</div>";
            }
            mysqli_stmt_close($stmt);
        }
    } elseif ($action == 'edit' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE citas SET documento = ?, fecha_nacimiento = ?, enlace_meet = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssi", $documento, $fecha_nacimiento, $enlace_meet, $id);
            if (mysqli_stmt_execute($stmt)) {
                $mensaje = "<div class='message success'>Cita actualizada exitosamente.</div>";
            } else {
                $mensaje = "<div class='message error'>Error al actualizar cita: " . mysqli_error($link) . "</div>";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM citas WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $mensaje = "<div class='message success'>Cita eliminada exitosamente.</div>";
        } else {
            $mensaje = "<div class='message error'>Error al eliminar cita: " . mysqli_error($link) . "</div>";
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: admin_citas.php?msg=" . urlencode(strip_tags($mensaje)));
    exit();
}

$cita_a_editar = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, documento, fecha_nacimiento, enlace_meet FROM citas WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $cita_a_editar = mysqli_fetch_assoc($result);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

if (isset($_GET['msg'])) {
    $mensaje = "<div class='message success'>" . htmlspecialchars($_GET['msg']) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Citas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .crud-container {
            max-width: 900px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .crud-container h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="url"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .crud-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .crud-table th, .crud-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: middle;
        }
        .crud-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        .crud-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .crud-table .action-buttons a {
            margin-right: 5px;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            display: inline-block;
        }
        .crud-table .action-buttons .edit-button {
            background-color: #3498db;
            color: white;
        }
        .crud-table .action-buttons .edit-button:hover {
            background-color: #2980b9;
        }
        .crud-table .action-buttons .delete-button {
            background-color: #e74c3c;
            color: white;
        }
        .crud-table .action-buttons .delete-button:hover {
            background-color: #c0392b;
        }
        .message {
            margin-top: 20px;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="crud-container">
        <h2>Administración de Citas</h2>

        <?php echo $mensaje; ?>
        <h3><?php echo ($cita_a_editar ? 'Editar Cita' : 'Añadir Nueva Cita'); ?></h3>
        <form action="admin_citas.php" method="POST">
            <?php if ($cita_a_editar): ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php echo $cita_a_editar['id']; ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="add">
            <?php endif; ?>

            <div class="form-group">
                <label for="documento">Número de Documento:</label>
                <input type="text" id="documento" name="documento" value="<?php echo htmlspecialchars($cita_a_editar['documento'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($cita_a_editar['fecha_nacimiento'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="enlace_meet">Enlace de Google Meet:</label>
                <input type="url" id="enlace_meet" name="enlace_meet" value="<?php echo htmlspecialchars($cita_a_editar['enlace_meet'] ?? ''); ?>" placeholder="https://meet.google.com/..." required>
            </div>
            <button type="submit" class="button">
                <?php echo ($cita_a_editar ? 'Actualizar Cita' : 'Añadir Cita'); ?>
            </button>
            <?php if ($cita_a_editar): ?>
                <a href="admin_citas.php" class="button secondary">Cancelar Edición</a>
            <?php endif; ?>
        </form>

        <hr>

        <h3>Citas Existentes</h3>
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Documento</th>
                    <th>Fecha Nacimiento</th>
                    <th>Enlace Meet</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id, documento, fecha_nacimiento, enlace_meet FROM citas ORDER BY id DESC";
                if ($result = mysqli_query($link, $sql)) {
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['documento']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['fecha_nacimiento']) . "</td>";
                            echo "<td><a href='" . htmlspecialchars($row['enlace_meet']) . "' target='_blank'>" . htmlspecialchars($row['enlace_meet']) . "</a></td>";
                            echo "<td class='action-buttons'>";
                            echo "<a href='admin_citas.php?action=edit&id=" . $row['id'] . "' class='edit-button'>Editar</a>";
                            echo "<a href='admin_citas.php?action=delete&id=" . $row['id'] . "' class='delete-button' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta cita?\");'>Eliminar</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay citas registradas aún.</td></tr>";
                    }
                    mysqli_free_result($result);
                } else {
                    echo "<tr><td colspan='5'>ERROR: No se pudo ejecutar $sql. " . mysqli_error($link) . "</td></tr>";
                }
                mysqli_close($link);
                ?>
            </tbody>
        </table>
        <p class="small-text" style="margin-top: 25px;">
            <a href="bienvenida.php" class="button">Volver al Inicio</a>
        </p>
    </div>
</body>
</html>