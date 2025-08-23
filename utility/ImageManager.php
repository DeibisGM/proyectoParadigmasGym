<?php

function gestionarImagen($modulo, $id, $archivo, $eliminar = false) {
    $rutaBase = __DIR__ . '/../img/';
    $directorioModulo = $rutaBase . $modulo . '/';
    $nombreArchivo = $modulo . '_' . $id . '.jpg'; // Nuevo formato de nombre
    $rutaImagen = $directorioModulo . $nombreArchivo;

    // Asegurarse de que el directorio del módulo exista
    if (!is_dir($directorioModulo)) {
        mkdir($directorioModulo, 0777, true);
    }

    // Si se solicita eliminar o se está subiendo un nuevo archivo, borrar el anterior
    if (($eliminar || ($archivo && $archivo['error'] === UPLOAD_ERR_OK)) && file_exists($rutaImagen)) {
        unlink($rutaImagen);
        if ($eliminar) {
            return ['status' => 'deleted', 'message' => 'Imagen eliminada correctamente.'];
        }
    }

    // Si no hay archivo para subir, y no se pidió eliminar, no hacer nada más
    if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
        if ($eliminar) {
             return ['status' => 'deleted_not_found', 'message' => 'Se solicitó eliminar, pero no existía imagen previa.'];
        }
        return ['status' => 'no_action', 'message' => 'No se subió ninguna imagen o hubo un error en la subida.'];
    }

    // Procesar y guardar la nueva imagen
    $tipoImagen = exif_imagetype($archivo['tmp_name']);
    $imagenOriginal = null;

    switch ($tipoImagen) {
        case IMAGETYPE_JPEG:
            $imagenOriginal = imagecreatefromjpeg($archivo['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            $imagenOriginal = imagecreatefrompng($archivo['tmp_name']);
            break;
        case IMAGETYPE_WEBP:
            $imagenOriginal = imagecreatefromwebp($archivo['tmp_name']);
            break;
        default:
            return ['status' => 'error', 'message' => 'Formato de imagen no soportado.'];
    }

    if ($imagenOriginal) {
        // Redimensionar a 500x500
        $imagenRedimensionada = imagescale($imagenOriginal, 500, 500);

        // Guardar como JPG con compresión
        if (imagejpeg($imagenRedimensionada, $rutaImagen, 85)) { // 85% de calidad
            imagedestroy($imagenOriginal);
            imagedestroy($imagenRedimensionada);
            return ['status' => 'success', 'message' => 'Imagen guardada correctamente.', 'path' => $rutaImagen];
        } else {
            imagedestroy($imagenOriginal);
            imagedestroy($imagenRedimensionada);
            return ['status' => 'error', 'message' => 'No se pudo guardar la imagen JPG.'];
        }
    }

    return ['status' => 'error', 'message' => 'No se pudo procesar la imagen original.'];
}
?>