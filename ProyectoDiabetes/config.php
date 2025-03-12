<?php
session_start();
$host = 'fdb1028.awardspace.net';
$dbname = '4599915_dbdiabetes';
$user = '4599915_dbdiabetes'; //Cambiar si es necesario
$pass = 'Patatafrita232'; //Cambiar si es necesario
try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
