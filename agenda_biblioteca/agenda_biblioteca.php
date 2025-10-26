<?php
// Conexión a la base de datos
require_once '../db/conexion.php';

// Filtro por fecha (opcional)
$filtro = isset($_GET['filtro_fecha']) ? trim($_GET['filtro_fecha']) : null;

// Query base
$sqlBase = "SELECT id, fecha, hora_inicio, hora_fin, responsable, area_sesion, actividad, creado_en 
            FROM agenda_reservas";

// Ejecutar consulta con o sin filtro (prepared)
if ($filtro) {
    $sql = $sqlBase . " WHERE fecha = ? ORDER BY fecha, hora_inicio";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filtro);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $sql = $sqlBase . " ORDER BY fecha, hora_inicio";
    $result = $conn->query($sql);
}

// Mapa simple de colores por área/sección
function colorPorArea($area) {
    $key = mb_strtolower(trim($area));
    $map = [
        'biblioteca'      => '#0d6efd',
        'preescolar'      => '#20c997',
        'primaria'        => '#6f42c1',
        'bachillerato'    => '#fd7e14',
        'docentes'        => '#198754',
        'reunión'         => '#dc3545',
        'taller'          => '#0dcaf0',
        'sala de sistemas'=> '#6c757d',
    ];
    foreach ($map as $k => $c) {
        if (mb_strpos($key, $k) !== false) return $c;
    }
    // color por defecto
    return '#495057';
}

