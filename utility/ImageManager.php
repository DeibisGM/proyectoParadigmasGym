<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../data/data.php';

class ImageManager extends Data
{

    public function __construct()
    {
        parent::__construct();
    }

    private function getNextImageId()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return 1;
        $conn->set_charset('utf8');

        $query = "SELECT MAX(tbimagenid) AS max_id FROM tbimagen";
        $result = mysqli_query($conn, $query);
        $nextId = 1;
        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['max_id'] !== null) {
                $nextId = (int)$row['max_id'] + 1;
            }
        }
        mysqli_close($conn);
        return $nextId;
    }

    public function addImages($files, $entityId, $modulePrefix)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return [];
        $conn->set_charset('utf8');

        $rutaBase = __DIR__ . '/../img/';
        if (!is_dir($rutaBase)) {
            mkdir($rutaBase, 0777, true);
        }

        $createdImageIds = [];
        $fileCount = is_array($files['name']) ? count($files['name']) : 0;

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                // 1. OBTENER EL PRÓXIMO ID ÚNICO PARA LA IMAGEN
                $nextImageId = $this->getNextImageId();

                // 2. CONSTRUIR EL NOMBRE DEL ARCHIVO BASADO EN IDS
                $entityIdPadded = str_pad($entityId, 4, '0', STR_PAD_LEFT);
                $imageIdPadded = str_pad($nextImageId, 4, '0', STR_PAD_LEFT);

                // Formato: cue0003_0005.jpg (módulo + id_entidad + id_imagen)
                $nombreArchivo = $modulePrefix . $entityIdPadded . $imageIdPadded . '.jpg';
                $rutaDestino = $rutaBase . $nombreArchivo;
                $rutaDB = '/img/' . $nombreArchivo;

                // 3. PROCESAR Y GUARDAR
                if ($this->processAndSaveImage($files['tmp_name'][$i], $rutaDestino)) {
                    $queryInsert = "INSERT INTO tbimagen (tbimagenid, tbimagenruta, tbimagenactivo) VALUES (?, ?, 1)";
                    $stmt = mysqli_prepare($conn, $queryInsert);
                    mysqli_stmt_bind_param($stmt, "is", $nextImageId, $rutaDB);
                    if (mysqli_stmt_execute($stmt)) {
                        $createdImageIds[] = $nextImageId;
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }
        mysqli_close($conn);
        return $createdImageIds;
    }

    public function deleteImage($imageId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return false;
        $conn->set_charset('utf8');
        $querySelect = "SELECT tbimagenruta FROM tbimagen WHERE tbimagenid = ?";
        $stmt = mysqli_prepare($conn, $querySelect);
        mysqli_stmt_bind_param($stmt, "i", $imageId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        if ($row) {
            $rutaArchivo = __DIR__ . '/..' . $row['tbimagenruta'];
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
        }
        $queryDelete = "DELETE FROM tbimagen WHERE tbimagenid = ?";
        $stmtDelete = mysqli_prepare($conn, $queryDelete);
        mysqli_stmt_bind_param($stmtDelete, "i", $imageId);
        $success = mysqli_stmt_execute($stmtDelete);
        mysqli_stmt_close($stmtDelete);
        mysqli_close($conn);
        return $success;
    }

    public function deleteImagesFromString($idString)
    {
        if (empty($idString)) return;
        $ids = explode('$', $idString);
        foreach ($ids as $id) {
            if (!empty(trim($id))) {
                $this->deleteImage((int)$id);
            }
        }
    }

    public function getImagesByIds($idString)
    {
        if (empty($idString)) {
            return [];
        }
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return [];
        $conn->set_charset('utf8');
        $ids = array_filter(explode('$', $idString), 'is_numeric');
        if (empty($ids)) {
            mysqli_close($conn);
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $query = "SELECT * FROM tbimagen WHERE tbimagenid IN ($placeholders) AND tbimagenactivo = 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$ids);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $images = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $images[] = $row;
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $images;
    }

    private function processAndSaveImage($tmpPath, $destinationPath)
    {
        $tipoImagen = @exif_imagetype($tmpPath);
        $imagenOriginal = null;
        switch ($tipoImagen) {
            case IMAGETYPE_JPEG:
                $imagenOriginal = @imagecreatefromjpeg($tmpPath);
                break;
            case IMAGETYPE_PNG:
                $imagenOriginal = @imagecreatefrompng($tmpPath);
                break;
            case IMAGETYPE_WEBP:
                $imagenOriginal = @imagecreatefromwebp($tmpPath);
                break;
            default:
                return false;
        }
        if ($imagenOriginal) {
            $imagenRedimensionada = imagescale($imagenOriginal, 500, 500);
            $success = imagejpeg($imagenRedimensionada, $destinationPath, 85);
            imagedestroy($imagenOriginal);
            imagedestroy($imagenRedimensionada);
            return $success;
        }
        return false;
    }

    public static function addIdsToString($newIds, $idString)
    {
        if (empty($newIds)) return $idString;
        $existingIds = empty($idString) ? [] : array_filter(explode('$', $idString));
        $mergedIds = array_unique(array_merge($existingIds, $newIds));
        return implode('$', array_filter($mergedIds));
    }

    public static function removeIdFromString($id, $idString)
    {
        if (empty($idString)) return '';
        $ids = array_filter(explode('$', $idString));
        $ids = array_filter($ids, function ($currentId) use ($id) {
            return $currentId != $id;
        });
        return implode('$', $ids);
    }
}

?>