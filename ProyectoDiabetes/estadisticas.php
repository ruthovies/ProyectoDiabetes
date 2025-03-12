<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Por favor, inicie sesión para acceder a esta página.";
    exit;
}

// Obtener datos de hiperglucemia
$hiperData = $pdo->prepare("SELECT fecha, glucosa FROM hiper WHERE idUsuario = ? ORDER BY fecha ASC");
$hiperData->execute([$_SESSION['user_id']]);
$hiperResults = $hiperData->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos de hipoglucemia
$hipoData = $pdo->prepare("SELECT fecha, glucosa FROM hipo WHERE idUsuario = ? ORDER BY fecha ASC");
$hipoData->execute([$_SESSION['user_id']]);
$hipoResults = $hipoData->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos de Glucosa</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Establecer tamaño mayor para los gráficos */
        canvas {
            width: 100% !important;  /* Ajusta el ancho al 100% del contenedor */
            max-width: 800px;        /* Limita el ancho máximo a 800px */
            height: 500px !important; /* Ajusta la altura a 500px */
        }

        .container-fluid {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 10vh;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <h2>Gráfico de Hiperglucemia</h2>
        <canvas id="hiperChart"></canvas>
        <h2>Gráfico de Hipoglucemia</h2>
        <canvas id="hipoChart"></canvas>
    </div>

    <script>
        const hiperData = {
            labels: [<?php foreach ($hiperResults as $row) { echo "'" . $row['fecha'] . "',"; } ?>],
            datasets: [{
                label: 'Niveles de Glucosa (mg/dL)',
                data: [<?php foreach ($hiperResults as $row) { echo $row['glucosa'] . ","; } ?>],
                borderColor: 'red',
                fill: false
            }]
        };

        const hipoData = {
            labels: [<?php foreach ($hipoResults as $row) { echo "'" . $row['fecha'] . "',"; } ?>],
            datasets: [{
                label: 'Niveles de Glucosa (mg/dL)',
                data: [<?php foreach ($hipoResults as $row) { echo $row['glucosa'] . ","; } ?>],
                borderColor: 'blue',
                fill: false
            }]
        };

        new Chart(document.getElementById('hiperChart'), {
            type: 'line',
            data: hiperData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 14  // Tamaño de la fuente de las etiquetas del eje X
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 14  // Tamaño de la fuente de las etiquetas del eje Y
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 16  // Tamaño de la fuente de las etiquetas de la leyenda
                            }
                        }
                    },
                    title: {
                        display: true,
                        font: {
                            size: 18  // Tamaño de la fuente del título
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('hipoChart'), {
            type: 'line',
            data: hipoData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 14  // Tamaño de la fuente de las etiquetas del eje X
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 14  // Tamaño de la fuente de las etiquetas del eje Y
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 16  // Tamaño de la fuente de las etiquetas de la leyenda
                            }
                        }
                    },
                    title: {
                        display: true,
                        font: {
                            size: 18  // Tamaño de la fuente del título
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

