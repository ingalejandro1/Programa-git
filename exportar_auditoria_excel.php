<?php
require './vendor/autoload.php';
include './db/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Recibir filtros desde GET
$filtro_libro = $_GET['libro'] ?? '';
$filtro_usuario = $_GET['usuario'] ?? '';
$filtro_fecha = $_GET['fecha'] ?? '';

// Construir SQL dinámico con filtros
$sql = "SELECT a.id, l.titulo AS libro, u.nombre AS usuario, a.nombre_usuario, a.accion, a.fecha
        FROM auditoria_prestamos a
        JOIN libros l ON a.libro_id = l.id
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE 1=1";

$params = [];
$types = '';

if (!empty($filtro_libro)) {
    $sql .= " AND l.titulo LIKE ?";
    $params[] = "%$filtro_libro%";
    $types .= 's';
}

if (!empty($filtro_usuario)) {
    $sql .= " AND u.nombre LIKE ?";
    $params[] = "%$filtro_usuario%";
    $types .= 's';
}

if (!empty($filtro_fecha)) {
    $sql .= " AND DATE(a.fecha) = ?";
    $params[] = $filtro_fecha;
    $types .= 's';
}

$sql .= " ORDER BY a.fecha DESC";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Verificar errores
if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}

// Crear archivo Excel
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator("Sistema Biblioteca")
    ->setTitle("Auditoría de Préstamos");

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Auditoría");

// Encabezados
$encabezados = ['ID', 'Libro', 'Usuario', 'Usuario Interno', 'Acción', 'Fecha'];
$sheet->fromArray($encabezados, null, 'A1');

// Agregar datos
$fila = 2;
while ($row = $resultado->fetch_assoc()) {
    $sheet->setCellValue("A{$fila}", $row['id']);
    $sheet->setCellValue("B{$fila}", $row['libro']);
    $sheet->setCellValue("C{$fila}", $row['usuario']);
    $sheet->setCellValue("D{$fila}", $row['nombre_usuario']);
    $sheet->setCellValue("E{$fila}", $row['accion'] === 'devuelto' ? '✔️ Devuelto' : '❌ No devuelto');
    $sheet->setCellValue("F{$fila}", date('d/m/Y H:i', strtotime($row['fecha'])));
    $fila++;
}

// Cabeceras para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="auditoria_prestamos.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

// Descargar
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

