<?php
include "./db/conexion.php";

if (isset($_GET['grafico'])) {
    $data = $conn->query("
        SELECT computador, COUNT(*) as total
        FROM consulta_especializada
        GROUP BY computador
        ORDER BY total DESC
    ");
    $labels = $valores = [];
    while ($row = $data->fetch_assoc()) {
        $labels[] = $row['computador'];
        $valores[] = $row['total'];
    }
    echo json_encode(['labels' => $labels, 'valores' => $valores]);
    exit;
}

$filtro = isset($_GET['filtro']) ? $conn->real_escape_string($_GET['filtro']) : '';
$sql = "
    SELECT ce.*, e.nombre_completo
    FROM consulta_especializada ce
    JOIN estudiantes e ON ce.estudiante_id = e.id
    WHERE e.nombre_completo LIKE '%$filtro%'
       OR computador LIKE '%$filtro%'
       OR tema_consulta LIKE '%$filtro%'
    ORDER BY fecha_consulta DESC
";
$res = $conn->query($sql);

echo '<table class="table table-striped">';
echo '<thead><tr><th>Estudiante</th><th>Computador</th><th>Tema</th><th>Fecha</th></tr></thead><tbody>';
while ($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['nombre_completo']}</td>";
    echo "<td>{$row['computador']}</td>";
    echo "<td>{$row['tema_consulta']}</td>";
    echo "<td>{$row['fecha_consulta']}</td>";
    echo "</tr>";
}
echo '</tbody></table>';
