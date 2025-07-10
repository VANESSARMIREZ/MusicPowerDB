<?php
include 'conexion.php';
if (!isset($_GET['id'])) exit('ID no especificado.');
$id = intval($_GET['id']);

$stm = $pdo->prepare("SELECT * FROM canciones WHERE id = ?");
$stm->execute([$id]);
$c = $stm->fetch(PDO::FETCH_ASSOC);

if (!$c) exit('Canción no encontrada');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($c['titulo']) ?> – <?= htmlspecialchars($c['artista']) ?></title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
</head>
<body>
  <div class="container">

  <?php
    $volver = isset($_GET['volver']) ? $_GET['volver'] : 'index.php';
  ?>
  <a href="<?= htmlspecialchars($volver) ?>" class="volver-fijo"><i class="fas fa-arrow-left"></i> Volver</a>

  <div class="header detalle-header">
    <a href="index.php"><img src="logo.png" class="logo" alt="Logo"/></a>
    <div class="acciones-detalle">
      <?php if ($c['tipo'] === 'recomendacion'): ?>
        <a href="editar-recomendacion.php?id=<?= $c['id'] ?>"><i class="fas fa-edit"></i> Editar</a>
      <?php else: ?>
        <a href="editar-top.php?id=<?= $c['id'] ?>"><i class="fas fa-edit"></i> Editar</a>
      <?php endif; ?>

      <!-- Botón que activa la alerta de confirmación -->
      <a href="#" onclick="confirmarEliminacion(<?= $c['id'] ?>); return false;">
        <i class="fas fa-trash"></i> Eliminar
      </a>
    </div>
  </div>

  <main class="contenido-detalle">
    <div class="detalle-info">
      <div class="detalle-cabecera">
        <div class="miniatura-circular">
          <img src="<?= htmlspecialchars($c['imagen_url']) ?>" alt="Imagen">
        </div>
        <h2><?= htmlspecialchars($c['titulo']) ?></h2>
        <h3><?= htmlspecialchars($c['artista']) ?></h3>
      </div>

      <div class="bloque-descripcion">
        <p class="texto-descripcion"><?= htmlspecialchars($c['descripcion']) ?></p>
        <?php if (!empty($c['enviado_por'])): ?>
          <p class="texto-recomendado"><strong>Recomendado por:</strong> <?= htmlspecialchars($c['enviado_por']) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="detalle-video">
      <?php preg_match('/v=([^&]+)/', $c['enlace_youtube'], $m); $vid = $m[1] ?? ''; ?>
      <iframe src="https://www.youtube.com/embed/<?= $vid ?>" frameborder="0" allowfullscreen></iframe>

      <a class="botonyoutube" href="<?= htmlspecialchars($c['enlace_youtube']) ?>" target="_blank">
        <img src="youtube.png" class="youtube-icon"> Ver en YouTube
      </a>
    </div>
  </main>

  <footer>
    <p>Curso: Conceptualización de servicios en la nube</p>
    <p>Nombre: Vanessa Itzarahí Gómez Ramírez</p>
    <p>Código: 218752859</p>
    <p>Correo: vanessa.gomez2859@alumnos.udg.mx</p>
  </footer>
</div>

<script>
function confirmarEliminacion(id) {
  Swal.fire({
    title: '¿Estás segura de eliminar esta canción?',
    text: "No podrás revertir esta acción.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#aaa',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = 'eliminar.php?id=' + id;
    }
  });
}
</script>

</body>
</html>
