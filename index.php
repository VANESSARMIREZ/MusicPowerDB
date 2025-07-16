<?php
include 'conexion.php';

// Configuración de paginación
$canciones_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $canciones_por_pagina;

// Total de canciones tipo 'top'
$stmtCount = $pdo->query("SELECT COUNT(*) FROM canciones WHERE tipo = 'top'");
$total_canciones = $stmtCount->fetchColumn();
$total_paginas = ceil($total_canciones / $canciones_por_pagina);

// Recomendaciones
$stmRec = $pdo->query("SELECT * FROM canciones WHERE tipo = 'recomendacion' ORDER BY orden_mes ASC");
$recs = $stmRec->fetchAll(PDO::FETCH_ASSOC);

// Top canciones paginadas
$stmtPag = $pdo->prepare("SELECT * FROM canciones WHERE tipo = 'top' ORDER BY artista, titulo LIMIT :limit OFFSET :offset");
$stmtPag->bindValue(':limit', $canciones_por_pagina, PDO::PARAM_INT);
$stmtPag->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmtPag->execute();
$canciones = $stmtPag->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <title>Music Power</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="icon" href="icon.png"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="fondo"></div>
<div class="container">

  <!-- Encabezado -->
  <div class="header">
    <a href="."><img src="logo.png" class="logo" alt="Logo"/></a>
<nav class="menu">

<a href="listacompleta.php" class="btn-buscar-canciones" style="font-weight:bold; color:#ff008c; margin-left: 15px;">
  <i class="fas fa-search"></i> Buscar canciones
</a>

  <a href="#recomendaciones">Inicio</a>

  <div class="menu-item">
    <a href="#top-canciones" class="top-link">Top 2025</a>
    <div class="submenu-top">
      <a href="listacompleta.php">Lista completa</a>
    </div>
  </div>


  <a href="#contacto">Contacto</a>
  <span class="separador">|</span>

  <div class="menu-hamb">
    ☰
    <div class="submenu">
      <a href="agregarrecomendacion.php?tipo=recomendacion">Agregar<br>Recomendación</a>
      <a href="agregartop.php?tipo=top">Agregar<br>Canción</a>
    </div>
  </div>
</nav>

  </div>

  <main>

    <!-- Recomendaciones -->
    <section id="recomendaciones" class="recomendacion">
      <h2>Recomendaciones del mes</h2>
      <div class="slider">
        <?php foreach($recs as $i => $c): ?>
          <div class="slide <?= $i===0?'active':'' ?>" onclick="window.location='detalle.php?id=<?= $c['id'] ?>'">
            <p class="recom-sent">
              <?= !empty($c['recomendacion_text']) ? htmlspecialchars($c['recomendacion_text']) :
                'Te recomiendo la canción "'.htmlspecialchars($c['titulo']).'" de '.htmlspecialchars($c['artista']).'.'; ?>
            </p>

            <div class="descripcion">
              <h3><?=htmlspecialchars($c['titulo']).' – '.htmlspecialchars($c['artista'])?></h3>
              <p><?=htmlspecialchars($c['descripcion'])?></p>
            </div>

            <div class="video-embed">
              <?php preg_match('/v=([^&]+)/',$c['enlace_youtube'],$m); $vid = $m[1] ?? ''; ?>
              <iframe src="https://www.youtube.com/embed/<?= $vid ?>" frameborder="0" allowfullscreen></iframe>
            </div>

            <a class="botonyoutube" href="<?= htmlspecialchars($c['enlace_youtube'])?>" target="_blank" onclick="event.stopPropagation();">
              <img src="youtube.png" class="youtube-icon"> Ver en YouTube
            </a>

            <div class="boton-detalles-container" onclick="event.stopPropagation();">
              <a class="boton-detalles" href="detalle.php?id=<?= $c['id'] ?>">
                <i class="fas fa-search"></i> <span>Ver detalles</span>
              </a>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="nav-flechas">
          <button id="prev">←</button>
          <button id="next">→</button>
        </div>
      </div>
    </section>

    <!-- Separador -->
    <div style="height:4px; margin:40px 0; background:linear-gradient(to right,#ff008c,#FFD700)"></div>

    <!-- Top canciones -->
    <section id="top-canciones" class="top-canciones">
      <h2 data-text="Top canciones más escuchadas 2025">Top canciones más escuchadas 2025</h2>
      <div class="lista-canciones">
        <?php foreach($canciones as $c): ?>
          <article class="cancion" onclick="window.location='detalle.php?id=<?= $c['id'] ?>'">
            <img src="<?=htmlspecialchars($c['imagen_url'])?>" alt="miniatura"/>
            <div class="info">
              <h3><?=htmlspecialchars($c['titulo']).' – '.htmlspecialchars($c['artista'])?></h3>
              <p><?=htmlspecialchars($c['descripcion'])?></p>
              <div class="acciones-mini">
                <a class="botonyoutube" href="<?=htmlspecialchars($c['enlace_youtube'])?>" target="_blank" onclick="event.stopPropagation();">
                  <img src="youtube.png" class="youtube-icon"> Ver en YouTube
                </a>
                <a class="boton-detalles boton-detalles-top" href="detalle.php?id=<?= $c['id'] ?>" onclick="event.stopPropagation();">
                  <i class="fas fa-search"></i> <span>Ver detalles</span>
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>

        <!-- Paginación -->
        <div class="paginacion">
          <?php if ($pagina_actual > 1): ?>
            <a href="?pagina=<?= $pagina_actual - 1 ?>#top-canciones" class="btn-pag">« Anterior</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <?php if ($i == $pagina_actual): ?>
              <span class="btn-pag activo"><?= $i ?></span>
            <?php else: ?>
              <a href="?pagina=<?= $i ?>#top-canciones" class="btn-pag"><?= $i ?></a>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($pagina_actual < $total_paginas): ?>
            <a href="?pagina=<?= $pagina_actual + 1 ?>#top-canciones" class="btn-pag">Siguiente »</a>
          <?php endif; ?>
        </div>

        <!-- Botón para ver lista completa -->
        <div style="text-align:center; margin-top: 1rem;">
          <a href="listacompleta.php" class="btn-ver-lista">Ver lista completa de canciones</a>
        </div>
      </div>
    </section>

    <!-- Separador -->
    <div style="height:4px; margin:40px 0; background:linear-gradient(to right,#ff008c,#FFD700)"></div>

    <!-- Contacto -->
    <section id="contacto" class="contacto">
      <h2>Contacto</h2>
      <p>¿Quieres recomendar alguna canción? Escríbeme a:</p>
      <p><a href="mailto:vanessa.gomez2859@alumnos.udg.mx">vanessa.gomez2859@alumnos.udg.mx</a></p>
    </section>

  </main>

  <footer>
    <p>Curso: Conceptualización de servicios en la nube</p>
    <p>Nombre: Vanessa Itzarahí Gómez Ramírez</p>
    <p>Código: 218752859</p>
    <p>Correo: vanessa.gomez2859@alumnos.udg.mx</p>
  </footer>
</div>

<script>
  let idx = 0, slides = document.querySelectorAll('.slider .slide');
  document.getElementById('prev').onclick = () => {
    slides[idx].classList.remove('active');
    idx = (idx - 1 + slides.length) % slides.length;
    slides[idx].classList.add('active');
  };
  document.getElementById('next').onclick = () => {
    slides[idx].classList.remove('active');
    idx = (idx + 1) % slides.length;
    slides[idx].classList.add('active');
  };
</script>
</body>
</html>
