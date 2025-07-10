<?php
$host = 'dpg-d1nkheadbo4c73en0ne0-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'musicpower_r2rc';
$user = 'girlpower';
$password = 'Vc6AuwLsOHB6Zasc86mHF3NM1RqUKXps';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
