<?php

namespace App\Services;

class JsonStorage
{
    public static function read(string $filePath): array
    {
        if (!file_exists($filePath)) {
            $dir = dirname($filePath);
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException("Tidak dapat membuat direktori penyimpanan: {$dir}");
            }
            if (file_put_contents($filePath, "[]\n", LOCK_EX) === false) {
                throw new \RuntimeException("Tidak dapat membuat file JSON: {$filePath}");
            }
            return [];
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("Tidak dapat membaca file JSON: {$filePath}");
        }
        if (trim($content) === '') {
            return [];
        }

        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException("Format JSON tidak valid: {$filePath}", 0, $exception);
        }

        if (!is_array($decoded)) {
            throw new \RuntimeException("Struktur JSON harus berupa array: {$filePath}");
        }

        return $decoded;
    }

    public static function write(string $filePath, array $data): bool
    {
        $dir = dirname($filePath);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException("Tidak dapat membuat direktori penyimpanan: {$dir}");
        }

        $json = json_encode(
            array_values($data),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
        if (file_put_contents($filePath, $json . "\n", LOCK_EX) === false) {
            throw new \RuntimeException("Tidak dapat menulis file JSON: {$filePath}");
        }

        return true;
    }

    public static function generateId(string $prefix, array $items): string
    {
        $max = 0;
        foreach ($items as $item) {
            if (isset($item['id']) && preg_match('/^' . preg_quote($prefix, '/') . '-(\d+)$/', $item['id'], $matches)) {
                $num = (int) $matches[1];
                if ($num > $max) {
                    $max = $num;
                }
            }
        }
        return sprintf('%s-%03d', $prefix, $max + 1);
    }
}
