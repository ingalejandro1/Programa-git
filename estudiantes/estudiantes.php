<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit();
}
include '../db/conexion.php';

$sql = "SELECT nombre_completo, email, documento, grado 
        FROM estudiantes 
        ORDER BY nombre_completo ASC";

$result = $conn->query($sql);
$estudiantes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Estudiantes - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        #buscar {
            max-width: 400px;
        }
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
<div class="container mt-5 shadow-sm bg-white p-4 rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">📚 Listado de Estudiantes Registrados</h2>
        <div>
            <a href="importar_estudiantes.php" class="btn btn-success">📥 Importar desde Excel</a>
            <a href="../dashboard_admin.php" class="btn btn-outline-secondary">🔙 Volver al Panel</a>
        </div>
    </div>

    <!-- Campo de búsqueda -->
    <div class="mb-3 text-center">
        <input type="text" id="buscar" class="form-control" placeholder="🔍 Buscar por nombre...">
    </div>

    <!-- Tabla de estudiantes -->
    <div class="table-responsive table-container">
        <table class="table table-bordered table-hover table-striped align-middle" id="tablaEstudiantes">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Documento</th>
                    <th>Grado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($estudiantes)): ?>
                    <?php foreach ($estudiantes as $est): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($est["nombre_completo"]); ?></td>
                            <td><?php echo htmlspecialchars($est["email"]); ?></td>
                            <td><?php echo htmlspecialchars($est["documento"]); ?></td>
                            <td><?php echo htmlspecialchars($est["grado"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No hay estudiantes registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Filtro por nombre
document.getElementById("buscar").addEventListener("keyup", function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("#tablaEstudiantes tbody tr");

    filas.forEach(fila => {
        let nombre = fila.children[0].textContent.toLowerCase();
        fila.style.display = nombre.includes(filtro) ? "" : "none";
    });
});
</script>
</body>
</html>

