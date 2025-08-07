<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["prestamo_id"])) {
    $prestamo_id = $_POST["prestamo_id"];
    $sql_update = "UPDATE prestamos p
                   JOIN libros l ON p.libro_id = l.id
                   SET p.devuelto = 'si', l.estado = 'disponible'
                   WHERE p.id = ?";

    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("i", $prestamo_id);

    if ($stmt->execute()) {
        $success = "Libro devuelto correctamente.";
    } else {
        $error = "Error al devolver el libro.";
    }
}

$sql = "SELECT p.id, l.titulo, u.nombre AS estudiante, p.fecha_prestamo
        FROM prestamos p
        JOIN libros l ON p.libro_id = l.id
        JOIN estudiantes e ON p.estudiante_id = e.id
        JOIN usuarios u ON e.usuario_id = u.id
        WHERE p.devuelto = 'no'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devolver Libro - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Devolver Libro Prestado</h2>
    <?php if (isset($success)): ?>
        <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Selecciona un préstamo no devuelto:</label>
            <select name="prestamo_id" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo "{$row['titulo']} - {$row['estudiante']} ({$row['fecha_prestamo']})"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-danger w-100">Confirmar Devolución</button>
    </form>
    <div class="text-center mt-3">
        <a href="dashboard_admin.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</div>
</body>
</html>
