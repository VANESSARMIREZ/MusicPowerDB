<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $artista = $_POST['artista'];
    $descripcion = $_POST['descripcion'];
    $enlace = $_POST['enlace_youtube'];
    $recomendacion_text = $_POST['recomendacion_text'];
    $enviado_por = trim($_POST['enviado_por']);
    if ($enviado_por === '') {
        $enviado_por = 'Anónimo';
    }

    // Extraer ID de YouTube (v=XXXXXXXX o youtu.be/XXXXXXXX)
    if (preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $enlace, $match)) {
        $video_id = $match[1];
    } else {
        $video_id = '';
    }

    // Generar miniatura
    $imagen = $video_id ? "https://img.youtube.com/vi/$video_id/hqdefault.jpg" : '';

    // Obtener el siguiente orden_mes
    $stmtOrden = $pdo->query("SELECT MAX(orden_mes) AS max_orden FROM canciones WHERE tipo = 'recomendacion'");
    $max = $stmtOrden->fetch(PDO::FETCH_ASSOC)['max_orden'] ?? 0;
    $orden = $max + 1;

    // Fecha actual
    $fecha = date('Y-m-d');

    // Insertar en la base de datos
    $stmt = $pdo->prepare("INSERT INTO canciones 
        (titulo, artista, descripcion, enlace_youtube, imagen_url, tipo, enviado_por, fecha_recomendacion, orden_mes, recomendacion_text)
        VALUES (?, ?, ?, ?, ?, 'recomendacion', ?, ?, ?, ?)");
    $stmt->execute([
        $titulo,
        $artista,
        $descripcion,
        $enlace,
        $imagen,
        $enviado_por,
        $fecha,
        $orden,
        $recomendacion_text
    ]);

    header('Location: index.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Recomendación</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="icon.png"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <!-- Header con logo y botones -->
    <div class="header detalle-header">
      <a href="index.php"><img src="logo.png" class="logo" alt="Logo"/></a>
    </div>
</head>
<body>
<div class="agregar-recomendacion-container">


    <!-- Volver fijo arriba a la izquierda -->
    <a href="index.php" class="volver-fijo"><i class="fas fa-arrow-left"></i>  Volver</a>


       <!-- Título -->
    <h2>Agregar Recomendación del Mes</h2>

<form method="post">
  <input name="titulo" placeholder="Título de la canción" required>
  <input name="artista" placeholder="Nombre del artista" required>
  <textarea name="descripcion" placeholder="Descripción de la canción" required></textarea>
  <input name="enlace_youtube" placeholder="URL de YouTube (https://...)" required>
  <textarea name="recomendacion_text" placeholder="Texto personalizado (opcional)"></textarea>
  <input name="enviado_por" placeholder="Nombre de quien recomienda la canción (opcional)">
  <button type="submit">Guardar Recomendación</button>
</form>


  </div>


  </div>
 <footer>
      <p>Curso: Conceptualización de servicios en la nube</p>
      <p>Nombre: Vanessa Itzarahí Gómez Ramírez</p>
      <p>Código: 218752859</p>
      <p>Correo: vanessa.gomez2859@alumnos.udg.mx</p>
    </footer>
  </body>
</html>
