<?php
include '../includes/db.php';

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = trim($_POST['description']);
    $imagePath = null;

    // Validación básica
    if (empty($name) || empty($category) || $price < 0 || $stock < 0) {
        $mensaje = "❌ Datos inválidos. Asegúrate de completar todos los campos y usar valores positivos.";
    } else {
        // Validación y subida de imagen
        if (!empty($_FILES['image']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxSize) {
                $imageName = time() . '_' . basename($_FILES['image']['name']);
                $targetDir = "../uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $targetPath = $targetDir . $imageName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = "uploads/" . $imageName;
                } else {
                    $mensaje = "❌ Error al subir la imagen.";
                }
            } else {
                $mensaje = "❌ Imagen no válida. Solo JPG, PNG, WEBP menores a 2MB.";
            }
        }

        
        if (!$mensaje) {
            $sql = "INSERT INTO products (name, category, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $category, $price, $stock, $description, $imagePath]);

            header("Location: add.php?success=1");
            exit;
        }
    }
}

if (isset($_GET['success'])) {
    $mensaje = "✅ Producto agregado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Producto</title>
  <style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(to right, #fdfbfb, #ebedee);
    padding: 40px;
    margin: 0;
  }

  h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
  }

  form {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  label {
    font-weight: bold;
    margin-top: 15px;
    display: block;
    color: #555;
  }

  input, textarea, select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }

  button {
    margin-top: 20px;
    padding: 12px;
    background-color: #28a745;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
  }

  button:hover {
    background-color: #218838;
  }

  .msg {
    color: green;
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
  }

  a {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #007bff;
    text-decoration: none;
  }

  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

  <h2 style="text-align:center;">Agregar Producto</h2>

  <?php if ($mensaje): ?>
    <p class="msg <?= str_starts_with($mensaje, '✅') ? 'success' : 'error' ?>">
      <?= $mensaje ?>
    </p>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <label>Nombre:</label>
    <input type="text" name="name" required>

    <label>Categoría:</label>
    <input type="text" name="category" required>

    <label>Precio:</label>
    <input type="number" step="0.01" name="price" min="0" required>

    <label>Stock:</label>
    <input type="number" name="stock" min="0" required>

    <label>Descripción:</label>
    <textarea name="description" rows="3"></textarea>

    <label>Imagen:</label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">

    <button type="submit">Guardar</button>
  </form>

  <a href="list.php">← Volver a la lista</a>

</body>
</html>