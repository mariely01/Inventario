<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

include '../includes/db.php';  // Conexión a la base de datos

$q = $_GET['q'] ?? '';
$categoriaSeleccionada = $_GET['categoria'] ?? '';

// Obtener todas las categorías para el filtro
$categorias = $pdo->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_COLUMN);

$products = [];

// Construir consulta con filtro y búsqueda combinados
if ($categoriaSeleccionada && $q) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND (name LIKE ? OR category LIKE ?) ORDER BY created_at DESC");
    $stmt->execute([$categoriaSeleccionada, "%$q%", "%$q%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($categoriaSeleccionada) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
    $stmt->execute([$categoriaSeleccionada]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($q) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$q%", "%$q%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Productos</title>
  <style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(to right, #eef2f3, #8e9eab);
    margin: 0;
    padding: 20px;
  }

  h1 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
  }

  .user-info {
    text-align: right;
    margin-bottom: 20px;
    font-weight: bold;
    color: #333;
  }

  .user-info a {
    color: #007bff;
    text-decoration: none;
    margin-left: 10px;
  }

  .add-btn {
    display: block;
    width: fit-content;
    margin: 20px auto;
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    border-radius: 30px;
    transition: background-color 0.3s;
  }

  .add-btn:hover {
    background-color: #218838;
  }

  form.search-filter {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  input[type="text"], select {
    padding: 10px;
    width: 250px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }

  button {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  button:hover {
    background-color: #0056b3;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }

  th {
    background-color: #343a40;
    color: white;
    padding: 12px;
  }

  td {
    padding: 10px;
    border: 1px solid #ddd;
  }

  img {
    max-width: 60px;
    height: auto;
    border-radius: 5px;
  }

  a {
    color: #007bff;
    text-decoration: none;
    margin: 0 5px;
  }

  a:hover {
    text-decoration: underline;
  }

  @media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
      display: block;
    }

    td {
      padding: 10px;
      border: none;
      border-bottom: 1px solid #eee;
      position: relative;
    }

    td::before {
      content: attr(data-label);
      position: absolute;
      left: 10px;
      top: 10px;
      font-weight: bold;
      color: #555;
    }

    th {
      display: none;
    }
  }
</style>
</head>
<body>

  <p class="user-info">
    Usuario: <?= htmlspecialchars($_SESSION['admin']) ?> | <a href="/logout.php">Cerrar sesión</a>
  </p>

  <h1>Inventario de Productos</h1>

  <?php if (isset($_GET['deleted'])): ?>
    <p style="color: green; text-align:center; font-weight: bold; margin-bottom: 15px;">
      ✅ Producto eliminado correctamente.
    </p>
  <?php endif; ?>

  <!-- Formulario combinado búsqueda y filtro -->
  <form method="GET" class="search-filter">
    <input type="text" name="q" placeholder="Buscar producto..." value="<?= htmlspecialchars($q) ?>">
    <select name="categoria">
      <option value="">-- Todas las categorías --</option>
      <?php foreach ($categorias as $cat): ?>
        <option value="<?= htmlspecialchars($cat) ?>" <?= $categoriaSeleccionada === $cat ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Buscar / Filtrar</button>
  </form>

  <a class="add-btn" href="add.php">+ Agregar Producto</a>

  <table>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Categoría</th>
      <th>Precio</th>
      <th>Stock</th>
      <th>Imagen</th>
      <th>Acciones</th>
    </tr>

    <?php if (count($products) === 0): ?>
      <tr><td colspan="7">No hay productos que mostrar.</td></tr>
    <?php else: ?>
      <?php foreach ($products as $product): ?>
        <tr>
          <td><?= htmlspecialchars($product['id']) ?></td>
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td><?= htmlspecialchars($product['category']) ?></td>
          <td>$<?= number_format($product['price'], 2) ?></td>
          <td><?= htmlspecialchars($product['stock']) ?></td>
          <td>
            <?php if ($product['image']): ?>
              <img src="../<?= $product['image'] ?>" alt="Imagen">
            <?php else: ?>
              Sin imagen
            <?php endif; ?>
          </td>
          <td>
            <a href="edit.php?id=<?= $product['id'] ?>">Editar</a> |
            <a href="delete.php?id=<?= $product['id'] ?>" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>

</body>
</html>