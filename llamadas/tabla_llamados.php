<?php
include "../db/conexion.php";

$sql = "
    SELECT e.nombre_completo, COUNT(*) as total_llamados
    FROM llamados_atencion la
    JOIN estudiantes e ON la.estudiante_id = e.id
    GROUP BY la.estudiante_id
    ORDER BY total_llamados DESC
";

$resultado = $conn->query($sql);
?>

<table class="table table-bordered table-hover table-striped mt-3">
    <thead class="table-dark">
        <tr>
            <th>Estudiante</th>
            <th>Total Llamados</th>
            <th>Alerta</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $row['nombre_completo'] ?></td>
                <td><?= $row['total_llamados'] ?></td>
                <td>
                    <?= $row['total_llamados'] >= 3 ? '<span class="badge bg-danger">¡Atención!</span>' : '-' ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
