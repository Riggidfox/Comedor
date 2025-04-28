<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Reactivar beneficiario
if (isset($_GET['reactivar'])) {
    $id = intval($_GET['reactivar']);
    $stmt = $conn->prepare("UPDATE beneficiarios SET activo=1 WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: beneficiarios_desactivados.php");
    exit();
}

// Consulta de beneficiarios desactivados
$result = $conn->query("SELECT * FROM beneficiarios WHERE activo=0 ORDER BY fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Beneficiarios Desactivados</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="logout-bar">
        Usuario: <b><?= htmlspecialchars($_SESSION['usuario']) ?></b> |
        <a href="beneficiarios_activos.php" class="mostrar-todos"><i class="fa-solid fa-users"></i> Ver activos</a> |
        <a href="buscar_beneficiarios.php" class="mostrar-todos"><i class="fa-solid fa-magnifying-glass"></i> Buscar</a> |
        <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
    </div>
    <button class="imprimir" onclick="window.print();return false;">
      <i class="fa-solid fa-print"></i> Imprimir
    </button>
    <h2><i class="fa-solid fa-user-slash"></i> Beneficiarios Desactivados</h2>
    <table>
        <tr>
            <th>#</th>
            <th class="col-nombre">Nombre</th>
            <th class="col-apellido">Apellido</th>
            <th>Cédula</th>
            <th>Fecha de Nacimiento</th>
            <th class="col-direccion">Dirección</th>
            <th class="col-telefono">Teléfono</th>
            <th class="col-correo">Correo</th>
            <th>Sexo</th>
            <th>Fecha Registro</th>
            <th>Acción</th>
        </tr>
        <?php
        $contador = 1;
        while($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $contador ?></td>
            <td class="col-nombre"><?= htmlspecialchars($row['nombre']) ?></td>
            <td class="col-apellido"><?= htmlspecialchars($row['apellido']) ?></td>
            <td><?= htmlspecialchars($row['cedula']) ?></td>
            <td><?= htmlspecialchars($row['fecha_nacimiento']) ?></td>
            <td class="col-direccion"><?= htmlspecialchars($row['direccion']) ?></td>
            <td class="col-telefono"><?= htmlspecialchars($row['telefono']) ?></td>
            <td class="col-correo"><?= htmlspecialchars($row['correo']) ?></td>
            <td><?= htmlspecialchars($row['sexo']) ?></td>
            <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
            <td>
                <a href="beneficiarios_desactivados.php?reactivar=<?= $row['id'] ?>" class="reactivar" title="Reactivar" onclick="return confirm('¿Seguro que deseas reactivar este beneficiario?');"><i class="fa-solid fa-user-check"></i></a>
            </td>
        </tr>
        <?php
        $contador++;
        endwhile;
        ?>
    </table>
</div>
</body>
</html>