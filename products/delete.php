<?php
include '../includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID no válido");
}

// Eliminar producto
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

// Redirigir a la lista con mensaje
header("Location: list.php?deleted=1");
exit;
