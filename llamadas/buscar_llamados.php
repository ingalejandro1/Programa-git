<?php
include "./db/conexion.php";

if (isset($_GET['grafico'])) {
    $data = $conn->query("
        SELECT e.nombre_completo, COUNT(*) AS total
        FROM llamados_atencion la
        JOIN estudiantes e ON la.estudiante_id = e.id
        GROUP BY la.estudiante_id
        ORDER BY total DESC LIMIT 10
    ");
    $labels = $valores = [];
    while ($row = $data->fetch_assoc()) {
        $labels[] = $row['nombre_completo'];
        $valores[] = $row['total'];
    }
    echo json_encode(['labels' => $labels, 'valores' => $valores]);
    exit;
}

$filtro = isset($_GET['filtro']) ? $conn->real_escape_string($_GET['filtro']) : '';
$sql = "
    SELECT la.*, e.nombre_completo,
    (SELECT COUNT(*) FROM llamados_atencion WHERE estudiante_id = la.estudiante_id) AS total_llamados
    FROM llamados_atencion la
    JOIN estudiantes e ON la.estudiante_id = e.id
    WHERE e.nombre_completo LIKE '%$filtro%'
    ORDER BY la.fecha DESC
";
$res = $conn->query($sql);

echo '<table class="table table-striped table-bordered">';
echo '<thead><tr><th>Estudiante</th><th>Motivo</th><th>Fecha</th><th>Total llamados</th><th>Alerta</th></tr></thead><tbody>';
while ($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['nombre_completo']}</td>";
    echo "<td>{$row['motivo']}</td>";
    echo "<td>{$row['fecha']}</td>";
    echo "<td>{$row['total_llamados']}</td>";
    echo "<td>" . ($row['total_llamados'] >= 3 ? "<span class='text-danger fw-bold'>¡Exceso!</span>" : "-") . "</td>";
    echo "</tr>";
}
echo '</tbody></table>';
