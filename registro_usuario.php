<?php
include 'db.php';
$mensaje = "";

if (isset($_POST['registro'])) {
    $usuario = $_POST['usuario'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $mensaje = "El usuario ya existe.";
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contrasena) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $contrasena);
        $stmt->execute();
        $mensaje = "Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="estilos_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-bg">
    <div class="login-box">
        <form method="post" class="login-form">
            <h2><i class="fa-solid fa-user-plus"></i> Registro</h2>
            <div class="input-icon">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="usuario" placeholder="Usuario" required autofocus>
            </div>
            <div class="input-icon">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
            </div>
            <button type="submit" name="registro"><i class="fa-solid fa-user-plus"></i> Registrar</button>
            <a href="login.php" class="registro-link"><i class="fa-solid fa-right-to-bracket"></i> ¿Ya tienes cuenta? Inicia sesión</a>
            <?php if ($mensaje): ?>
                <div class="error-message"><i class="fa-solid fa-circle-exclamation"></i> <?= $mensaje ?></div>
            <?php endif; ?>
        </form>
    </div>
</div>
</body>
</html>