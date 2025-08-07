<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include './db/conexion.php';
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $rol = 'admin';

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $password, $rol);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Error al registrar: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Administrador - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Registrar Administrador</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre completo:</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-success w-100">Registrar</button>
            </form>
            <div class="text-center mt-3">
                <a href="index.php">Volver al inicio</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>