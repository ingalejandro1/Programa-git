<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit();
}
include './db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"];
    $autor = $_POST["autor"];
    $codigo_libro = $_POST["codigo_libro"];

    $stmt = $conn->prepare("INSERT INTO libros (titulo, autor, codigo_libro) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $titulo, $autor, $codigo_libro);

    if ($stmt->execute()) { 
        $success = "Libro registrado exitosamente.";
    } else {
        $error = "Error al registrar libro: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Libro - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Registrar Nuevo Libro</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Título del libro:</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Autor:</label>
                    <input type="text" name="autor" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Código del libro (ISBN o interno):</label>
                    <input type="text" name="codigo_libro" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrar Libro</button>
            </form>
            <div class="text-center mt-3">
                <a href="dashboard_admin.php">Volver al Panel</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
