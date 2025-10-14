<?php
// LIMPIAR TODO BUFFER
while (ob_get_level()) ob_end_clean();

session_start();
header('Content-Type: application/json');

// DEBUG INMEDIATO
error_log("=== HORARIO PERSONAL ACTION - RESERVAR ===");
error_log("SESSION DATA: " . print_r($_SESSION, true));
error_log("POST DATA: " . print_r($_POST, true));

// Respuesta de error genérica
function sendError($message) {
    error_log("ERROR: $message");
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Respuesta de éxito
function sendSuccess($message, $data = []) {
    error_log("SUCCESS: $message");
    echo json_encode(array_merge(['success' => true, 'message' => $message], $data));
    exit;
}

try {
    // Incluir archivos básicos
    include_once '../data/data.php';
    include_once '../domain/horarioPersonal.php';

    // Crear conexión directa
    $data = new Data();
    $conn = mysqli_connect($data->server, $data->user, $data->password, $data->db, $data->port);

    if (!$conn) {
        sendError('No se pudo conectar a la base de datos');
    }

    $conn->set_charset('utf8');

    $action = $_POST['action'] ?? '';
    error_log("Action recibido: $action");

    if ($action === 'reservar_personal') {
        error_log("=== PROCESANDO RESERVA ===");

        if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
            error_log("ERROR: No autorizado - Tipo usuario: " . ($_SESSION['tipo_usuario'] ?? 'NO DEFINIDO'));
            sendError('No autorizado - solo clientes pueden reservar');
        }

        $clienteId = $_SESSION['usuario_id'];
        $horarioId = $_POST['horarioId'] ?? null;

        error_log("Cliente ID: $clienteId, Horario ID: $horarioId");

        if (!$horarioId || $horarioId == '0' || $horarioId == 'null') {
            error_log("ERROR: Horario ID inválido: " . $horarioId);
            sendError('Horario no especificado o inválido');
        }

        // Verificar que el cliente existe y está activo
        $checkCliente = mysqli_query($conn, "SELECT tbclienteid FROM tbcliente WHERE tbclienteid = $clienteId AND tbclienteactivo = 1");
        if (mysqli_num_rows($checkCliente) === 0) {
            error_log("ERROR: Cliente no existe o no activo - ID: $clienteId");
            sendError('Cliente no válido o inactivo');
        }

        // VERIFICAR DETALLADAMENTE EL HORARIO
        error_log("=== VERIFICANDO HORARIO EN BASE DE DATOS ===");
        $checkHorarioQuery = "SELECT * FROM tbhorariopersonal WHERE tbhorariopersonalid = $horarioId";
        error_log("Query: $checkHorarioQuery");

        $checkHorario = mysqli_query($conn, $checkHorarioQuery);

        if (!$checkHorario) {
            error_log("ERROR en query: " . mysqli_error($conn));
            sendError('Error al verificar el horario');
        }

        if (mysqli_num_rows($checkHorario) === 0) {
            error_log("ERROR: Horario no encontrado - ID: $horarioId");
            sendError('El horario no existe');
        }

        $horarioData = mysqli_fetch_assoc($checkHorario);
        error_log("Datos del horario: " . print_r($horarioData, true));

        // Verificar que el horario está disponible
        if ($horarioData['tbhorariopersonalestado'] !== 'disponible' || $horarioData['tbclienteid'] !== NULL) {
            error_log("ERROR: Horario no disponible - Estado: " . $horarioData['tbhorariopersonalestado'] . ", ClienteID: " . $horarioData['tbclienteid']);
            sendError('El horario no está disponible o ya fue reservado');
        }

        // RESERVAR EL HORARIO
        error_log("=== RESERVANDO HORARIO ===");
        $query = "UPDATE tbhorariopersonal
                  SET tbclienteid = $clienteId, tbhorariopersonalestado = 'reservado'
                  WHERE tbhorariopersonalid = $horarioId
                  AND tbhorariopersonalestado = 'disponible'
                  AND tbclienteid IS NULL";

        error_log("Query de reserva: $query");

        $result = mysqli_query($conn, $query);

        if (!$result) {
            error_log("ERROR en update: " . mysqli_error($conn));
            sendError('Error al reservar el horario en la base de datos');
        }

        $affectedRows = mysqli_affected_rows($conn);
        error_log("Filas afectadas: $affectedRows");

        if ($affectedRows > 0) {
            sendSuccess('Reserva de instructor personal confirmada exitosamente');
        } else {
            error_log("ERROR: No se pudo actualizar el horario - posible condición de carrera");
            sendError('No se pudo completar la reserva. El horario puede haber sido reservado por otra persona.');
        }

    }
    elseif ($action === 'cancelar_personal') {
        // ... (mantener el código existente para cancelar)
        if (!isset($_SESSION['usuario_id'])) {
            sendError('No autorizado');
        }

        $usuarioId = $_SESSION['usuario_id'];
        $tipoUsuario = $_SESSION['tipo_usuario'];
        $horarioId = $_POST['horarioId'] ?? null;

        if (!$horarioId) {
            sendError('Horario no especificado');
        }

        // Construir query según el tipo de usuario
        if ($tipoUsuario === 'cliente') {
            $query = "UPDATE tbhorariopersonal
                      SET tbclienteid = NULL, tbhorariopersonalestado = 'disponible'
                      WHERE tbhorariopersonalid = $horarioId AND tbclienteid = $usuarioId";
        } elseif ($tipoUsuario === 'admin' || $tipoUsuario === 'instructor') {
            $query = "UPDATE tbhorariopersonal
                      SET tbclienteid = NULL, tbhorariopersonalestado = 'disponible'
                      WHERE tbhorariopersonalid = $horarioId";
        } else {
            sendError('No autorizado para cancelar reservas');
        }

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_affected_rows($conn) > 0) {
            sendSuccess('Reserva cancelada exitosamente');
        } else {
            sendError('Error al cancelar la reserva o reserva no encontrada');
        }
    }
    elseif ($action === 'crear_horarios') {
        // ... (mantener el código existente para crear horarios)
        if (!isset($_SESSION['tipo_usuario']) || ($_SESSION['tipo_usuario'] !== 'admin' && $_SESSION['tipo_usuario'] !== 'instructor')) {
            sendError('No autorizado');
        }

        $instructorId = $_POST['instructorId'] ?? '';
        $fecha = $_POST['fecha'] ?? '';
        $duracion = $_POST['duracion'] ?? 60;
        $horarios = $_POST['horarios'] ?? [];

        if (empty($instructorId) || empty($fecha) || empty($horarios)) {
            sendError('Datos incompletos: instructor, fecha y horarios son requeridos');
        }

        // Si es instructor, solo puede crear sus propios horarios
        if ($_SESSION['tipo_usuario'] === 'instructor' && $_SESSION['usuario_id'] != $instructorId) {
            sendError('No puedes crear horarios para otros instructores');
        }

        // Verificar que el instructor existe y está activo
        $checkInstructor = mysqli_query($conn, "SELECT tbinstructorid FROM tbinstructor WHERE tbinstructorid = '$instructorId' AND tbinstructoractivo = 1");
        if (mysqli_num_rows($checkInstructor) === 0) {
            sendError("El instructor $instructorId no existe o no está activo");
        }

        $exitos = 0;
        $total = count($horarios);
        $errores = [];

        foreach ($horarios as $hora) {
            // Verificar si ya existe el horario
            $checkHorario = mysqli_query($conn, "SELECT tbhorariopersonalid FROM tbhorariopersonal WHERE tbinstructorid = '$instructorId' AND tbhorariopersonalfecha = '$fecha' AND tbhorariopersonalhora = '$hora:00'");

            if (mysqli_num_rows($checkHorario) > 0) {
                error_log("Ya existe horario para $fecha $hora - saltando");
                $errores[] = "Horario $hora ya existe";
                continue;
            }

            // Insertar nuevo horario
            $query = "INSERT INTO tbhorariopersonal
                      (tbhorariopersonalfecha, tbhorariopersonalhora, tbinstructorid, tbclienteid, tbhorariopersonalestado, tbhorariopersonalduracion, tbhorariopersonaltipo)
                      VALUES ('$fecha', '$hora:00', '$instructorId', NULL, 'disponible', $duracion, 'personal')";

            $result = mysqli_query($conn, $query);

            if ($result) {
                $exitos++;
                $id = mysqli_insert_id($conn);
                error_log("✅ Horario creado exitosamente con ID: $id");
            } else {
                $error = mysqli_error($conn);
                error_log("❌ Error al crear horario: $error");
                $errores[] = "Error en horario $hora: $error";
            }
        }

        if ($exitos > 0) {
            $message = "Se crearon $exitos de $total horarios personales exitosamente";
            if (!empty($errores)) {
                $message .= ". Errores: " . implode(', ', array_slice($errores, 0, 3));
            }
            sendSuccess($message, [
                'created' => $exitos,
                'total' => $total,
                'errors' => $errores
            ]);
        } else {
            sendError("No se pudo crear ningún horario. Errores: " . implode(', ', $errores));
        }
    }
    else {
        sendError("Acción no reconocida: $action");
    }

    mysqli_close($conn);

} catch (Exception $e) {
    sendError("Excepción: " . $e->getMessage());
}
?>