<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Guardar nuevo registro
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $sexo = $_POST['sexo'];

    $stmt = $conn->prepare("INSERT INTO beneficiarios (nombre, apellido, cedula, fecha_nacimiento, direccion, telefono, correo, sexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nombre, $apellido, $cedula, $fecha_nacimiento, $direccion, $telefono, $correo, $sexo);
    if (!$stmt->execute()) {
        echo "Error al registrar: " . $stmt->error;
    } else {
        $stmt->close();
        header("Location: registro.php?ok=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Beneficiario</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="logout-bar">
        Usuario: <b><?= htmlspecialchars($_SESSION['usuario']) ?></b> |
        <a href="beneficiarios_activos.php" class="mostrar-todos"><i class="fa-solid fa-users"></i> Ver beneficiarios</a> |
        <a href="buscar_beneficiarios.php" class="mostrar-todos"><i class="fa-solid fa-magnifying-glass"></i> Buscar</a> |
        <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
    </div>
    <form method="post" action="registro.php">
        <h2><i class="fa-solid fa-user-plus"></i> Registrar Beneficiario</h2>
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        <label>Apellido:</label>
        <input type="text" name="apellido" required>
        <label>Cédula:</label>
        <input type="text" name="cedula" required>
        <label>Fecha de nacimiento:</label>
        <input type="date" name="fecha_nacimiento" required>
        <label>Dirección:</label>
        <textarea name="direccion" rows="2" required></textarea>
        <label>Teléfono:</label>
        <input type="text" name="telefono" required>
        <label>Correo:</label>
        <input type="email" name="correo" required>
        <label>Sexo:</label>
        <select name="sexo" required>
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
        </select>
        <button class="guardar" type="submit" name="guardar"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
    </form>
    <?php if (isset($_GET['ok'])): ?>
        <p class="success"><i class="fa-solid fa-circle-check"></i> ¡Beneficiario registrado correctamente!</p>
    <?php endif; ?>
</div>
</body>
</html>