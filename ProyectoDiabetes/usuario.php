<?php
require 'config.php';
// Manejo de operaciones: agregar, actualizar y eliminar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'];
    $idUsuario = $_POST['idUsuario'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    if ($accion == 'agregar') {
        $sql = "INSERT INTO usuarios (nombre, apellido, fecha_nacimiento, usuario, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$nombre, $apellido, $fecha_nacimiento, $usuario, $password])) {
            echo "Usuario agregado con éxito.";
        } else {
            echo "Error al agregar usuario.";
        }
    } elseif ($accion == 'editar' && $id) {
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, fecha_nacimiento = ?, usuario = ?";
        $params = [$nombre, $apellido, $fecha_nacimiento, $usuario];
        if ($password) {
            $sql .= ", password = ?";
            $params[] = $password;
        }
        $sql .= " WHERE idUsuario = ?";
        $params[] = $id;
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            echo "Usuario actualizado con éxito.";
        } else {
            echo "Error al actualizar usuario.";
        }
    } elseif ($accion == 'eliminar' && $id) {
        $sql = "DELETE FROM usuarios WHERE idUsuario = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$id])) {
            echo "Usuario eliminado con éxito.";
        } else {
            echo "Error al eliminar usuario.";
        }
    }
}
// Obtener usuarios para mostrar
$usuarios = $pdo->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Agregar Usuario</h2>
<form method="post">
    <input type="hidden" name="accion" value="agregar">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="apellido" placeholder="Apellidos" required>
    <input type="date" name="fecha_nacimiento" required>
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Agregar</button>
</form>
<h2>Usuarios Registrados</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Fecha Nacimiento</th>
        <th>Login</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($usuarios as $usuario): ?>
    <tr>
        <td><?php echo htmlspecialchars($usuario['idUsuario']); ?></td>
        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
        <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
        <td><?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?></td>
        <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
        <td>
            <form method="post" style="display:inline;">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?php echo $usuario['idUsuario']; ?>">
                <button type="submit" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</button>
            </form>
            <form method="post" style="display:inline;">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="idUsuario" value="<?php echo $usuario['idUsuario']; ?>">
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                <input type="text" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                <input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?>" required>
                <input type="text" name="usuario" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
                <input type="password" name="password" placeholder="Nueva contraseña (opcional)">
                <button type="submit">Editar</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?> 
</table>
