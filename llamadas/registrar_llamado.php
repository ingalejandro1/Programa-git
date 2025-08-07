<?php
include "../db/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $estudiante_id = $_POST["estudiante_id"];
    $motivo = $_POST["motivo"];

    $stmt = $conn->prepare("INSERT INTO llamados_atencion (estudiante_id, motivo) VALUES (?, ?)");
    $stmt->bind_param("is", $estudiante_id, $motivo);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}

