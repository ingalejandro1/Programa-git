<?php
require 'vendor/autoload.php';
include './db/conexion.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$datos = $conn->query("
    SELECT e.nombre_completo, ce.computador, ce.tema_consulta, ce.fecha_consulta
    FROM consulta_especializada ce
    JOIN estudiantes e ON ce.estudiante_id = e.id
");

$excel = new Spreadsheet();
$sheet = $excel->getActiveSheet();
$sheet->setTitle("Consultas");

$sheet->fromArray(["Estudiante", "Computador", "Tema", "Fecha"], NULL, 'A1');

$fila = 2;
while ($row = $datos->fetch_assoc()) {
    $sheet->setCellValue("A$fila", $row['nombre_completo']);
    $sheet->setCellValue("B$fila", $row['computador']);
    $sheet->setCellValue("C$fila", $row['tema_consulta']);
    $sheet->setCellValue("D$fila", $row['fecha_consulta']);
    $fila++;
}

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=consultas.xlsx");
$writer = new Xlsx($excel);
$writer->save("php://output");
