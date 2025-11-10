<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'], $_SESSION['tipo_usuario'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once __DIR__ . '/../business/progresoBusiness.php';

$tipoUsuario = $_SESSION['tipo_usuario'];
$usuarioId = (int) $_SESSION['usuario_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

$progresoBusiness = new ProgresoBusiness();

function respond($payload, int $status = 200)
{
    http_response_code($status);
    echo json_encode($payload);
    exit();
}

function parseDateParam(string $value)
{
    try {
        return new DateTimeImmutable($value);
    } catch (Throwable $exception) {
        return null;
    }
}

switch ($action) {
    case 'get_periodos':
    case 'get_periodos_cliente': {
        $granularidad = $_GET['granularidad'] ?? 'week';
        $limite = isset($_GET['limite']) ? (int) $_GET['limite'] : 16;

        $clienteId = $usuarioId;
        if ($tipoUsuario !== 'cliente' && isset($_GET['clienteId'])) {
            $clienteId = (int) $_GET['clienteId'];
        }

        if ($tipoUsuario === 'cliente' && isset($_GET['clienteId']) && (int) $_GET['clienteId'] !== $usuarioId) {
            respond(['error' => 'No autorizado para consultar otros clientes'], 403);
        }

        $periodos = $progresoBusiness->getPeriodosDisponibles($clienteId, $granularidad, $limite);
        respond(['periodos' => $periodos, 'granularidad' => $granularidad]);
    }

    case 'get_progreso_rango': {
        $clienteId = $usuarioId;
        if (isset($_GET['clienteId'])) {
            $clienteSolicitado = (int) $_GET['clienteId'];
            if ($tipoUsuario === 'cliente' && $clienteSolicitado !== $usuarioId) {
                respond(['error' => 'No autorizado para consultar otros clientes'], 403);
            }
            if ($tipoUsuario !== 'cliente') {
                $clienteId = $clienteSolicitado;
            }
        }

        $inicio = $_GET['inicio'] ?? $_POST['inicio'] ?? null;
        $fin = $_GET['fin'] ?? $_POST['fin'] ?? null;

        if (!$inicio || !$fin) {
            respond(['error' => 'Se requieren las fechas de inicio y fin'], 400);
        }

        try {
            $dataset = $progresoBusiness->getProgresoPorRango($clienteId, $inicio, $fin);
        } catch (Throwable $exception) {
            respond(['error' => 'No fue posible calcular el progreso para el rango solicitado'], 400);
        }

        respond(['dataset' => $dataset]);
    }

    case 'get_cobertura_cliente': {
        if (!in_array($tipoUsuario, ['instructor', 'admin'], true)) {
            respond(['error' => 'No autorizado'], 403);
        }

        $clienteId = isset($_GET['clienteId']) ? (int) $_GET['clienteId'] : 0;
        if ($clienteId <= 0) {
            respond(['error' => 'Se requiere el identificador del cliente'], 400);
        }

        $inicio = $_GET['inicio'] ?? date('Y-m-d', strtotime('-30 days'));
        $fin = $_GET['fin'] ?? date('Y-m-d');

        $inicioDate = parseDateParam($inicio);
        $finDate = parseDateParam($fin);

        if (!$inicioDate || !$finDate) {
            respond(['error' => 'Las fechas proporcionadas no son válidas'], 400);
        }

        $resultado = $progresoBusiness->getCoberturaCliente($clienteId, $inicioDate, $finDate);
        respond($resultado);
    }

    default:
        respond(['error' => 'Acción no soportada'], 400);
}
