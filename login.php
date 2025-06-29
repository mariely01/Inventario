<?php
session_start();

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin'] = $username;
        header('Location: products/list.php');
        exit;
    } else {
        $mensaje = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login Administrador</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(to right, #c9d6ff, #e2e2e2);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }

  form {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 400px;
  }

  h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
  }

  label {
    font-weight: bold;
    margin-top: 10px;
    display: block;
    color: #555;
  }

  input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }

  button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background-color: #007bff;
    border: none;
    color: white;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
  }

  button:hover {
    background-color: #0056b3;
  }

  .msg {
    color: red;
    margin-top: 15px;
    font-weight: bold;
    text-align: center;
  }
</style>
</head>
<body>

<h2 style="text-align:center;">Login Administrador</h2>

<form method="POST" action="">
    <label>Usuario:</label>
    <input type="text" name="username" required>

    <label>Contraseña:</label>
    <input type="password" name="password" required>

    <button type="submit">Ingresar</button>

    <?php if ($mensaje): ?>
        <p class="msg"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
</form>

</body>
</html>