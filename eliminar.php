<?php
include 'conexion.php';
if (!isset($_GET['id'])) exit;
$pdo->prepare("DELETE FROM canciones WHERE id = ?")->execute([intval($_GET['id'])]);
header('Location: index.php');
