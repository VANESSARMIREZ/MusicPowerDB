<?php
include 'conexion.php'; // usa tu conexión unificada con $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $artista = $_POST['artista'];
    $descripcion = $_POST['descripcion'];
    $enlace = $_POST['enlace_youtube'];

  // Extraer ID de YouTube
parse_str(parse_url($enlace, PHP_URL_QUERY), $url_params);
$video_id = $url_params['v'] ?? '';

// Si aún no hay ID, intenta con youtu.be/ID
if (!$video_id && preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $enlace, $match)) {
    $video_id = $match[1];
}

$imagen = $video_id ? "https://img.youtube.com/vi/$video_id/hqdefault.jpg" : '';


    // Miniatura
    $imagen = $video_id ? "https://img.youtube.com/vi/$video_id/hqdefault.jpg" : '';

    // Obtener siguiente orden_mes para tipo 'top'
    $stmtOrden = $pdo->query("SELECT MAX(orden_mes) AS max_orden FROM canciones WHERE tipo = 'top'");
    $max = $stmtOrden->fetch(PDO::FETCH_ASSOC)['max_orden'] ?? 0;
    $orden = $max + 1;

    // Fecha actual
    $fecha = date('Y-m-d');

    // Insertar en la base de datos
    $stmt = $pdo->prepare("INSERT INTO canciones 
        (titulo, artista, descripcion, enlace_youtube, imagen_url, tipo, fecha_recomendacion, orden_mes)
        VALUES (?, ?, ?, ?, ?, 'top', ?, ?)");
    $stmt->execute([
        $titulo,
        $artista,
        $descripcion,
        $enlace,
        $imagen,
        $fecha,
        $orden
    ]);

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Canción al Top</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="icon.png"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="header detalle-header">
  <a href="index.php"><img src="logo.png" class="logo" alt="Logo"/></a>
</div>

<a href="index.php" class="volver-fijo"><i class="fas fa-arrow-left"></i> Volver</a>

<div class="agregar-recomendacion-container">
  <h2>Agregar Canción al Top</h2>

  <form method="post">
    <input name="titulo" placeholder="Título de la canción" required>
    <input name="artista" placeholder="Nombre del artista" required>
    <textarea name="descripcion" placeholder="Descripción de la canción" required></textarea>
    <input name="enlace_youtube" placeholder="URL de YouTube (https://...)" required>
    <button type="submit">Guardar Canción</button>
  </form>
</div>

<footer>
  <p>Curso: Conceptualización de servicios en la nube</p>
  <p>Nombre: Vanessa Itzarahí Gómez Ramírez</p>
  <p>Código: 218752859</p>
  <p>Correo: vanessa.gomez2859@alumnos.udg.mx</p>
</footer>
</body>
</html>
