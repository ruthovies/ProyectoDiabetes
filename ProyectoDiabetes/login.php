<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['usuario']);
    $pass = trim($_POST['password']);

    // Consulta para obtener usuario
    $stmt = $pdo->prepare("SELECT idUsuario, password FROM usuarios WHERE usuario = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    // Verificar si la contraseña es correcta
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['idUsuario']; // Guardar usuario en sesión
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
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
                    <h2 class="text-center mb-4 text-primary"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</h2>

                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger text-center">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
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

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Acceder
                            </button>
                        </div>
                    </form>

                    <p class="text-center mt-3">
                        ¿No tienes una cuenta? <a href="registroUsuarios.php" class="text-primary">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
