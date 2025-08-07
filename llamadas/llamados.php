<?php
include "../db/conexion.php";
include "../header.php";

// Registrar llamado de atención
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO llamados_atencion (estudiante_id, motivo) VALUES (?, ?)");
    $stmt->bind_param("is", $_POST["estudiante_id"], $_POST["motivo"]);
    $stmt->execute();
    header("Location: llamados.php");
    exit();
}

// Obtener listado de estudiantes
$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo");

// Obtener el estudiante con más llamados
$sql_top_estudiante = "
    SELECT e.id, e.nombre_completo, COUNT(la.id) AS total_llamados
    FROM llamados_atencion la
    JOIN estudiantes e ON la.estudiante_id = e.id
    GROUP BY la.estudiante_id
    ORDER BY total_llamados DESC
    LIMIT 1
";
$top_estudiante = $conn->query($sql_top_estudiante)->fetch_assoc();

// Obtener detalles de llamados del estudiante con más llamados
$detalles_llamados = [];

if ($top_estudiante) {
    $stmt_detalle = $conn->prepare("
        SELECT motivo, fecha
        FROM llamados_atencion
        WHERE estudiante_id = ?
        ORDER BY fecha DESC
    ");
    $stmt_detalle->bind_param("i", $top_estudiante['id']);
    $stmt_detalle->execute();
    $detalles_llamados = $stmt_detalle->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Llamados de Atención</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 90px;
            padding-bottom: 90px;
        }
        header, footer {
            position: fixed;
            width: 100%;
            z-index: 1040;
        }
        header { top: 0; }
        footer { bottom: 0; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4 text-center">📢 Llamados de Atención a Estudiantes</h2>

    <!-- Formulario -->
    <form method="POST" class="row g-3 mb-4 border p-3 bg-light rounded shadow-sm">
        <div class="col-md-6">
            <label class="form-label">Estudiante:</label>
            <select name="estudiante_id" id="estudiante_id" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php while($row = $estudiantes->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre_completo']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Motivo:</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="1" required></textarea>
        </div>
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">Registrar Llamado</button>
        </div>
    </form>

    <!-- Estudiante con más llamados -->
    <div class="text-center mt-5">
        <h4>👤 Estudiante con más llamados de atención</h4>
        <?php if ($top_estudiante): ?>
            <div class="d-flex justify-content-center">
                <table class="table table-bordered table-hover table-striped mt-3 w-auto">
                    <thead class="table-dark">
                        <tr>
                            <th>Estudiante</th>
                            <th>Total de llamados</th>
                            <th>Alerta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= htmlspecialchars($top_estudiante['nombre_completo']) ?></td>
                            <td><?= $top_estudiante['total_llamados'] ?></td>
                            <td>
                                <?php if ($top_estudiante['total_llamados'] >= 3): ?>
                                    <span class="badge bg-danger">¡Atención!</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No hay llamados registrados aún.</p>
        <?php endif; ?>
    </div>

    <!-- Detalles de llamados -->
    <?php if ($detalles_llamados && $detalles_llamados->num_rows > 0): ?>
        <div class="mt-5">
            <h5 class="text-center">📋 Detalles de llamados de <?= htmlspecialchars($top_estudiante['nombre_completo']) ?></h5>
            <div class="d-flex justify-content-center">
                <table class="table table-bordered table-hover mt-3 w-auto">
                    <thead class="table-secondary">
                        <tr>
                            <th>Fecha</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($detalle = $detalles_llamados->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($detalle['fecha'])) ?></td>
                                <td><?= htmlspecialchars($detalle['motivo']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include "../footer.php"; ?>
</body>
</html>
