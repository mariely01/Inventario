<?php
include 'includes/db.php';

$username = 'admin';
$password = 'admin123'; 
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->execute([$username]);
$existe = $stmt->fetch();

if ($existe) {
    echo "⚠️ El usuario ya existe.";
} else {
    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);
    echo "✅ Usuario creado correctamente. Usuario: admin, Contraseña: admin123";
}
?>