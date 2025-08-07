<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit();
}

include './db/conexion.php';

$filtro_libro = $_GET['libro'] ?? '';
$filtro_usuario = $_GET['usuario'] ?? '';
$filtro_fecha = $_GET['fecha'] ?? '';

// Construir consulta
$sql = "SELECT a.*, l.titulo AS libro_titulo, u.nombre AS usuario_nombre
        FROM auditoria_prestamos a
        JOIN libros l ON a.libro_id = l.id
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE 1=1";

$params = [];
$types = '';

if (!empty($filtro_libro)) {
    $sql .= " AND l.titulo LIKE ?";
    $params[] = "%$filtro_libro%";
    $types .= 's';
}

if (!empty($filtro_usuario)) {
    $sql .= " AND u.nombre LIKE ?";
    $params[] = "%$filtro_usuario%";
    $types .= 's';
}

if (!empty($filtro_fecha)) {
    $sql .= " AND DATE(a.fecha) = ?";
    $params[] = $filtro_fecha;
    $types .= 's';
}

$sql .= " ORDER BY a.fecha DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Cambios - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">🔍 Auditoría de Cambios en Préstamos</h2>
    <div class="text-center mb-3">
    <a href="exportar_auditoria_excel.php" class="btn btn-success">📥 Exportar a Excel</a>
</div>


    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="libro" value="<?= htmlspecialchars($filtro_libro) ?>" class="form-control" placeholder="Buscar por libro">
        </div>
        <div class="col-md-4">
            <input type="text" name="usuario" value="<?= htmlspecialchars($filtro_usuario) ?>" class="form-control" placeholder="Buscar por usuario">
        </div>
        <div class="col-md-3">
            <input type="date" name="fecha" value="<?= htmlspecialchars($filtro_fecha) ?>" class="form-control">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Libro</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['libro_titulo']) ?></td>
                            <td><?= htmlspecialchars($row['usuario_nombre']) ?> (<?= htmlspecialchars($row['nombre_usuario']) ?>)</td>
                            <td>
                                <?= $row['accion'] === 'devuelto'
                                    ? '<span class="text-success fw-bold">✔️ Devuelto</span>'
                                    : '<span class="text-danger fw-bold">❌ No devuelto</span>'
                                ?>
                            </td>
                            <td><?= date("d/m/Y H:i", strtotime($row['fecha'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No se encontraron registros de auditoría.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="dashboard_admin.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</div>
</body>
</html>