// Construir eventos para FullCalendar
$eventos = [];
while ($row = $result->fetch_assoc()) {
    // Asegurar formato HH:MM:SS
    $inicio = preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $row['hora_inicio']) ? $row['hora_inicio'] : ($row['hora_inicio'] . ':00');
    $fin    = preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $row['hora_fin'])    ? $row['hora_fin']    : ($row['hora_fin'] . ':00');

    $color = colorPorArea($row['area_sesion']);

    $eventos[] = [
        'id'    => (int)$row['id'],
        'title' => $row['actividad'] . " — " . $row['responsable'],
        'start' => $row['fecha'] . 'T' . $inicio,
        'end'   => $row['fecha'] . 'T' . $fin,
        'allDay' => false,
        'backgroundColor' => $color,
        'borderColor' => $color,
        'textColor' => '#fff',
        'extendedProps' => [
            'responsable' => $row['responsable'],
            'area'        => $row['area_sesion'],
            'actividad'   => $row['actividad'],
            'creado_en'   => $row['creado_en'],
            'fecha'       => $row['fecha'],
            'hora_inicio' => $row['hora_inicio'],
            'hora_fin'    => $row['hora_fin']
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda Biblioteca</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FullCalendar (bundle global) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

    <style>
        body { background-color: #f8fafc; }
        #calendario { min-height: 720px; background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: .75rem; }
        .fc .fc-toolbar-title { font-size: 1.15rem; }
        .legend-dot { display:inline-block; width:12px; height:12px; border-radius: 50%; margin-right:6px; vertical-align: middle; }
        .card { border-radius: .75rem; }
    </style>
</head>
<body>
<?php include '../header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h2 class="mb-0">📅 Agenda de Biblioteca</h2>
        <a href="../dashboard_admin.php" class="btn btn-outline-primary">🏠 Volver al panel</a>
    </div>

    <!-- Leyenda por área -->
    <div class="alert alert-light border d-flex align-items-center flex-wrap gap-3">
        <span><span class="legend-dot" style="background:#0d6efd"></span>Biblioteca</span>
        <span><span class="legend-dot" style="background:#20c997"></span>Preescolar</span>
        <span><span class="legend-dot" style="background:#6f42c1"></span>Primaria</span>
        <span><span class="legend-dot" style="background:#fd7e14"></span>Bachillerato</span>
        <span><span class="legend-dot" style="background:#198754"></span>Docentes</span>
        <span><span class="legend-dot" style="background:#0dcaf0"></span>Taller</span>
        <span><span class="legend-dot" style="background:#6c757d"></span>Sala de Sistemas</span>
    </div>

    <!-- Formulario de creación -->
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
                    <label class="form-label">Área o Sección</label>
                    <input type="text" name="area_sesion" class="form-control" placeholder="Biblioteca, Primaria, Bachillerato, etc." required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Actividad</label>
                    <textarea name="actividad" rows="2" class="form-control" required></textarea>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success">Guardar Reserva</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filtro por fecha + export -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">🔍 Filtrar por Fecha</div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="date" name="filtro_fecha" class="form-control" value="<?php echo $filtro ? htmlspecialchars($filtro) : ''; ?>">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Aplicar</button>
                    <a href="agenda_biblioteca.php" class="btn btn-outline-secondary">Limpiar</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="exportar_excel.php<?php echo $filtro ? '?filtro_fecha=' . urlencode($filtro) : ''; ?>" class="btn btn-success">📥 Exportar a Excel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Calendario -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">🗓️ Calendario de Reservas</div>
        <div class="card-body">
            <div id="calendario"></div>
            <?php if (empty($eventos)): ?>
                <p class="text-center text-muted mt-3 mb-0">No hay reservas para mostrar.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<!-- Modal Detalle de Evento -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEventoLabel">Detalle de reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-4">Actividad</dt><dd class="col-8" id="m-actividad"></dd>
          <dt class="col-4">Responsable</dt><dd class="col-8" id="m-responsable"></dd>
          <dt class="col-4">Área/Sesión</dt><dd class="col-8" id="m-area"></dd>
          <dt class="col-4">Inicio</dt><dd class="col-8" id="m-inicio"></dd>
          <dt class="col-4">Fin</dt><dd class="col-8" id="m-fin"></dd>
          <dt class="col-4">Registrado</dt><dd class="col-8" id="m-creado"></dd>
        </dl>
      </div>
      <div class="modal-footer">
        <a href="#" id="m-editar" class="btn btn-outline-primary">Editar</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendario');
    const eventos = <?php echo json_encode($eventos, JSON_UNESCAPED_UNICODE); ?>;
    const filtro = <?php echo $filtro ? ("'".htmlspecialchars($filtro, ENT_QUOTES)."'") : "null"; ?>;

    // Configurar calendario
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        timeZone: 'local',
        initialView: 'dayGridMonth',
        initialDate: filtro || undefined, // si hay filtro, abre ese día/mes
        height: 'auto',
        nowIndicator: true,
        firstDay: 1, // Lunes
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5], // L-V
            startTime: '06:00',
            endTime: '19:00'
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today:    'Hoy',
            month:    'Mes',
            week:     'Semana',
            day:      'Día',
            list:     'Agenda'
        },
        expandRows: true,
        navLinks: true,
        weekNumbers: true,
        selectable: false,
        editable: false,
        displayEventEnd: true,
        events: eventos,
        eventDidMount: function(info) {
            const p = info.event.extendedProps || {};
            info.el.title =
                "Actividad: " + (p.actividad || info.event.title) + "\n" +
                "Responsable: " + (p.responsable || '') + "\n" +
                "Área/Sesión: " + (p.area || '') + "\n" +
                "Inicio: " + (info.event.start ? info.event.start.toLocaleString() : '') + "\n" +
                (info.event.end ? ("Fin: " + info.event.end.toLocaleString() + "\n") : "") +
                "Registrado: " + (p.creado_en || '');
        },
        eventClick: function(info) {
            const p = info.event.extendedProps || {};
            const inicio = info.event.start ? info.event.start.toLocaleString() : '';
            const fin    = info.event.end ? info.event.end.toLocaleString() : '';

            // Llenar modal
            document.getElementById('m-actividad').textContent   = p.actividad || info.event.title || '';
            document.getElementById('m-responsable').textContent = p.responsable || '';
            document.getElementById('m-area').textContent        = p.area || '';
            document.getElementById('m-inicio').textContent      = inicio;
            document.getElementById('m-fin').textContent         = fin || '—';
            document.getElementById('m-creado').textContent      = p.creado_en || '—';

            // Link de edición (ajusta a tu ruta real, por ejemplo editar_reserva.php?id=)
            document.getElementById('m-editar').href = 'editar_reserva.php?id=' + encodeURIComponent(info.event.id);

            const modal = new bootstrap.Modal(document.getElementById('modalEvento'));
            modal.show();
        }
    });

    calendar.render();
});
</script>
</body>
</html>
