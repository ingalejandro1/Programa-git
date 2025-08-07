<?php
include "./db/conexion.php";
include "header.php";

// Registro de personal
if (isset($_POST['registrar_personal'])) {
    $stmt = $conn->prepare("INSERT INTO personal_externo (nombre_completo, documento, tipo_personal) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['nombre_completo'], $_POST['documento'], $_POST['tipo_personal']);
    $stmt->execute();
}

// Registro de préstamo
if (isset($_POST['registrar_prestamo'])) {
    $stmt = $conn->prepare("INSERT INTO prestamos_externos (personal_id, recurso) VALUES (?, ?)");
    $stmt->bind_param("is", $_POST['personal_id'], $_POST['recurso']);
    $stmt->execute();
}

// Marcar como devuelto
if (isset($_GET['devolver_id'])) {
    $id = $_GET['devolver_id'];
    $stmt = $conn->prepare("UPDATE prestamos_externos SET estado='devuelto', fecha_devolucion=CURDATE() WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$personal = $conn->query("SELECT * FROM personal_externo ORDER BY nombre_completo");
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">👨‍🏫 Gestión de Préstamos a Personal Externo</h2>

    <!-- Registro de Personal -->
    <form method="POST" class="row g-3 border p-3 bg-light mb-4 rounded shadow-sm">
        <input type="hidden" name="registrar_personal" value="1">
        <div class="col-md-4">
            <label>Nombre completo:</label>
            <input type="text" name="nombre_completo" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Documento:</label>
            <input type="text" name="documento" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Tipo de personal:</label>
            <select name="tipo_personal" class="form-select" required>
                <option value="docente">Docente</option>
                <option value="administrativo">Administrativo</option>
                <option value="otro">Otro</option>
            </select>
        </div>
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">Registrar Personal</button>
        </div>
    </form>

    <!-- Registro de préstamo -->
    <form method="POST" class="row g-3 border p-3 bg-light mb-4 rounded shadow-sm">
        <input type="hidden" name="registrar_prestamo" value="1">
        <div class="col-md-6">
            <label>Personal:</label>
            <select name="personal_id" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php while($row = $personal->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre_completo'] ?> (<?= $row['tipo_personal'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label>Recurso:</label>
            <input type="text" name="recurso" class="form-control" required>
        </div>
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-success">Registrar Préstamo</button>
        </div>
    </form>

    <!-- Herramientas -->
    <input type="text" id="busqueda" class="form-control mb-3" placeholder="🔍 Buscar por nombre, recurso o tipo...">

    <div class="mb-3 d-flex gap-2">
        <a href="exportar_prestamos_excel.php" class="btn btn-success">📥 Exportar a Excel</a>
        <a href="exportar_prestamos_pdf.php" class="btn btn-danger">📄 Exportar a PDF</a>
    </div>

    <!-- Resultados AJAX -->
    <div id="resultado_prestamos"></div>

    <!-- Gráfico -->
    <div class="mt-5">
        <h4 class="text-center">📊 Préstamos por Tipo de Personal</h4>
        <canvas id="graficoPrestamos"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('busqueda').addEventListener('keyup', function() {
    fetch('buscar_prestamos_externos.php?filtro=' + this.value)
        .then(res => res.text())
        .then(data => document.getElementById('resultado_prestamos').innerHTML = data);
});

fetch('buscar_prestamos_externos.php?grafico=1')
    .then(res => res.json())
    .then(data => {
        new Chart(document.getElementById('graficoPrestamos'), {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Tipo de Personal',
                    data: data.valores,
                    backgroundColor: ['#0d6efd', '#198754', '#dc3545']
                }]
            }
        });
    });
</script>

<?php include "footer.php"; ?>
