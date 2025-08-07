<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $actividad = $_POST['actividad'];
    $responsable = $_POST['responsable'];
    $inicio = $_POST['inicio'];
    $fin = $_POST['fin'];

    $fecha_inicio = explode("T", $inicio)[0];
    $hora_inicio = explode("T", $inicio)[1];
    $hora_fin = explode("T", $fin)[1];

    $stmt = $conn->prepare("UPDATE agenda_reservas SET actividad=?, responsable=?, fecha=?, hora_inicio=?, hora_fin=? WHERE id=?");
    $stmt->bind_param("sssssi", $actividad, $responsable, $fecha_inicio, $hora_inicio, $hora_fin, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "No se pudo editar."]);
    }
}
?>
