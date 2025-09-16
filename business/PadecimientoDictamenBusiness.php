<?php
// VERSIÓN CORREGIDA DE PadecimientoDictamenBusiness

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

    /**
     * Inserta un padecimiento dictamen y lo asocia al cliente
     */
    public function insertarTBPadecimientoDictamen($padecimientodictamen)
        {
            return $this->padecimientoDictamenData->insertarTBPadecimientoDictamen($padecimientodictamen);
        }

    /**
     * Actualiza un padecimiento dictamen
     */
    public function actualizarTBPadecimientoDictamen($padecimientodictamen)
    {
        return $this->padecimientoDictamenData->actualizarTBPadecimientoDictamen($padecimientodictamen);
    }

    /**
     * Elimina una imagen específica de un padecimiento
     */
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

        // Si la actualización es exitosa, eliminamos el archivo físico
        if ($result) {
            $this->imageManager->deleteImage($imagenId);
        }
        return $result;
    }

    /**
     * Elimina un padecimiento dictamen y todas sus relaciones
     */
    public function eliminarTBPadecimientoDictamen($id)
        {
            try {
                // 1. Obtener el padecimiento para eliminar imágenes
                $padecimiento = $this->padecimientoDictamenData->getPadecimientoDictamenPorId($id);
                if (!$padecimiento) {
                    return false;
                }

                // 2. Eliminar las imágenes físicas asociadas
                $imageIds = $padecimiento->getPadecimientodictamenimagenid();
                if (!empty($imageIds)) {
                    $imageIdsArray = explode('$', $imageIds);
                    foreach ($imageIdsArray as $imageId) {
                        if (!empty(trim($imageId))) {
                            $this->imageManager->deleteImage(trim($imageId));
                        }
                    }
                }

                // 3. Eliminar la relación en la tabla intermedia
                $this->padecimientoDictamenData->eliminarRelacionPorDictamenId($id);

                // 4. Eliminar el dictamen de la base de datos
                return $this->padecimientoDictamenData->eliminarTBPadecimientoDictamen($id);

            } catch (Exception $e) {
                error_log("Error en eliminarTBPadecimientoDictamen: " . $e->getMessage());
                return false;
            }
        }

    /**
     * Obtiene todos los padecimientos dictamen
     */
    public function getAllTBPadecimientoDictamen()
    {
        return $this->padecimientoDictamenData->getAllTBPadecimientoDictamen();
    }

    /**
     * Obtiene padecimientos dictamen por cliente
     */
    public function getPadecimientosDictamenPorCliente($clienteId)
    {
        return $this->padecimientoDictamenData->getPadecimientosDictamenPorCliente($clienteId);
    }

    /**
     * Obtiene padecimientos dictamen por lista de IDs
     */
    public function getAllTBPadecimientoDictamenPorId($padecimientoLista)
    {
        return $this->padecimientoDictamenData->getAllTBPadecimientoDictamenPorId($padecimientoLista);
    }

    /**
     * Verifica si existe un padecimiento dictamen por entidad
     */
    public function existePadecimientoDictamenEntidad($entidad)
    {
        return $this->padecimientoDictamenData->existePadecimientoDictamenEntidad($entidad);
    }

    /**
     * Obtiene un padecimiento dictamen por ID
     */
    public function getPadecimientoDictamenPorId($id)
    {
        return $this->padecimientoDictamenData->getPadecimientoDictamenPorId($id);
    }

    /**
     * Obtiene todos los clientes
     */
    public function getAllClientes()
        {
            $clientesData = $this->padecimientoDictamenData->getAllClientes();

            // Convertir al formato esperado por el Action
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

    /**
     * Obtiene un cliente por carnet
     */
    public function getClientePorCarnet($carnet)
    {
        return $this->padecimientoDictamenData->getClientePorCarnet($carnet);
    }

    /**
     * Alias para mantener compatibilidad
     */
    public function obtenerTodosLosClientes()
    {
        return $this->getAllClientes();
    }

    /**
     * Valida los datos antes de crear un dictamen
     */
    public function validarDatosDictamen($fechaemision, $entidademision, $clienteCarnet)
    {
        $errores = array();

        // Validar fecha
        if (empty($fechaemision)) {
            $errores[] = "La fecha de emisión es obligatoria";
        } elseif (strtotime($fechaemision) > time()) {
            $errores[] = "La fecha de emisión no puede ser futura";
        }

        // Validar entidad
        if (empty($entidademision)) {
            $errores[] = "La entidad de emisión es obligatoria";
        }

        // Validar cliente
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

    /**
     * Obtiene estadísticas de dictámenes
     */
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
            // Contar por entidad
            $entidad = $dictamen->getPadecimientodictamenentidademision();
            if (!isset($estadisticas['por_entidad'][$entidad])) {
                $estadisticas['por_entidad'][$entidad] = 0;
            }
            $estadisticas['por_entidad'][$entidad]++;

            // Contar recientes
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
}
?>