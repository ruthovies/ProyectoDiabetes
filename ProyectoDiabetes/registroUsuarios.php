<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Obtener la fecha actual
    $fecha_actual = date("Y-m-d");

    // Validaciones
    if ($fecha_nacimiento > $fecha_actual) {
        $error = "La fecha de nacimiento no puede ser en el futuro.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el usuario ya existe
        $stmt = $pdo->prepare("SELECT idUsuario FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error = "El usuario ya está registrado. Intenta con otro nombre de usuario.";
        } else {
            // Hash de la contraseña y registro del usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre, apellido, fecha_nacimiento, usuario, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$nombre, $apellido, $fecha_nacimiento, $usuario, $hashed_password])) {
                $success = "Usuario registrado con éxito. <a href='login.php'>Inicia sesión aquí</a>.";
            } else {
                $error = "Error en el registro. Intenta nuevamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .form-control:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 8px rgba(106, 17, 203, 0.5);
        }
        .btn-primary {
            background: #6a11cb;
            border: none;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: #5012a1;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4">
                    <h2 class="text-center mb-4 text-primary"><i class="bi bi-person-plus"></i> Registro</h2>

                    <?php if (isset($success)) : ?>
                        <div class="alert alert-success text-center">
                            <?php echo $success; ?>
                        </div>
                    <?php elseif (isset($error)) : ?>
                        <div class="alert alert-danger text-center">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" id="nombre" name="nombre" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" id="apellido" name="apellido" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                                <input type="text" id="usuario" name="usuario" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-check"></i> Registrar
                            </button>
                        </div>

                    </form>

                    <p class="text-center mt-3">
                        ¿Ya tienes una cuenta? <a href="index.php" class="text-primary">Inicia sesión aquí</a>
                    </p>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
