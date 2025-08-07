<?php
include './db/conexion.php'; // Asegúrate de tener la conexión a la base de datos

// Obtener tipos de usuario externo
$tipos_usuario = [];
$sql = "SELECT id, tipo_usuario FROM tipo_usuario_externo ORDER BY tipo_usuario ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tipos_usuario[] = $row;
    }
}

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $nombre = $_POST['nombre_completo'];
    $area = $_POST['area_sesion'];
    $tipo_usuario_id = $_POST['tipo_usuario_id'];
    $actividad = $_POST['actividad'];
    $requerimientos = $_POST['requerimientos'];
    $observaciones = $_POST['observaciones'];

    $stmt = $conn->prepare("INSERT INTO prestamo_sala_biblioteca 
        (fecha, hora, nombre_completo, area_sesion, tipo_usuario_id, actividad, requerimientos, observaciones) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisss", $fecha, $hora, $nombre, $area, $tipo_usuario_id, $actividad, $requerimientos, $observaciones);

    if ($stmt->execute()) {
        $mensaje = "Actividad registrada con éxito.";
    } else {
        $mensaje = "Error al registrar: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividades Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
     <?php include './header.php'; ?>
<div class="container mt-4">

    <!-- Encabezado con logo y título -->
    <div class="d-flex align-items-center mb-4">
        
        <h2 class="text-primary">Registro de Actividades en la Biblioteca</h2>
    </div>

    <?php if (isset($mensaje)): ?>
        <div class="alert alert-info"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <!-- Formulario -->
    <form method="POST" class="border p-4 bg-white rounded shadow-sm">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="fecha" class="form-label">Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="hora" class="form-label">Hora:</label>
                <input type="time" name="hora" id="hora" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="nombre_completo" class="form-label">Nombre completo de quien presta:</label>
            <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="area_sesion" class="form-label">Área o Sección:</label>
            <input type="text" name="area_sesion" id="area_sesion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tipo_usuario_id" class="form-label">Tipo de Usuario:</label>
            <select name="tipo_usuario_id" id="tipo_usuario_id" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach ($tipos_usuario as $tipo): ?>
                    <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['tipo_usuario']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="actividad" class="form-label">Actividad a realizar:</label>
            <input type="text" name="actividad" id="actividad" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="requerimientos" class="form-label">Requerimientos:</label>
            <textarea name="requerimientos" id="requerimientos" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones:</label>
            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success">Registrar Actividad</button>
        </div>
    </form>
</div>
 <?php include './footer.php'; ?>
</body>
</html>
