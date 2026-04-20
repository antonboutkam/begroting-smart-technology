<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

use RuntimeException;

final class UploadHelper
{
    public static function save(array $file, string $targetDirectory, string $publicPrefix): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload mislukt.');
        }

        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
            throw new RuntimeException('Uploadmap kon niet worden aangemaakt.');
        }

        $originalName = (string) ($file['name'] ?? 'bestand');
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = bin2hex(random_bytes(12));
        if ($extension !== '') {
            $safeName .= '.' . strtolower($extension);
        }

        $targetPath = rtrim($targetDirectory, '/') . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Bestand kon niet worden opgeslagen.');
        }

        return [
            'file_path' => rtrim($publicPrefix, '/') . '/' . $safeName,
            'original_name' => $originalName,
        ];
    }
}
