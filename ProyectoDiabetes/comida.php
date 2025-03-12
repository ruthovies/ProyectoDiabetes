<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';
    $idUsuario = $_POST['idUsuario'] ?? $_SESSION['user_id'];
    $fecha = isset($_POST['fecha']) && !empty($_POST['fecha']) ? date("Y-m-d", strtotime($_POST['fecha'])) : null;
    $pre = $_POST['pre'] ?? '';
    $post = $_POST['post'] ?? '';
    $racion = $_POST['racion'] ?? '';
    $insulina = $_POST['insulina'] ?? '';
    $tipoComida = $_POST['tipoComida'] ?? '';

    try {
        if ($accion == 'agregar') {
            $sql = "INSERT INTO comidas (idUsuario, fecha, pre, post, racion, insulina, tipoComida) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idUsuario, $fecha, $pre, $post, $racion, $insulina, $tipoComida]);
        } elseif ($accion == 'editar' && $tipoComida && $fecha) {
            $sql = "UPDATE comidas SET fecha = ?, pre = ?, post = ?, racion = ?, insulina = ?, tipoComida = ? WHERE idUsuario = ? AND fecha = ? AND tipoComida = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fecha, $pre, $post, $racion, $insulina, $tipoComida, $idUsuario, $fecha, $tipoComida]);
        } elseif ($accion == 'eliminar' && $tipoComida) {
            try {
                // Obtener la fecha del registro a eliminar
                $stmt = $pdo->prepare("SELECT fecha FROM comidas WHERE idUsuario = ? AND tipoComida = ? ORDER BY fecha DESC LIMIT 1");
                $stmt->execute([$idUsuario, $tipoComida]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$row) {
                    throw new Exception("No se encontró la fecha para el tipo de comida: $tipoComida");
                }
                
                $fecha = $row['fecha']; // Ahora tenemos la fecha correcta

                $pdo->beginTransaction();

                // Eliminar registros en 'hiper'
                $sql = "DELETE FROM hiper WHERE idUsuario = ? AND fecha = ? AND tipoComida = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$idUsuario, $fecha, $tipoComida]);

                // Eliminar registros en 'hipo'
                $sql = "DELETE FROM hipo WHERE idUsuario = ? AND fecha = ? AND tipoComida = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$idUsuario, $fecha, $tipoComida]);

                // Luego eliminar el registro en 'comidas'
                $sql = "DELETE FROM comidas WHERE idUsuario = ? AND fecha = ? AND tipoComida = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$idUsuario, $fecha, $tipoComida]);

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "<div class='alert alert-danger text-center'>Error en la base de datos: " . $e->getMessage() . "</div>";
            }
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger text-center'>Error en la base de datos: " . $e->getMessage() . "</div>";
    }
}

// Obtener comidas del usuario
$comidas = $pdo->query("SELECT * FROM comidas WHERE idUsuario = {$_SESSION['user_id']} ORDER BY fecha DESC")->fetchAll(PDO::FETCH_ASSOC);

// Cargar datos para edición
$editComida = null;
if (isset($_POST['accion']) && $_POST['accion'] == 'cargar_edicion') {
    $tipoComida = $_POST['tipoComida'];
    $stmt = $pdo->prepare("SELECT * FROM comidas WHERE tipoComida = ?");
    $stmt->execute([$tipoComida]);
    $editComida = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Comidas</title>
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
        .btn-custom {
            width: 100%;
            margin-top: 10px;
        }
        .table {
            margin-top: 20px;
        }
        .btn-group .btn {
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="container text-center">
        <h2 class="mb-3">Registro de Comidas</h2>

        <form method="post" class="mb-4">
            <input type="hidden" name="accion" value="<?= $editComida ? 'editar' : 'agregar' ?>">
            <input type="hidden" name="tipoComida" id="tipoComidaInput" value="<?= $editComida['tipoComida'] ?? '' ?>">
            
            <div class="mb-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" required value="<?= $editComida['fecha'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Medición Pre-Comida</label>
                <input type="number" name="pre" class="form-control" placeholder="Glucosa antes de comer" required value="<?= $editComida['pre'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Medición Post-Comida</label>
                <input type="number" name="post" class="form-control" placeholder="Glucosa después de comer" required value="<?= $editComida['post'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Raciones</label>
                <input type="number" name="racion" class="form-control" placeholder="Cantidad de raciones" required value="<?= $editComida['racion'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Dosis de Insulina</label>
                <input type="number" name="insulina" class="form-control" placeholder="Dosis en unidades" required value="<?= $editComida['insulina'] ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Comida</label>
                <div class="btn-group d-flex" role="group">
                    <?php 
                    $tiposComida = ["Desayuno", "Aperitivo", "Comida", "Merienda", "Cena"];
                    foreach ($tiposComida as $tipo) {
                        $activo = (isset($editComida['tipoComida']) && $editComida['tipoComida'] == $tipo) ? 'btn-primary' : 'btn-outline-primary';
                        echo "<button type='button' class='btn $activo flex-fill' onclick='seleccionarComida(\"$tipo\", this)'>$tipo</button>";
                    }
                    ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-custom"><?= $editComida ? 'Actualizar' : 'Agregar' ?></button>
        </form>

        <h3 class="mt-3">Historial de Comidas</h3>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Pre</th>
                    <th>Post</th>
                    <th>Raciones</th>
                    <th>Insulina</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comidas as $comida): ?>
                <tr>
                    <td><?= htmlspecialchars($comida['fecha']) ?></td>
                    <td><?= htmlspecialchars($comida['pre']) ?></td>
                    <td><?= htmlspecialchars($comida['post']) ?></td>
                    <td><?= htmlspecialchars($comida['racion']) ?></td>
                    <td><?= htmlspecialchars($comida['insulina']) ?></td>
                    <td><?= htmlspecialchars($comida['tipoComida']) ?></td>
                    <td>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="accion" value="cargar_edicion">
                            <input type="hidden" name="tipoComida" value="<?= $comida['tipoComida'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">Editar</button>
                        </form>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="tipoComida" value="<?= $comida['tipoComida'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este registro?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary btn-custom">Atrás</a>
    </div>

    <script>
    function seleccionarComida(tipo, boton) {
        document.getElementById('tipoComidaInput').value = tipo;
        document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('btn-primary'));
        boton.classList.add('btn-primary');
    }
    </script>

</body>
</html>


