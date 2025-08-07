<?php
// Conexión a la base de datos
include '../db/conexion.php';

// Obtener eventos
$filtro = isset($_GET['filtro_fecha']) ? $_GET['filtro_fecha'] : null;

if ($filtro) {
    $sql = "SELECT id, fecha, hora_inicio, hora_fin, responsable, actividad 
            FROM agenda_reservas 
            WHERE fecha = '$filtro' 
            ORDER BY hora_inicio";
} else {
    $sql = "SELECT id, fecha, hora_inicio, hora_fin, responsable, actividad 
            FROM agenda_reservas 
            ORDER BY fecha, hora_inicio";
}

$result = $conn->query($sql);

$eventos = [];
$tabla_eventos = [];

while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        'id' => $row['id'],
        'title' => $row['actividad'] . " - " . $row['responsable'],
        'start' => $row['fecha'] . 'T' . $row['hora_inicio'],
        'end' => $row['fecha'] . 'T' . $row['hora_fin']
    ];

    $tabla_eventos[] = $row; // Para mostrar en la tabla
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/es.js'></script>
    <style>
        #calendario {
            min-height: 600px;
        }
    </style>
</head>
<body class="bg-light">
<?php include '../header.php'; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">📅 Agenda de Biblioteca</h2>

    <!-- Botón para volver -->
    <div class="mb-3 text-start">
        <a href="../dashboard_admin.php" class="btn btn-outline-primary">🏠 Volver a la página principal</a>
    </div>

    <!-- Formulario -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">Registrar nueva reserva</div>
        <div class="card-body">
            <form action="guardar_reserva.php" method="POST" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora de Inicio</label>
                    <input type="time" name="hora_inicio" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora de Fin</label>
                    <input type="time" name="hora_fin" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Responsable</label>
                    <input type="text" name="responsable" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Área o Sección:</label>
                    <input type="text" name="area_sesion" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Actividad</label>
                    <textarea name="actividad" rows="2" class="form-control" required></textarea>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success">Guardar Reserva</button>
                </div>
            </form>
        </div>
    </div>


<!-- Filtro por fecha -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">🔍 Filtrar por Fecha</div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="date" name="filtro_fecha" class="form-control" value="<?php echo isset($_GET['filtro_fecha']) ? $_GET['filtro_fecha'] : ''; ?>" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary">Aplicar Filtro</button>
                <a href="agenda_biblioteca.php" class="btn btn-outline-secondary">Limpiar</a>
            </div>
            <div class="col-md-4">
                <a href="exportar_excel.php<?php echo isset($_GET['filtro_fecha']) ? '?filtro_fecha=' . $_GET['filtro_fecha'] : ''; ?>" class="btn btn-success">📥 Exportar a Excel</a>
            </div>
        </form>
    </div>
</div>

    <!-- Tabla de eventos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">📄 Lista de Reservas</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Responsable</th>
                            <th>Actividad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tabla_eventos as $evento): ?>
                            <tr>
                                <td><?php echo $evento['id']; ?></td>
                                <td><?php echo $evento['fecha']; ?></td>
                                <td><?php echo $evento['hora_inicio']; ?></td>
                                <td><?php echo $evento['hora_fin']; ?></td>
                                <td><?php echo htmlspecialchars($evento['responsable']); ?></td>
                                <td><?php echo htmlspecialchars($evento['actividad']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tabla_eventos)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay reservas registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendario');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        height: "auto",
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?php echo json_encode($eventos); ?>,
        eventClick: function (info) {
            alert("Actividad: " + info.event.title + "\nInicio: " + info.event.start.toLocaleString());
        }
    });
    calendar.render();
});
</script>

</body>
</html>
