<?php
require __DIR__ . '/../vendor/autoload.php';  // Ruta a tu autoload de Composer
include __DIR__ . '/../db/conexion.php';     // Conexión a la base de datos

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_excel'])) {
    $archivo = $_FILES['archivo_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($archivo);
        $hoja = $spreadsheet->getActiveSheet();
        $datos = $hoja->toArray();

        $insertados = 0;
        $actualizados = 0;

        // Omitimos encabezados
        for ($i = 1; $i < count($datos); $i++) {
            $fila = $datos[$i];
            $nombre = $conn->real_escape_string($fila[0]);
            $email = $conn->real_escape_string($fila[1]);
            $documento = $conn->real_escape_string($fila[2]);
            $grado = $conn->real_escape_string($fila[3]);

            // Verificamos si ya existe por documento
            $existe = $conn->query("SELECT id FROM estudiantes WHERE documento = '$documento'");
            if ($existe->num_rows > 0) {
                // Ya existe → actualizamos
                $conn->query("UPDATE estudiantes SET 
                    nombre_completo='$nombre',
                    email='$email',
                    grado='$grado'
                    WHERE documento='$documento'");
                $actualizados++;
            } else {
                // Insertamos nuevo
                $conn->query("INSERT INTO estudiantes (nombre_completo, email, documento, grado)
                              VALUES ('$nombre', '$email', '$documento', '$grado')");
                $insertados++;
            }
        }

        echo "<div style='padding: 20px; font-family: Arial;'>
                <h3>📋 Resultado de la Importación</h3>
                <p>✅ Estudiantes insertados: <b>$insertados</b></p>
                <p>✏️ Estudiantes actualizados: <b>$actualizados</b></p>
                <a href='listado_estudiantes.php'>🔙 Volver al listado</a>
              </div>";
    } catch (Exception $e) {
        echo "❌ Error al procesar el archivo: " . $e->getMessage();
    }
} else {
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Importar Estudiantes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container mt-5">
        <h3 class="mb-4">📂 Importar Estudiantes desde Excel</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="archivo_excel" class="form-label">Selecciona el archivo Excel:</label>
                <input type="file" name="archivo_excel" class="form-control" required accept=".xls,.xlsx">
            </div>
            <button type="submit" class="btn btn-primary">Importar</button>
            <a href="listado_estudiantes.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    </body>
    </html>
<?php
}
?>
