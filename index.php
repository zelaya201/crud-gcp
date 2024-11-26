<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_escuela";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inicializar variables
$id = "";
$nombre = "";
$edad = "";
$update = false;
$error = "";
$success = "";

// Crear estudiante
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $edad = $_POST['edad'];

    if (empty($nombre) || empty($edad)) {
        $error = "Por favor, completa todos los campos";
    } else {
        $sql = "INSERT INTO estudiantes (nombre, edad) VALUES ('$nombre', '$edad')";
        if ($conn->query($sql) === TRUE) {
            $success = "Estudiante agregado correctamente";
            $nombre = "";
            $edad = "";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Eliminar estudiante
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM estudiantes WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $success = "Estudiante eliminado correctamente";
    } else {
        $error = "Error al eliminar: " . $conn->error;
    }
}

// Obtener estudiante para editar
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;
    $result = $conn->query("SELECT * FROM estudiantes WHERE id=$id");
    if ($result->num_rows == 1) {
        $row = $result->fetch_array();
        $nombre = $row['nombre'];
        $edad = $row['edad'];
    }
}

// Actualizar estudiante
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $edad = $_POST['edad'];

    if (empty($nombre) || empty($edad)) {
        $error = "Por favor, completa todos los campos";
    } else {
        $sql = "UPDATE estudiantes SET nombre='$nombre', edad='$edad' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $success = "Estudiante actualizado correctamente";
            $update = false;
            $nombre = "";
            $edad = "";
        } else {
            $error = "Error al actualizar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Estudiantes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .title-underline {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <h1 class="text-center title-underline">Gestión de estudiantes</h1>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulario -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?php echo $update ? 'Editar estudiante' : 'Agregar estudiante'; ?></h4>
                </div>
                <div class="card-body">
                    <form method="post" action="" class="needs-validation" novalidate>
                        <?php if ($update): ?>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="edad" class="form-label">Edad</label>
                            <input type="number" class="form-control" id="edad" name="edad" value="<?php echo $edad; ?>" required>
                        </div>

                        <div class="d-flex gap-2">
                            <?php if ($update): ?>
                                <button type="submit" name="actualizar" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Actualizar
                                </button>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            <?php else: ?>
                                <button type="submit" name="guardar" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Guardar
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Lista de estudiantes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Edad</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM estudiantes");
                            $contador = 1;
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $contador++; ?></td>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['edad']; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="?delete=<?php echo $row['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar este estudiante?')">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validación de formulario -->
<script>
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
</body>
</html>