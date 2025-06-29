<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}
include '../includes/db.php';

$id = $_GET['id'] ?? null;
$mensaje = '';

if (!$id) {
    die("ID no válido");
}

// Obtener producto actual
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Producto no encontrado");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = $_POST['description'];

    // Manejo de imagen
    $imagePath = $product['image']; 
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetPath = $targetDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = "uploads/" . $imageName;
        }
    }

    // Actualizar producto
    $sql = "UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $category, $price, $stock, $description, $imagePath, $id]);

    // Redirigir para evitar reenvío del formulario
    header("Location: edit.php?id=$id&success=1");
    exit;
}

if (isset($_GET['success'])) {
    $mensaje = "✅ Producto actualizado correctamente.";
}

// Recargar producto actualizado para mostrar en formulario
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
    form { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px #ccc; }
    label { display: block; margin-top: 10px; }
    input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
    button { margin-top: 15px; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; width: 100%; }
    .msg { color: green; text-align: center; font-weight: bold; margin-bottom: 15px; }
    a { display: block; text-align: center; margin-top: 10px; color: #007bff; text-decoration: none; }
    img { max-width: 150px; margin-top: 10px; border-radius: 5px; }
  </style>
</head>
<body>

  <h2 style="text-align:center;">Editar Producto</h2>

  <?php if ($mensaje): ?>
    <p class="msg"><?= $mensaje ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Nombre:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label>Categoría:</label>
    <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>

    <label>Precio:</label>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>

    <label>Stock:</label>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required>

    <label>Descripción:</label>
    <textarea name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>

    <label>Imagen actual:</label><br>
    <?php if (!empty($product['image'])): ?>
      <img src="../<?= htmlspecialchars($product['image']) ?>" alt="Imagen actual">
    <?php else: ?>
      <p>Sin imagen</p>
    <?php endif; ?>

    <label>Cambiar imagen:</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Guardar Cambios</button>
  </form>

  <a href="list.php">← Volver a la lista</a>

</body>
</html>