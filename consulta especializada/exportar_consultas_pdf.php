<?php
require('vendor/autoload.php');
include './db/conexion.php';
use TCPDF;

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

$html = '<h3>Consultas Especializadas</h3><table border="1" cellpadding="5"><tr><th>Estudiante</th><th>Computador</th><th>Tema</th><th>Fecha</th></tr>';

$data = $conn->query("
    SELECT e.nombre_completo, ce.computador, ce.tema_consulta, ce.fecha_consulta
    FROM consulta_especializada ce
    JOIN estudiantes e ON ce.estudiante_id = e.id
    ORDER BY ce.fecha_consulta DESC
");

while ($row = $data->fetch_assoc()) {
    $html .= "<tr><td>{$row['nombre_completo']}</td><td>{$row['computador']}</td><td>{$row['tema_consulta']}</td><td>{$row['fecha_consulta']}</td></tr>";
}
$html .= '</table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('consultas.pdf', 'D');
