<?php
require '../vendor/autoload.php'; // Ajusta la ruta si tu vendor está en otra carpeta
include '../db/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$filtro = $_GET['filtro_fecha'] ?? null;

$sql = "SELECT fecha, hora_inicio, hora_fin, responsable, actividad FROM agenda_reservas";
if ($filtro) {
    $sql .= " WHERE fecha = '$filtro'";
}
$sql .= " ORDER BY fecha, hora_inicio";

$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Reservas Biblioteca');

// Encabezados
$sheet->fromArray(['Fecha', 'Hora Inicio', 'Hora Fin', 'Responsable', 'Actividad'], NULL, 'A1');

// Datos
$fila = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$fila", $row['fecha']);
    $sheet->setCellValue("B$fila", $row['hora_inicio']);
    $sheet->setCellValue("C$fila", $row['hora_fin']);
    $sheet->setCellValue("D$fila", $row['responsable']);
    $sheet->setCellValue("E$fila", $row['actividad']);
    $fila++;
}

// Descargar
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="reservas_biblioteca.xlsx"');
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
