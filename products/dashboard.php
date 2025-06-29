<?php
include '../includes/db.php';

// Total de productos
$totalProductos = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Total stock
$totalStock = $pdo->query("SELECT SUM(stock) FROM products")->fetchColumn();

// Valor total del inventario
$totalValor = $pdo->query("SELECT SUM(price * stock) FROM products")->fetchColumn();

// Producto con más stock
$masStock = $pdo->query("SELECT name, stock FROM products ORDER BY stock DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; background: #eef2f7; padding: 20px; }
    h1 { text-align: center; }
    .panel {
      max-width: 600px;
      margin: 20px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
    }
    .panel h2 {
      margin: 0 0 10px;
      color: #333;
    }
    .item {
      margin-bottom: 15px;
      font-size: 18px;
    }
    a {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #007bff;
      text-decoration: none;
    }
  </style>
</head>
<body>

   <p style="text-align:right; margin-right: 20px;">
    Usuario: <?= htmlspecialchars($_SESSION['admin']) ?> | <a href="../logout.php">Cerrar sesión</a>
</p>
  <h1>Resumen del Inventario</h1>

  <div class="panel">
    <div class="item">📦 Total de productos: <strong><?= $totalProductos ?></strong></div>
    <div class="item">📊 Stock total: <strong><?= $totalStock ?></strong></div>
    <div class="item">💰 Valor total: <strong>$<?= number_format($totalValor, 2) ?></strong></div>
    <div class="item">🏆 Producto con más stock: <strong><?= htmlspecialchars($masStock['name']) ?></strong> (<?= $masStock['stock'] ?> unidades)</div>
  </div>

  <a href="list.php">← Volver al inventario</a>
</body>
</html>