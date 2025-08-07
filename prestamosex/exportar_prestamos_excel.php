<?php
require 'vendor/autoload.php';
include './db/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$hoja = $spreadsheet->getActiveSheet();
$hoja->setTitle("Préstamos Externos");

// Encabezados
$hoja->fromArray(["Nombre", "Tipo", "Recurso", "Fecha Préstamo", "Fecha Devolución", "Estado"], NULL, 'A1');

// Consulta
$sql = "
    SELECT px.nombre_completo, px.tipo_personal, pe.recurso, pe.fecha_prestamo, pe.fecha_devolucion, pe.estado
    FROM prestamos_externos pe
    JOIN personal_externo px ON pe.personal_id = px.id
    ORDER BY pe.fecha_prestamo DESC
";
$res = $conn->query($sql);
$fila = 2;
while ($row = $res->fetch_assoc()) {
    $hoja->fromArray([
        $row['nombre_completo'],
        $row['tipo_personal'],
        $row['recurso'],
        $row['fecha_prestamo'],
        $row['fecha_devolucion'] ?: '-',
        strtoupper($row['estado'])
    ], NULL, 'A' . $fila);
    $fila++;
}

// Exportar
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="prestamos_externos.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
