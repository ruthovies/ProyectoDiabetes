<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glucomate - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .navbar-brand img {
            width: 40px;
            margin-right: 10px;
        }
        .dashboard-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 600px;
            text-align: center;
            margin-top: 20px;
        }
        .btn-logout {
            background: #dc3545;
            border: none;
            transition: 0.3s;
        }
        .btn-logout:hover {
            background: #a71d2a;
        }
    </style>
</head>
<body>

    <!--Navbar-->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="imagenes/logo.png" alt="Glucomate Logo"> <strong>Glucomate</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-logout text-white" href="logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Imagen decorativa arriba del cuadro -->
    <div class="text-center mt-4">
        <img src="imagenes/amiguito.png" alt="Imagen de Glucomate" style="max-width: 200px;">
    </div>

    <!-- Dashboard -->
<div class="dashboard-container text-center">
    <h2>Bienvenido a Glucomate</h2>
    <p>Gestiona tus mediciones y mantén tu salud bajo control.</p>

    <!-- Contenedor de botones en fila (en una sola línea) -->
    <div class="d-flex justify-content-center gap-3 flex-nowrap">
        
        <!-- Glucosa -->
        <a href="glucosa.php" class="btn btn-primary">
            <i class="bi bi-droplet"></i> Glucosa
        </a>

        <!-- Comida -->
        <a href="comida.php" class="btn btn-success">
            <i class="bi bi-egg-fried"></i> Comida
        </a>

        <!-- Hiperglucemia -->
        <a href="hiperglucemia.php" class="btn btn-danger">
            <i class="bi bi-arrow-up-circle"></i> Hiperglucemia
        </a>

        <!-- Hipoglucemia -->
        <a href="hipoglucemia.php" class="btn btn-warning">
            <i class="bi bi-arrow-down-circle"></i> Hipoglucemia
        </a>

    </div>

    <!-- Botón de Ver Estadísticas centrado debajo -->
    <a href="estadisticas.php" class="btn btn-success mt-3 w-100"><i class="bi bi-bar-chart"></i> Ver Estadísticas</a>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>