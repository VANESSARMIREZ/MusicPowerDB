<?php
include 'conexion.php';

$term = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($term !== '') {
    $stmt = $pdo->prepare("SELECT id, titulo, artista FROM canciones WHERE LOWER(titulo) LIKE LOWER(?) OR LOWER(artista) LIKE LOWER(?) ORDER BY artista, titulo LIMIT 10");
    $like = "%$term%";
    $stmt->execute([$like, $like]);
    $canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($canciones);
}
