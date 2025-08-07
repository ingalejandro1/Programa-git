<?php
require 'vendor/autoload.php';
include './db/conexion.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$datos = $conn->query("
    SELECT e.nombre_completo, la.motivo, la.fecha
    FROM llamados_atencion la
    JOIN estudiantes e ON la.estudiante_id = e.id
");

$excel = new Spreadsheet();
$sheet = $excel->getActiveSheet();
$sheet->setTitle("Llamados");

$sheet->fromArray(["Estudiante", "Motivo", "Fecha"], NULL, 'A1');

$fila = 2;
while ($row = $datos->fetch_assoc()) {
    $sheet->setCellValue("A$fila", $row['nombre_completo']);
    $sheet->setCellValue("B$fila", $row['motivo']);
    $sheet->setCellValue("C$fila", $row['fecha']);
    $fila++;
}

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=llamados.xlsx");
$writer = new Xlsx($excel);
$writer->save("php://output");
