<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include './db/conexion.php';
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password, $rol);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["rol"] = $rol;
            header("Location: dashboard_admin.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Biblioteca</title>
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
    </style>
</head>
<body class="bg-light">
    <?php include 'header.php'; ?>

    <div class="container mt-5 main-content">
        <h2 class="text-center mb-4">Inicio de Sesión</h2>
        <div class="row justify-content-center">
            <div class="col-md-5">
                <form method="POST">
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
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
                <div class="text-center mt-3">
                    <a href="registro_admin.php">Registrar nuevo administrador</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
