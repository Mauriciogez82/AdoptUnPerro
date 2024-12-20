<?php
include("../vista/conectar.php");

session_start();

// Verifica el usuario
if (!isset($_SESSION['usuario'])) {
    // Redirige al inicio de sesión si no está autenticado
    header("Location: index.php");
    exit();
}

// Procesar acciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];

    switch($accion) {
        case 'btnAgregar':
            agregarAdopcion($conexion);
            header('location: ../vista/registros.php');
            break;
        case 'btnModificar':
            modificarAdopcion($conexion);
            break;
        case 'btnEliminar':
            eliminarAdopcion($conexion);
            break;
        case 'btnCancelar':
            cancelarAccion();
            break;
        default:
            header('location: index.php?error=Acción no reconocida');
            exit;
    }
}

function agregarAdopcion($conexion) {
    try {
        // Datos de adopción
        $id_adopta = $_POST['txtID_ADOPTA'];
        $dni = $_POST['txtDNI'];
        $apellido = $_POST['txtAPELLIDO'];
        $nombre = $_POST['txtNOMBRE'];
        $fecha_adopcion = $_POST['txtFECHA_ADOPCION'];
        $email = $_POST['email'];

        // Datos de perro
        $id_perro = $_POST['txtID_PERRO'];
        $fecha_ingreso = $_POST['txtFECHA_INGRESO'];
        $color = $_POST['txtCOLOR'];
        $estado = $_POST['txtESTADO'];

        // Validaciones básicas
        if (empty($id_perro) || empty($fecha_ingreso) || empty($color) || empty($estado)) {
            throw new Exception("Todos los campos son requeridos.");
        }
        

        

        // Consultas preparadas para prevenir inyección SQL
        $sql_adopta = "INSERT INTO dueño (ID_ADOPTA, DNI, APELLIDO, NOMBRE, FECHA_ADOPCION, EMAIL) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_adopta = $conexion->prepare($sql_adopta);
        $stmt_adopta->bind_param("isssss", $id_adopta, $dni, $apellido, $nombre, $fecha_adopcion, $email);
        $stmt_adopta->execute();

        $sql_perro = "INSERT INTO perro (ID_PERRO, FECHA_INGRESO, COLOR, ESTADO) 
                      VALUES (?, ?, ?, ?)";
        $stmt_perro = $conexion->prepare($sql_perro);
        $stmt_perro->bind_param("ssss", $id_perro, $fecha_ingreso, $color, $estado);
        $stmt_perro->execute();
        header('location: index.php?mensaje=Adopción agregada exitosamente');
    } catch (Exception $e) {
        header('location: index.php?error=' . urlencode($e->getMessage()));
    }
}

function modificarAdopcion($conexion) {
    // Lógica similar a agregar, pero con UPDATE
    try {
        $id_adopcion = $_POST['txtID_ADOPCION'];
        $id_usuario = $_POST['txtID_USUARIO'];
        $id_perro = $_POST['txtID_PERRO'];
        $fecha_adopcion = $_POST['txtFECHA_ADOPCION'];

        $sql = "UPDATE adopciones SET ID_USUARIO = ?, ID_PERRO = ?, FECHA_ADOPCION = ? WHERE ID_ADOPCION = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iisi", $id_usuario, $id_perro, $fecha_adopcion, $id_adopcion);
        $stmt->execute();

        header('location: index.php?mensaje=Adopción modificada exitosamente');
    } catch (Exception $e) {
        header('location: index.php?error=' . urlencode($e->getMessage()));
    }
}

function eliminarAdopcion($conexion) {
    try {
        $id_adopcion = $_POST['txtID_ADOPCION'];

        $sql = "DELETE FROM adopciones WHERE ID_ADOPCION = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_adopcion);
        $stmt->execute();

        header('location: index.php?mensaje=Adopción eliminada exitosamente');
    } catch (Exception $e) {
        header('location: index.php?error=' . urlencode($e->getMessage()));
    }
}

function cancelarAccion() {
    header('location: index.php');
    exit;
}
?>