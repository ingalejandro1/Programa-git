<?php
include '../db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $responsable = $_POST['responsable'];
    $actividad = $_POST['actividad'];

    $stmt = $conn->prepare("INSERT INTO agenda_reservas (fecha, hora_inicio, hora_fin, responsable, actividad) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fecha, $hora_inicio, $hora_fin, $responsable, $actividad);

    if ($stmt->execute()) {
        header("Location: agenda_biblioteca.php?exito=1");
    } else {
        echo "Error al guardar la reserva.";
    }
}
?>
