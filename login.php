<?php
include 'db.php';
session_start();
$mensaje = "";

if (isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $stmt = $conn->prepare("SELECT id, contrasena FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();
        if (password_verify($contrasena, $hash)) {
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario'] = $usuario;
            header("Location: registro.php");
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "Usuario no encontrado.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="estilos_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-bg">
    <div class="login-box">
        <form method="post" class="login-form">
            <h2><i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión</h2>
            <div class="input-icon">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="usuario" placeholder="Usuario" required autofocus>
            </div>
            <div class="input-icon">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
            </div>
            <button type="submit" name="login"><i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar</button>
            <a href="registro_usuario.php" class="registro-link"><i class="fa-solid fa-user-plus"></i> ¿No tienes cuenta? Regístrate</a>
            <?php if ($mensaje): ?>
                <div class="error-message"><i class="fa-solid fa-circle-exclamation"></i> <?= $mensaje ?></div>
            <?php endif; ?>
        </form>
    </div>
</div>
</body>
</html>