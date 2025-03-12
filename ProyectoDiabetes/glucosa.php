<?php
require "config.php";

if (!isset($_SESSION['user_id'])) {
    header("location:index.php");
    exit;
}

$errorMessage = ""; // Variable para almacenar mensajes de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST["accion"] ?? "";
    $fecha = isset($_POST["fecha"]) ? date("Y-m-d", strtotime($_POST["fecha"])) : null;
    $lenta = isset($_POST["lenta"]) ? intval($_POST["lenta"]) : null;
    $deporte = isset($_POST["deporte"]) ? intval($_POST["deporte"]) : 1;

    try {
        if ($accion === "agregar" && $fecha && $lenta) {
            // Verificar si ya existe un registro en la misma fecha
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM controlglucosa WHERE idUsuario=? AND fecha=?");
            $stmt->execute([$_SESSION["user_id"], $fecha]);
            $existe = $stmt->fetchColumn();

            if ($existe == 0) {
                $SQL = "INSERT INTO controlglucosa (idUsuario, fecha, lenta, deporte) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($SQL);
                $stmt->execute([$_SESSION["user_id"], $fecha, $lenta, $deporte]);
            } else {
                $errorMessage = "⚠️ Ya existe un registro para esta fecha. Solo se permite un ingreso por día.";
            }
        } elseif ($accion === "editar" && isset($_POST["old_fecha"])) {
            $old_fecha = date("Y-m-d", strtotime($_POST["old_fecha"]));
            $SQL = "UPDATE controlglucosa SET fecha=?, lenta=?, deporte=? WHERE idUsuario=? AND fecha=?";
            $stmt = $pdo->prepare($SQL);
            $stmt->execute([$fecha, $lenta, $deporte, $_SESSION["user_id"], $old_fecha]);
        } elseif ($accion === "eliminar" && isset($_POST["delete"])) {
            $delete_fecha = date("Y-m-d", strtotime($_POST["delete"]));
            $sql = "DELETE FROM controlglucosa WHERE idUsuario=? AND fecha=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION["user_id"], $delete_fecha]);
        }
    } catch (PDOException $e) {
        $errorMessage = "❌ Error en la base de datos: " . $e->getMessage();
    }
}

$edit_fecha = $edit_lenta = $edit_deporte = "";
if (isset($_POST["accion"]) && $_POST["accion"] === "cargar_edicion") {
    $edit_fecha = $_POST["edit_fecha"];
    $edit_lenta = $_POST["edit_lenta"];
    $edit_deporte = $_POST["edit_deporte"];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Glucosa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
            width: 90%;
            max-width: 600px;
        }
        .alert {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-custom {
            width: 100%;
            margin-top: 10px;
        }
        .btn-group label {
            margin-right: 5px;
        }
        table {
            margin-top: 20px;
        }
        .btn-back {
            width: 100%;
            margin-top: 15px;
            background-color: #6c757d;
            color: white;
            font-size: 16px;
            padding: 10px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

    <div class="container text-center">
        <h2 class="mb-3">Gestión de Control de Glucosa</h2>

        <?php if ($errorMessage): ?>
            <div class="alert alert-warning"><?= $errorMessage ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-4">
            <input type="hidden" name="accion" value="<?= $edit_fecha ? 'editar' : 'agregar' ?>">
            <div class="mb-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" required value="<?= $edit_fecha ?>">
                <input type="hidden" name="old_fecha" value="<?= $edit_fecha ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Índice de Actividad</label>
                <div class="btn-group" role="group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input type="radio" class="btn-check" name="deporte" id="deporte<?= $i ?>" value="<?= $i ?>" 
                            <?= ($edit_deporte == $i) ? "checked" : "" ?> required>
                        <label class="btn btn-outline-primary" for="deporte<?= $i ?>"><?= $i ?></label>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Lenta</label>
                <input type="number" name="lenta" class="form-control" placeholder="Ingrese la medición" required value="<?= $edit_lenta ?>">
            </div>
            
            <button type="submit" class="btn btn-primary btn-custom"><?= $edit_fecha ? 'Actualizar' : 'Agregar' ?></button>
        </form>

        <h3 class="mt-3">Registros</h3>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Lenta</th>
                    <th>Índice de Actividad</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM controlglucosa WHERE idUsuario=? ORDER BY fecha DESC");
                $stmt->execute([$_SESSION["user_id"]]);
                while ($row = $stmt->fetch()) {
                    $fecha_formateada = date("Y-m-d", strtotime($row['fecha']));
                    echo "<tr>
                        <td>{$fecha_formateada}</td>
                        <td>{$row['lenta']}</td>
                        <td>{$row['deporte']}</td>
                        <td>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='accion' value='cargar_edicion'>
                                <input type='hidden' name='edit_fecha' value='{$fecha_formateada}'>
                                <input type='hidden' name='edit_lenta' value='{$row['lenta']}'>
                                <input type='hidden' name='edit_deporte' value='{$row['deporte']}'>
                                <button type='submit' class='btn btn-success btn-sm'>Editar</button>
                            </form>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='accion' value='eliminar'>
                                <input type='hidden' name='delete' value='{$fecha_formateada}'>
                                <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\");'>Eliminar</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn-back">Atrás</a>
    </div>

</body>
</html>

