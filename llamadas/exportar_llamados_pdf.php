<?php
require('vendor/autoload.php');
include './db/conexion.php';

use TCPDF;

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

$html = '<h3>Llamados de Atención</h3><table border="1" cellpadding="5"><tr><th>Estudiante</th><th>Motivo</th><th>Fecha</th></tr>';

$data = $conn->query("
    SELECT e.nombre_completo, la.motivo, la.fecha
    FROM llamados_atencion la
    JOIN estudiantes e ON la.estudiante_id = e.id
    ORDER BY la.fecha DESC
");
while ($row = $data->fetch_assoc()) {
    $html .= "<tr><td>{$row['nombre_completo']}</td><td>{$row['motivo']}</td><td>{$row['fecha']}</td></tr>";
}
$html .= '</table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('llamados.pdf', 'D');
