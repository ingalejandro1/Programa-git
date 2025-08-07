<?php
require 'vendor/autoload.php';
include './db/conexion.php';

use TCPDF;

// Crear PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema Biblioteca');
$pdf->SetTitle('Préstamos Externos');
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Título
$pdf->Cell(0, 10, 'Historial de Préstamos a Personal Externo', 0, 1, 'C');
$pdf->Ln(5);

// Tabla
$html = '
<table border="1" cellpadding="4">
    <thead>
        <tr>
            <th><b>Nombre</b></th>
            <th><b>Tipo</b></th>
            <th><b>Recurso</b></th>
            <th><b>Fecha Préstamo</b></th>
            <th><b>Fecha Devolución</b></th>
            <th><b>Estado</b></th>
        </tr>
    </thead>
    <tbody>
';

// Consulta
$sql = "
    SELECT px.nombre_completo, px.tipo_personal, pe.recurso, pe.fecha_prestamo, pe.fecha_devolucion, pe.estado
    FROM prestamos_externos pe
    JOIN personal_externo px ON pe.personal_id = px.id
    ORDER BY pe.fecha_prestamo DESC
";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $html .= '<tr>
        <td>' . $row['nombre_completo'] . '</td>
        <td>' . ucfirst($row['tipo_personal']) . '</td>
        <td>' . $row['recurso'] . '</td>
        <td>' . $row['fecha_prestamo'] . '</td>
        <td>' . ($row['fecha_devolucion'] ?: '-') . '</td>
        <td>' . strtoupper($row['estado']) . '</td>
    </tr>';
}
$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('prestamos_externos.pdf', 'I');
exit;
