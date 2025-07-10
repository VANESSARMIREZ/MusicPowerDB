<?php
include 'conexion.php';

// Procesar borrado múltiple
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids_eliminar'])) {
    $ids = $_POST['ids_eliminar'];
    if (!empty($ids) && is_array($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("DELETE FROM canciones WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        header("Location: listacompleta.php");
        exit;
    }
}

// No procesamos edición en esta página (solo redirigir con id)

// Obtener canciones
$stmt = $pdo->query("SELECT * FROM canciones WHERE tipo = 'top' ORDER BY artista, titulo");
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<title>Lista completa de canciones - Music Power</title>
<link rel="stylesheet" href="style.css"/>
<link rel="icon" href="icon.png"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>

<div class="container">

<!-- Header -->
<div class="header detalle-header" style="display:flex; align-items:center;">
  <a href="index.php"><img src="logo.png" class="logo" alt="Logo"/></a>

  <div class="acciones-detalle">
  <button id="btn-editar"><i class="fas fa-edit"></i> Editar</button>
  <a href="agregartop.php" class="boton-agregar">
    <i class="fas fa-plus"></i> Agregar
  </a>
  <button id="btn-eliminar"><i class="fas fa-trash"></i> Eliminar</button>
  <button id="btn-cancelar" style="display:none;">Cancelar</button>

  <!-- Controles edición -->
  <button type="button" class="confirmar accion-contextual" id="confirmar-editar" style="display: none;">
    Editar canción seleccionada
  </button>

  <!-- Controles eliminación -->
  <button type="button" class="confirmar eliminar accion-contextual" id="confirmar-eliminar" style="display: none;">
    Eliminar canciones seleccionadas
  </button>
</div>

</div>


  <a href="index.php" class="volver-fijo"><i class="fas fa-arrow-left"></i> Volver al inicio</a>

<main>
   <h1 class="titulo-lista">Lista Completa de Canciones </h1>
  
  <form id="form-acciones" method="POST">
    <div class="lista-canciones lista-completa">

      <?php $i = 1; foreach($canciones as $c): ?>
        <article class="cancion fila-cancion" data-id="<?= $c['id'] ?>" onclick="onClickCancion(event, <?= $c['id'] ?>)">
          <input type="checkbox" class="chk-select" name="ids_eliminar[]" value="<?= $c['id'] ?>" />
          <div class="numero"><?= $i++ ?></div>
          <div class="miniatura">
            <img src="<?= htmlspecialchars($c['imagen_url']) ?>" alt="miniatura">
          </div>
          <div class="datos">
            <div class="titulo-artista">
              <strong><?= htmlspecialchars($c['titulo']) ?></strong> – <?= htmlspecialchars($c['artista']) ?>
            </div>
          </div>
          <div class="acciones-mini">
            <a class="botonyoutube" href="<?= htmlspecialchars($c['enlace_youtube']) ?>" target="_blank" onclick="event.stopPropagation();">
              <img src="youtube.png" class="youtube-icon"> YouTube
            </a>
<a class="boton-detalles" href="detalle.php?id=<?= $c['id'] ?>" onclick="event.stopPropagation();">
  <i class="fas fa-search"></i> <span>Ver Detalles</span>
</a>

          </div>
        </article>

<!-- Separador excepto después de la última canción -->
  <?php if ($i <= count($canciones)) : ?>
    <div style="height:2px; margin:0 0 10px 0; width: 103%; background:linear-gradient(to right,#ff008c,#FFD700)"></div>

  <?php endif; ?>
<?php endforeach; ?>
    </div>
  </form>
</main>


  <footer>
    <p>Curso: Conceptualización de servicios en la nube</p>
    <p>Nombre: Vanessa Itzarahí Gómez Ramírez</p>
    <p>Código: 218752859</p>
    <p>Correo: vanessa.gomez2859@alumnos.udg.mx</p>
  </footer>
</div>
<script>
  const body = document.body;
  const btnEditar = document.getElementById('btn-editar');
  const btnEliminar = document.getElementById('btn-eliminar');
  const btnCancelar = document.getElementById('btn-cancelar');

  const confirmarEditar = document.getElementById('confirmar-editar');
  const confirmarEliminar = document.getElementById('confirmar-eliminar');

  const form = document.getElementById('form-acciones');
  const checkboxes = form.querySelectorAll('.chk-select');

  // Modo actual: "ninguno", "editar", "eliminar"
  let modoActual = "ninguno";

  btnEditar.addEventListener('click', () => {
    activarModo('editar');
  });
  btnEliminar.addEventListener('click', () => {
    activarModo('eliminar');
  });
  btnCancelar.addEventListener('click', () => {
    desactivarModos();
  });

  function activarModo(modo) {
    modoActual = modo;
    body.classList.remove('modo-editar', 'modo-eliminar');

    if (modo === 'editar') {
      body.classList.add('modo-editar');
      confirmarEditar.style.display = 'inline-block';
      confirmarEliminar.style.display = 'none';
    } else if (modo === 'eliminar') {
      body.classList.add('modo-eliminar');
      confirmarEditar.style.display = 'none';
      confirmarEliminar.style.display = 'inline-block';
    }

    btnCancelar.style.display = 'inline-block';
    btnEditar.style.display = 'none';
    btnEliminar.style.display = 'none';

    // Mostrar checkboxes
    checkboxes.forEach(chk => {
      chk.checked = false;
      chk.style.display = 'inline-block';
    });
  }

  function desactivarModos() {
    modoActual = "ninguno";
    body.classList.remove('modo-editar', 'modo-eliminar');

    confirmarEditar.style.display = 'none';
    confirmarEliminar.style.display = 'none';
    btnCancelar.style.display = 'none';
    btnEditar.style.display = 'inline-block';
    btnEliminar.style.display = 'inline-block';

    // Ocultar y desmarcar checkboxes
    checkboxes.forEach(chk => {
      chk.checked = false;
      chk.style.display = 'none';
    });
  }

  // Control para que en modo editar solo puedas seleccionar 1 checkbox
  checkboxes.forEach(chk => {
    chk.addEventListener('change', (e) => {
      if (modoActual === 'editar') {
        if (e.target.checked) {
          checkboxes.forEach(c => {
            if (c !== e.target) c.checked = false;
          });
        }
      }
    });
  });

  // Click en canción: en modo normal va a detalles, en modo con checkbox no hace nada extra
  function onClickCancion(event, id) {
    if (modoActual === "ninguno") {
      window.location.href = 'detalle.php?id=' + id;
    } else {
      event.stopPropagation(); // evita navegación
    }
  }

  // Confirmar edición
  confirmarEditar.addEventListener('click', () => {
    const seleccionado = Array.from(checkboxes).find(chk => chk.checked);
    if (!seleccionado) {
      Swal.fire({
        icon: 'warning',
        title: 'Selecciona una canción para editar',
      });
      return;
    }
    // Redirigir a editar.php con el ID
    window.location.href = 'editar-top.php?id=' + seleccionado.value;
  });

  // Confirmar eliminación múltiple con SweetAlert2
  confirmarEliminar.addEventListener('click', () => {
    const seleccionados = Array.from(checkboxes).filter(chk => chk.checked);
    if (seleccionados.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Selecciona al menos una canción para eliminar',
      });
      return;
    }

    Swal.fire({
      title: `¿Eliminar ${seleccionados.length} canción(es)?`,
      text: "Esta acción no se puede deshacer.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#aaa',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Crear y enviar un formulario dinámico con los IDs seleccionados
        const formPost = document.createElement('form');
        formPost.method = 'POST';
        formPost.action = 'listacompleta.php';

        seleccionados.forEach(chk => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'ids_eliminar[]';
          input.value = chk.value;
          formPost.appendChild(input);
        });

        document.body.appendChild(formPost);
        formPost.submit();
      }
    });
  });
</script>

</body>
</html>
