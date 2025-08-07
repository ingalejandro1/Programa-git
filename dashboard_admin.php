<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
        .logout-section {
            padding: 20px 0;
        }
        .card {
    transition: transform 0.2s ease;
}
.card:hover {
    transform: scale(1.02);
}

    </style>
</head>
<body class="bg-light">
    <?php include 'header.php'; ?>

    <div class="container mt-5 main-content">
        <h2 class="mb-4 text-center">Panel de Administración</h2>
        <div class="row g-4 justify-content-center">
    <!-- FILA SUPERIOR: 3 botones -->
<div class="row g-4 justify-content-center">
    <!-- FILA 1 -->
    <div class="col-md-4">
        <div class="card border-primary h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Registrar Libro</h5>
                <a href="registrar_libro.php" class="btn btn-primary w-100">📚 Ir</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Registrar Estudiante</h5>
                <a href="registro_estudiante.php" class="btn btn-success w-100">🧑‍🎓 Ir</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Realizar Préstamo</h5>
                <a href="realizar_prestamo.php" class="btn btn-warning w-100">📖 Ir</a>
            </div>
        </div>
    </div>

    <!-- FILA 2 -->
    <div class="col-md-4">
        <div class="card border-info h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Historial de Préstamos</h5>
                <a href="historial_prestamos.php" class="btn btn-info w-100">📘 Ir</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-dark h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Lista de Estudiantes</h5>
                <a href="./estudiantes/estudiantes.php" class="btn btn-dark w-100">📋 Ir</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-primary h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Agenda de Biblioteca</h5>
                <a href="./agenda_biblioteca/agenda_biblioteca.php" class="btn btn-primary w-100">📅 Gestionar</a>
            </div>
        </div>
    </div>

    <!-- FILA 3: NUEVOS MÓDULOS -->
    <div class="col-md-4">
        <div class="card border-danger h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Llamados de Atención</h5>
                <a href="./llamadas/llamados.php" class="btn btn-danger w-100">⚠️ Gestionar</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-secondary h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Consulta Especializada</h5>
                <a href="consultas.php" class="btn btn-secondary w-100">💻 Asignar PC</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Actividades Biblioteca</h5>
                <a href="actividades_biblioteca.php" class="btn btn-success w-100">👨‍🏫 Ver</a>
            </div>
        </div>
    </div>

    <!-- AUDITORÍA -->
    <div class="col-md-12 mt-4 text-center">
        <div class="card border-secondary shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">📊 Gestión de Auditoría</h5>
                <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                    <a href="auditoria_prestamos.php" class="btn btn-outline-secondary">🔍 Ver Auditoría</a>
                    <a href="exportar_auditoria_excel.php" class="btn btn-outline-success">📥 Exportar a Excel</a>
                </div>
            </div>
        </div>
    </div>
</div>



    <!-- Botón de cerrar sesión centrado abajo -->
    <div class="container logout-section text-center">
        <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

