<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Por favor, inicie sesión para acceder a esta página.";
    exit;
}

// Obtener los últimos 10 registros de hiperglucemia
$hiperData = $pdo->prepare("SELECT fecha, glucosa FROM hiper WHERE idUsuario = ? ORDER BY fecha DESC LIMIT 10");
$hiperData->execute([$_SESSION['user_id']]);
$hiperResults = array_reverse($hiperData->fetchAll(PDO::FETCH_ASSOC));

// Obtener los últimos 10 registros de hipoglucemia
$hipoData = $pdo->prepare("SELECT fecha, glucosa FROM hipo WHERE idUsuario = ? ORDER BY fecha DESC LIMIT 10");
$hipoData->execute([$_SESSION['user_id']]);
$hipoResults = array_reverse($hipoData->fetchAll(PDO::FETCH_ASSOC));

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos de Glucosa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            width: 90%;
            max-width: 1200px;
        }
        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            flex: 1;
            min-width: 500px;
            text-align: center;
        }
        canvas {
            width: 100% !important;
            height: 350px !important;
        }
        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
            background: white;
            color: #6a11cb;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #6a11cb;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Botón de volver -->
    <a href="dashboard.php" class="btn-back">Atras</a>

    <div class="chart-container">
        <div class="chart-box">
            <h2>Hiperglucemia</h2>
            <canvas id="hiperChart"></canvas>
        </div>
        <div class="chart-box">
            <h2>Hipoglucemia</h2>
            <canvas id="hipoChart"></canvas>
        </div>
    </div>
    
    <script>
        // Datos de hiperglucemia
        const hiperData = {
            labels: [<?php foreach ($hiperResults as $row) { echo "'" . $row['fecha'] . "',"; } ?>],
            datasets: [{
                label: 'Glucosa (mg/dL)',
                data: [<?php foreach ($hiperResults as $row) { echo $row['glucosa'] . ","; } ?>],
                borderColor: 'red',
                backgroundColor: 'rgba(255, 0, 0, 0.2)',
                fill: true,
                tension: 0.3
            }]
        };

        // Datos de hipoglucemia
        const hipoData = {
            labels: [<?php foreach ($hipoResults as $row) { echo "'" . $row['fecha'] . "',"; } ?>],
            datasets: [{
                label: 'Glucosa (mg/dL)',
                data: [<?php foreach ($hipoResults as $row) { echo $row['glucosa'] . ","; } ?>],
                borderColor: 'blue',
                backgroundColor: 'rgba(0, 0, 255, 0.2)',
                fill: true,
                tension: 0.3
            }]
        };

        // Configuración común para los gráficos
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { font: { size: 14 } } },
                y: { ticks: { font: { size: 14 } } }
            },
            plugins: {
                legend: { labels: { font: { size: 14 } } },
                title: { display: true, font: { size: 16 } }
            }
        };

        // Renderizar gráficos
        new Chart(document.getElementById('hiperChart'), { type: 'line', data: hiperData, options: chartOptions });
        new Chart(document.getElementById('hipoChart'), { type: 'line', data: hipoData, options: chartOptions });
    </script>

</body>
</html>

