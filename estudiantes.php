<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

$sql = "SELECT u.nombre, u.email, e.documento, e.grado
        FROM estudiantes e
        JOIN usuarios u ON e.usuario_id = u.id
        ORDER BY u.nombre ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Estudiantes - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Listado de Estudiantes Registrados</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Documento</th>
                    <th>Grado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["nombre"]); ?></td>
                            <td><?php echo htmlspecialchars($row["email"]); ?></td>
                            <td><?php echo htmlspecialchars($row["documento"]); ?></td>
                            <td><?php echo htmlspecialchars($row["grado"]); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No hay estudiantes registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-3">
        <a href="dashboard_admin.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</div>
</body>
</html>
