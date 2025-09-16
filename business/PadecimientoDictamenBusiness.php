<?php

include_once '../data/PadecimientoDictamenData.php';
include_once '../utility/ImageManager.php';
include_once 'clientePadecimientoBusiness.php';
include_once '../domain/PadecimientoDictamen.php';

class PadecimientoDictamenBusiness
{
    private $padecimientoDictamenData;
    private $clientePadecimientoBusiness;
    private $imageManager;

    public function __construct()
    {
        $this->padecimientoDictamenData = new PadecimientoDictamenData();
        $this->clientePadecimientoBusiness = new ClientePadecimientoBusiness();
        $this->imageManager = new ImageManager();
    }


    public function insertarTBPadecimientoDictamen($padecimientodictamen)
        {
            return $this->padecimientoDictamenData->insertarTBPadecimientoDictamen($padecimientodictamen);
        }


    public function actualizarTBPadecimientoDictamen($padecimientodictamen)
    {
        return $this->padecimientoDictamenData->actualizarTBPadecimientoDictamen($padecimientodictamen);
    }

    public function eliminarImagenDePadecimiento($padecimientoId, $imagenId)
    {
        $padecimiento = $this->padecimientoDictamenData->getPadecimientoDictamenPorId($padecimientoId);
        if (!$padecimiento) {
            return false;
        }

        $currentIds = $padecimiento->getPadecimientodictamenimagenid();
        $newIds = $this->imageManager->removeIdFromString($imagenId, $currentIds);

        $padecimiento->setPadecimientodictamenimagenid($newIds);

        $result = $this->padecimientoDictamenData->actualizarTBPadecimientoDictamen($padecimiento);

        if ($result) {
            $this->imageManager->deleteImage($imagenId);
        }
        return $result;
    }

    public function eliminarTBPadecimientoDictamen($id)
        {
            try {

                $padecimiento = $this->padecimientoDictamenData->getPadecimientoDictamenPorId($id);
                if (!$padecimiento) {
                    return false;
                }

                $imageIds = $padecimiento->getPadecimientodictamenimagenid();
                if (!empty($imageIds)) {
                    $imageIdsArray = explode('$', $imageIds);
                    foreach ($imageIdsArray as $imageId) {
                        if (!empty(trim($imageId))) {
                            $this->imageManager->deleteImage(trim($imageId));
                        }
                    }
                }

                $this->padecimientoDictamenData->eliminarRelacionPorDictamenId($id);

                return $this->padecimientoDictamenData->eliminarTBPadecimientoDictamen($id);

            } catch (Exception $e) {
                error_log("Error en eliminarTBPadecimientoDictamen: " . $e->getMessage());
                return false;
            }
        }


    public function getAllTBPadecimientoDictamen()
    {
        return $this->padecimientoDictamenData->getAllTBPadecimientoDictamen();
    }


    public function getPadecimientosDictamenPorCliente($clienteId)
    {
        return $this->padecimientoDictamenData->getPadecimientosDictamenPorCliente($clienteId);
    }

    public function getAllTBPadecimientoDictamenPorId($padecimientoLista)
    {
        return $this->padecimientoDictamenData->getAllTBPadecimientoDictamenPorId($padecimientoLista);
    }

    public function existePadecimientoDictamenEntidad($entidad)
    {
        return $this->padecimientoDictamenData->existePadecimientoDictamenEntidad($entidad);
    }

    public function getPadecimientoDictamenPorId($id)
    {
        return $this->padecimientoDictamenData->getPadecimientoDictamenPorId($id);
    }

    public function getAllClientes()
        {
            $clientesData = $this->padecimientoDictamenData->getAllClientes();

            $clientesFormateados = array();
            foreach ($clientesData as $cliente) {
                $clientesFormateados[] = [
                    'id' => $cliente['tbclienteid'],
                    'carnet' => $cliente['tbclientecarnet'],
                    'nombre' => $cliente['tbclientenombre']
                ];
            }

            return $clientesFormateados;
        }

    public function getClientePorCarnet($carnet)
    {
        return $this->padecimientoDictamenData->getClientePorCarnet($carnet);
    }

    public function obtenerTodosLosClientes()
    {
        return $this->getAllClientes();
    }

    public function validarDatosDictamen($fechaemision, $entidademision, $clienteCarnet)
    {
        $errores = array();

        if (empty($fechaemision)) {
            $errores[] = "La fecha de emisión es obligatoria";
        } elseif (strtotime($fechaemision) > time()) {
            $errores[] = "La fecha de emisión no puede ser futura";
        }

        if (empty($entidademision)) {
            $errores[] = "La entidad de emisión es obligatoria";
        }

        if (empty($clienteCarnet)) {
            $errores[] = "El carnet del cliente es obligatorio";
        } else {
            $cliente = $this->getClientePorCarnet($clienteCarnet);
            if (!$cliente) {
                $errores[] = "No se encontró un cliente con el carnet proporcionado";
            }
        }

        return $errores;
    }

    public function obtenerEstadisticasDictamenes()
    {
        $dictamenes = $this->getAllTBPadecimientoDictamen();
        $estadisticas = array(
            'total' => count($dictamenes),
            'por_entidad' => array(),
            'recientes' => 0
        );

        $fechaLimite = date('Y-m-d', strtotime('-30 days'));

        foreach ($dictamenes as $dictamen) {

            $entidad = $dictamen->getPadecimientodictamenentidademision();
            if (!isset($estadisticas['por_entidad'][$entidad])) {
                $estadisticas['por_entidad'][$entidad] = 0;
            }
            $estadisticas['por_entidad'][$entidad]++;

            if ($dictamen->getPadecimientodictamenfechaemision() >= $fechaLimite) {
                $estadisticas['recientes']++;
            }
        }

        return $estadisticas;
    }
     public function asociarDictamenACliente($clienteId, $dictamenId)
        {
            return $this->padecimientoDictamenData->asociarDictamenACliente($clienteId, $dictamenId);
        }
    public function clienteTieneDictamen($clienteId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return false;
        }
        $conn->set_charset('utf8');

        $query = "SELECT COUNT(*) as total FROM tbclientepadecimiento
                  WHERE tbclienteid = ? AND tbpadecimientodictamenid IS NOT NULL
                  AND tbpadecimientodictamenid != ''";

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $clienteId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            return $row['total'] > 0;
        }

        mysqli_close($conn);
        return false;
    }
}
?>