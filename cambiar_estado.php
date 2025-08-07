<?php
session_start();
include './db/conexion.php'; // Asegúrate que la ruta sea correcta

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prestamo_id'], $_POST['estado_actual'])) {
    $prestamo_id = intval($_POST['prestamo_id']);
    $estado_actual = $_POST['estado_actual'];
    $nuevo_estado = ($estado_actual === 'si') ? 'no' : 'si';

    // Verifica si la conexión existe
    if (!$conn) {
        die("❌ Error de conexión a la base de datos.");
    }

    // Actualizar el estado del préstamo
    $stmt = $conn->prepare("UPDATE prestamos SET devuelto = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_estado, $prestamo_id);

    if ($stmt->execute()) {
        // Obtener libro_id del préstamo
        $query_libro = $conn->prepare("SELECT libro_id FROM prestamos WHERE id = ?");
        $query_libro->bind_param("i", $prestamo_id);
        $query_libro->execute();
        $res_libro = $query_libro->get_result();
        $libro = $res_libro->fetch_assoc();

        if ($libro) {
            $libro_id = $libro['libro_id'];
            $nuevo_estado_libro = ($nuevo_estado === 'si') ? 'disponible' : 'prestado';

            // Actualizar estado del libro
            $update_libro = $conn->prepare("UPDATE libros SET estado = ? WHERE id = ?");
            $update_libro->bind_param("si", $nuevo_estado_libro, $libro_id);
            $update_libro->execute();

            // Insertar auditoría
            $usuario_id = $_SESSION['user_id'] ?? 0;
            $nombre_usuario = $_SESSION['nombre'] ?? 'admin';
            $accion = ($nuevo_estado === 'si') ? 'devuelto' : 'no_devuelto';

            $audit = $conn->prepare("
                INSERT INTO auditoria_prestamos (prestamo_id, libro_id, usuario_id, nombre_usuario, accion)
                VALUES (?, ?, ?, ?, ?)
            ");
            $audit->bind_param("iiiss", $prestamo_id, $libro_id, $usuario_id, $nombre_usuario, $accion);
            $audit->execute();
        }

        header("Location: historial_prestamos.php?msg=estado_actualizado");
        exit();
    } else {
        echo "❌ Error al actualizar el préstamo: " . $stmt->error;
    }
} else {
    echo "⚠️ Solicitud inválida.";
}
