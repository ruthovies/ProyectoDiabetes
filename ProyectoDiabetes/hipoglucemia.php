<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Por favor, inicie sesión para acceder a esta página.";
    exit;
}

// Si se seleccionó editar, cargar los datos del registro
$editData = null;
if (isset($_POST['accion']) && $_POST['accion'] == 'cargar_edicion') {
    $stmt = $pdo->prepare("SELECT * FROM hipo WHERE idUsuario = ? AND fecha = ? AND tipoComida = ?");
    $stmt->execute([$_SESSION['user_id'], $_POST['fecha'], $_POST['tipoComida']]);
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'];
    $idUsuario = $_SESSION['user_id'];
    $fecha = $_POST['fecha'] ?? '';
    $tipoComida = $_POST['tipoComida'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $glucosa = $_POST['glucosa'] ?? '';

    try {
        if ($accion == 'agregar') {
            $sql = "INSERT INTO hipo (idUsuario, fecha, tipoComida, hora, glucosa) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idUsuario, $fecha, $tipoComida, $hora, $glucosa]);
        } elseif ($accion == 'editar') {
            $sql = "UPDATE hipo SET hora = ?, glucosa = ? WHERE idUsuario = ? AND fecha = ? AND tipoComida = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$hora, $glucosa, $idUsuario, $fecha, $tipoComida]);
        } elseif ($accion == 'eliminar') {
            $sql = "DELETE FROM hipo WHERE idUsuario = ? AND fecha = ? AND tipoComida = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idUsuario, $fecha, $tipoComida]);
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger text-center'>Error en la base de datos: " . $e->getMessage() . "</div>";
    }
}

$registros = $pdo->query("SELECT * FROM hipo WHERE idUsuario = {$_SESSION['user_id']} ORDER BY fecha DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Hipoglucemia</title>
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
            max-width: 800px;
        }
        .btn-custom {
            width: 100%;
            margin-top: 10px;
        }
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container text-center">
        <h2 class="mb-3">Gestión de Hipoglucemia</h2>

        <!-- Formulario para agregar o editar un registro -->
        <form method="POST" class="mb-4">
            <input type="hidden" name="accion" value="<?= $editData ? 'editar' : 'agregar' ?>">
            <input type="hidden" name="fecha" value="<?= $editData['fecha'] ?? '' ?>">
            <input type="hidden" name="tipoComida" value="<?= $editData['tipoComida'] ?? '' ?>">

            <div class="mb-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" required value="<?= $editData['fecha'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Comida</label>
                <input type="text" name="tipoComida" class="form-control" required value="<?= $editData['tipoComida'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Hora</label>
                <input type="time" name="hora" class="form-control" required value="<?= $editData['hora'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nivel de Glucosa</label>
                <input type="number" name="glucosa" class="form-control" required value="<?= $editData['glucosa'] ?? '' ?>">
            </div>
            
            <button type="submit" class="btn btn-primary btn-custom"><?= $editData ? 'Actualizar Registro' : 'Agregar Registro' ?></button>
        </form>

        <h3 class="mt-3">Registros de Hipoglucemia</h3>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo de Comida</th>
                    <th>Hora</th>
                    <th>Glucosa (mg/dL)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $registro): ?>
                <tr>
                    <td><?= htmlspecialchars($registro['fecha']) ?></td>
                    <td><?= htmlspecialchars($registro['tipoComida']) ?></td>
                    <td><?= htmlspecialchars($registro['hora']) ?></td>
                    <td><?= htmlspecialchars($registro['glucosa']) ?></td>
                    <td>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="accion" value="cargar_edicion">
                            <input type="hidden" name="fecha" value="<?= $registro['fecha'] ?>">
                            <input type="hidden" name="tipoComida" value="<?= $registro['tipoComida'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">Editar</button>
                        </form>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="fecha" value="<?= $registro['fecha'] ?>">
                            <input type="hidden" name="tipoComida" value="<?= $registro['tipoComida'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este registro?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary btn-custom">Atrás</a>
    </div>

</body>
</html>

