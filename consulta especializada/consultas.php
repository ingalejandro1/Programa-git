<?php
include "./db/conexion.php";
include "header.php";

// Registrar consulta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO consulta_especializada (estudiante_id, computador, tema_consulta) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_POST["estudiante_id"], $_POST["computador"], $_POST["tema_consulta"]);
    $stmt->execute();
}
$estudiantes = $conn->query("SELECT id, nombre_completo FROM estudiantes ORDER BY nombre_completo");
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">💻 Consulta Especializada - Asignación de Computador</h2>

    <!-- Formulario -->
    <form method="POST" class="row g-3 border p-3 bg-light rounded shadow-sm mb-4">
        <div class="col-md-4">
            <label>Estudiante:</label>
            <select name="estudiante_id" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php while($row = $estudiantes->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre_completo'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Computador:</label>
            <input type="text" name="computador" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Tema:</label>
            <input type="text" name="tema_consulta" class="form-control" required>
        </div>
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">Registrar Consulta</button>
        </div>
    </form>

    <!-- Búsqueda -->
    <input type="text" id="busqueda" class="form-control mb-3" placeholder="🔍 Buscar por nombre, computador o tema...">

    <!-- Botones exportar -->
    <div class="mb-3 d-flex gap-2">
        <a href="exportar_consultas_excel.php" class="btn btn-success">📥 Exportar a Excel</a>
        <a href="exportar_consultas_pdf.php" class="btn btn-danger">📄 Exportar a PDF</a>
    </div>

    <!-- Resultado AJAX -->
    <div id="resultado_consultas"></div>

    <!-- Gráfico -->
    <div class="mt-5">
        <h4 class="text-center">📊 Gráfico de Uso por Computador</h4>
        <canvas id="graficoConsulta"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Búsqueda en vivo
document.getElementById('busqueda').addEventListener('keyup', function() {
    fetch('buscar_consultas.php?filtro=' + this.value)
        .then(res => res.text())
        .then(data => document.getElementById('resultado_consultas').innerHTML = data);
});

// Gráfico
fetch('buscar_consultas.php?grafico=1')
    .then(res => res.json())
    .then(data => {
        new Chart(document.getElementById('graficoConsulta'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Consultas por computador',
                    data: data.valores,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });
    });
</script>

<?php include "footer.php"; ?>
