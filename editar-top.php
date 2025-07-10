<?php
include 'conexion.php'; // usa tu conexión unificada con $pdo

// Obtener ID
if (!isset($_GET['id'])) exit('ID no especificado.');
$id = intval($_GET['id']);

// Obtener datos actuales
$stmt = $pdo->prepare("SELECT * FROM canciones WHERE id = ? AND tipo = 'top'");
$stmt->execute([$id]);
$c = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$c) exit('Canción no encontrada o no es del tipo "top".');

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $artista = $_POST['artista'];
    $descripcion = $_POST['descripcion'];
    $enlace = $_POST['enlace_youtube'];

    // Extraer ID de YouTube
    parse_str(parse_url($enlace, PHP_URL_QUERY), $url_params);
    $video_id = $url_params['v'] ?? '';

    if (!$video_id && preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $enlace, $match)) {
        $video_id = $match[1];
    }

    $imagen = $video_id ? "https://img.youtube.com/vi/$video_id/hqdefault.jpg" : '';

    // Actualizar en la base de datos
    $stmt = $pdo->prepare("UPDATE canciones SET 
        titulo = ?, 
        artista = ?, 
        descripcion = ?, 
        enlace_youtube = ?, 
        imagen_url = ? 
        WHERE id = ? AND tipo = 'top'");
        
    $stmt->execute([
        $titulo,
        $artista,
        $descripcion,
        $enlace,
        $imagen,
        $id
    ]);

    header("Location: detalle.php?id=$id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Canción del Top</title>
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
  <h2>Editar Canción del Top</h2>

  <form method="post">
    <label>
      <span class="label-text">Título:</span>
      <input name="titulo" placeholder="Título de la canción" required value="<?= htmlspecialchars($c['titulo']) ?>">
    </label>

    <label>
      <span class="label-text">Artista:</span>
      <input name="artista" placeholder="Nombre del artista" required value="<?= htmlspecialchars($c['artista']) ?>">
    </label>

    <label>
      <span class="label-text">Descripción:</span>
      <textarea name="descripcion" placeholder="Escribe una breve descripción" required><?= htmlspecialchars($c['descripcion']) ?></textarea>
    </label>

    <label>
      <span class="label-text">URL de YouTube:</span>
      <input name="enlace_youtube" placeholder="https://www.youtube.com/watch?v=..." required value="<?= htmlspecialchars($c['enlace_youtube']) ?>">
    </label>

    <button type="submit">Guardar Cambios</button>
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
