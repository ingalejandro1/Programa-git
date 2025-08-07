<?php
include "./db/conexion.php";

// Gráfico
if (isset($_GET['grafico'])) {
    $res = $conn->query("
        SELECT px.tipo_personal, COUNT(*) as total
        FROM prestamos_externos pe
        JOIN personal_externo px ON pe.personal_id = px.id
        GROUP BY px.tipo_personal
    ");
    $labels = $valores = [];
    while ($row = $res->fetch_assoc()) {
        $labels[] = ucfirst($row['tipo_personal']);
        $valores[] = $row['total'];
    }
    echo json_encode(['labels' => $labels, 'valores' => $valores]);
    exit;
}

// Búsqueda
$filtro = $conn->real_escape_string($_GET['filtro'] ?? '');
$sql = "
    SELECT pe.id, px.nombre_completo, px.tipo_personal, pe.recurso, pe.fecha_prestamo, pe.fecha_devolucion, pe.estado
    FROM prestamos_externos pe
    JOIN personal_externo px ON pe.personal_id = px.id
    WHERE px.nombre_completo LIKE '%$filtro%' 
       OR pe.recurso LIKE '%$filtro%'
       OR px.tipo_personal LIKE '%$filtro%'
    ORDER BY pe.fecha_prestamo DESC
";
$res = $conn->query($sql);

echo '<table class="table table-striped">';
echo '<thead><tr><th>Nombre</th><th>Recurso</th><th>Tipo</th><th>Fecha préstamo</th><th>Fecha devolución</th><th>Estado</th><th>Acción</th></tr></thead><tbody>';
while ($row = $res->fetch_assoc()) {
    echo "<tr>
            <td>{$row['nombre_completo']}</td>
            <td>{$row['recurso']}</td>
            <td>{$row['tipo_personal']}</td>
            <td>{$row['fecha_prestamo']}</td>
            <td>" . ($row['fecha_devolucion'] ?: '-') . "</td>
            <td>" . strtoupper($row['estado']) . "</td>
            <td>";
    if ($row['estado'] === 'prestado') {
        echo "<a href='?devolver_id={$row['id']}' class='btn btn-sm btn-outline-danger'>Devolver</a>";
    } else {
        echo "-";
    }
    echo "</td></tr>";
}
echo '</tbody></table>';
