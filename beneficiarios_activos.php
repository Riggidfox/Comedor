<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Desactivar beneficiario
if (isset($_GET['desactivar'])) {
    $id = intval($_GET['desactivar']);
    $stmt = $conn->prepare("UPDATE beneficiarios SET activo=0 WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: beneficiarios_activos.php");
    exit();
}

// Para edición, cargar datos actuales del registro
$editar = false;
if (isset($_GET['edit'])) {
    $editar = true;
    $id = intval($_GET['edit']);
    $query = $conn->query("SELECT * FROM beneficiarios WHERE id = $id LIMIT 1");
    $data = $query->fetch_assoc();
}

// Actualizar registro editado
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $sexo = $_POST['sexo'];

    $stmt = $conn->prepare("UPDATE beneficiarios SET nombre=?, apellido=?, cedula=?, fecha_nacimiento=?, direccion=?, telefono=?, correo=?, sexo=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $nombre, $apellido, $cedula, $fecha_nacimiento, $direccion, $telefono, $correo, $sexo, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: beneficiarios_activos.php");
    exit();
}

// Lógica de búsqueda y mostrar todos
$busqueda = "";
$mostrar_todos = isset($_GET['mostrar_todos']);
if (isset($_GET['buscar']) && !$mostrar_todos) {
    $busqueda = $_GET['buscar'];
    $sql = "SELECT * FROM beneficiarios WHERE activo=1 AND (nombre LIKE ? OR apellido LIKE ? OR cedula LIKE ?) ORDER BY fecha_registro DESC";
    $stmt = $conn->prepare($sql);
    $b = "%$busqueda%";
    $stmt->bind_param("sss", $b, $b, $b);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} elseif ($mostrar_todos) {
    // Mostrar todos (activos y desactivados)
    $result = $conn->query("SELECT * FROM beneficiarios ORDER BY fecha_registro DESC");
} else {
    // Solo activos por defecto
    $result = $conn->query("SELECT * FROM beneficiarios WHERE activo=1 ORDER BY fecha_registro DESC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Beneficiarios Activos y Desactivados</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="logout-bar">
        Usuario: <b><?= htmlspecialchars($_SESSION['usuario']) ?></b> |
        <a href="registro.php" class="mostrar-todos"><i class="fa-solid fa-user-plus"></i> Registrar</a> |
        <a href="buscar_beneficiarios.php" class="mostrar-todos"><i class="fa-solid fa-magnifying-glass"></i> Buscar</a> |
        <a href="beneficiarios_desactivados.php" class="mostrar-todos"><i class="fa-solid fa-user-slash"></i> Ver desactivados</a> |
        <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
    </div>
    <button class="imprimir" onclick="window.print();">
      <i class="fa-solid fa-print"></i> Imprimir
    </button>
    <form method="get" action="beneficiarios_activos.php" class="buscar-form">
        <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o cédula" value="<?= htmlspecialchars($busqueda) ?>">
        <button class="buscar" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
        <a href="beneficiarios_activos.php?mostrar_todos=1" class="mostrar-todos"><i class="fa-solid fa-list"></i> Mostrar todos</a>
    </form>
    <h3>Beneficiarios <?= $mostrar_todos ? "Activos y Desactivados" : "Activos" ?></h3>
    <table>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Cédula</th>
            <th>Fecha de Nacimiento</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Sexo</th>
            <th>Fecha Registro</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        <?php
        $contador = 1;
        while($row = $result->fetch_assoc()):
        ?>
        <?php if ($editar && $row['id'] == $data['id']): ?>
        <form method="post" action="beneficiarios_activos.php">
        <tr>
            <td><?= $contador ?></td>
            <td><input type="text" name="nombre" required value="<?= htmlspecialchars($data['nombre']) ?>"></td>
            <td><input type="text" name="apellido" required value="<?= htmlspecialchars($data['apellido']) ?>"></td>
            <td><input type="text" name="cedula" required value="<?= htmlspecialchars($data['cedula']) ?>"></td>
            <td><input type="date" name="fecha_nacimiento" required value="<?= htmlspecialchars($data['fecha_nacimiento']) ?>"></td>
            <td><textarea name="direccion" rows="1" required><?= htmlspecialchars($data['direccion']) ?></textarea></td>
            <td><input type="text" name="telefono" required value="<?= htmlspecialchars($data['telefono']) ?>"></td>
            <td><input type="email" name="correo" required value="<?= htmlspecialchars($data['correo']) ?>"></td>
            <td>
                <select name="sexo" required>
                    <option value="Masculino" <?= $data['sexo']=='Masculino' ? 'selected' : '' ?>>Masculino</option>
                    <option value="Femenino" <?= $data['sexo']=='Femenino' ? 'selected' : '' ?>>Femenino</option>
                </select>
            </td>
            <td><?= htmlspecialchars($data['fecha_registro']) ?></td>
            <td><?= $row['activo'] ? '<span style="color:green;font-weight:bold;"><i class="fa-solid fa-check-circle"></i> Activo</span>' : '<span style="color:red;font-weight:bold;"><i class="fa-solid fa-ban"></i> Desactivado</span>'; ?></td>
            <td>
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <button class="guardar" type="submit" name="actualizar"><i class="fa-solid fa-floppy-disk"></i></button>
                <a href="beneficiarios_activos.php" class="cancelar"><i class="fa-solid fa-xmark"></i></a>
            </td>
        </tr>
        </form>
        <?php else: ?>
        <tr>
            <td><?= $contador ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['apellido']) ?></td>
            <td><?= htmlspecialchars($row['cedula']) ?></td>
            <td><?= htmlspecialchars($row['fecha_nacimiento']) ?></td>
            <td><?= htmlspecialchars($row['direccion']) ?></td>
            <td><?= htmlspecialchars($row['telefono']) ?></td>
            <td><?= htmlspecialchars($row['correo']) ?></td>
            <td><?= htmlspecialchars($row['sexo']) ?></td>
            <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
            <td>
                <?php if ($row['activo'] == 1): ?>
                    <span style="color:green;font-weight:bold;"><i class="fa-solid fa-check-circle"></i> Activo</span>
                <?php else: ?>
                    <span style="color:red;font-weight:bold;"><i class="fa-solid fa-ban"></i> Desactivado</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($row['activo'] == 1): ?>
                <a href="beneficiarios_activos.php?edit=<?= $row['id'] ?>" class="editar" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                <a href="beneficiarios_activos.php?desactivar=<?= $row['id'] ?>" class="desactivar" title="Desactivar" onclick="return confirm('¿Seguro que deseas desactivar este beneficiario?');"><i class="fa-solid fa-user-slash"></i></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php
        $contador++;
        endwhile;
        ?>
    </table>
</div>
</body>
</html>