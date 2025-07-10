<?php
include 'conexion.php';

if (!isset($_GET['id'])) {
    exit('ID no especificado.');
}

$id = intval($_GET['id']);

// Obtener datos actuales
$stmt = $pdo->prepare("SELECT * FROM canciones WHERE id = ?");
$stmt->execute([$id]);
$c = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$c) {
    exit('Canción no encontrada');
}

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

    // Mantener orden_mes actual (no se cambia en edición)
    $orden = $c['orden_mes'];

    // Mantener fecha_recomendacion actual
    $fecha = $c['fecha_recomendacion'];

    // Actualizar base de datos
    $stmtUpdate = $pdo->prepare("UPDATE canciones SET
        titulo = ?, artista = ?, descripcion = ?, enlace_youtube = ?, imagen_url = ?, enviado_por = ?, recomendacion_text = ?
        WHERE id = ?");
    $stmtUpdate->execute([
        $titulo,
        $artista,
        $descripcion,
        $enlace,
        $imagen,
        $enviado_por,
        $recomendacion_text,
        $id
    ]);

    // Redirigir a detalle.php para ver los cambios
    header('Location: detalle.php?id=' . $id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Recomendación</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="icon.png"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="header detalle-header">
      <a href="index.php"><img src="logo.png" class="logo" alt="Logo"/></a>
    </div>

<div class="agregar-recomendacion-container">

    <!-- Volver fijo arriba a la izquierda -->
    <a href="detalle.php?id=<?= $id ?>" class="volver-fijo"><i class="fas fa-arrow-left"></i> Volver</a>

    <!-- Título -->
    <h2>Editar Recomendación del Mes</h2>

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
    <textarea name="descripcion" placeholder="Descripción de la canción" required><?= htmlspecialchars($c['descripcion']) ?></textarea>
  </label>

  <label>
    <span class="label-text">Enlace YouTube:</span>
    <input name="enlace_youtube" placeholder="URL de YouTube (https://...)" required value="<?= htmlspecialchars($c['enlace_youtube']) ?>">
  </label>

  <label>
    <span class="label-text">Texto personalizado:</span>
    <textarea name="recomendacion_text" placeholder="Texto personalizado (opcional)"><?= htmlspecialchars($c['recomendacion_text']) ?></textarea>
  </label>

  <label>
    <span class="label-text">Recomendado por:</span>
    <input name="enviado_por" placeholder="Nombre de quien recomienda la canción (opcional)" value="<?= htmlspecialchars($c['enviado_por']) ?>">
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
