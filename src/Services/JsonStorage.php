<?php

namespace App\Services;

class JsonStorage
{
    public static function read(string $filePath): array
    {
        if (!file_exists($filePath)) {
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            file_put_contents($filePath, "[]\n", LOCK_EX);
            return [];
        }

        $content = file_get_contents($filePath);
        if ($content === false || trim($content) === '') {
            return [];
        }

        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function write(string $filePath, array $data): bool
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $json = json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return false;
        }

        return file_put_contents($filePath, $json . "\n", LOCK_EX) !== false;
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
