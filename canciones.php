<?php
include('db.php');
$stmt = $pdo->query("SELECT * FROM canciones ORDER BY id");
$canciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<ul>
<?php foreach ($canciones as $cancion): ?>
    <li>
        <strong><?= $cancion['titulo'] ?></strong> - <?= $cancion['artista'] ?>
        <a href="editar.php?id=<?= $cancion['id'] ?>">Editar</a> |
        <a href="eliminar.php?id=<?= $cancion['id'] ?>" onclick="return confirm('¿Eliminar esta canción?');">Eliminar</a>
    </li>
<?php endforeach; ?>
</ul>
<a href="agregar.php">Agregar nueva canción</a>
