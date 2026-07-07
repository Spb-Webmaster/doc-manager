<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Storage;

trait SavesBase64Images
{
    /**
     * Декодирует data-URL изображения и сохраняет его на диск 'pdf'.
     * Возвращает относительный путь либо null, если данных нет.
     */
    private function saveBase64Image(?string $data, int $userId, string $prefix): ?string
    {
        if (!$data || !str_contains($data, ',')) return null;

        [$header, $encoded] = explode(',', $data, 2);
        $decoded = base64_decode($encoded, strict: true);
        if ($decoded === false) return null;

        $ext  = str_contains($header, 'png') ? 'png' : 'jpg';
        $path = "images/{$userId}/{$prefix}_" . uniqid() . ".{$ext}";
        Storage::disk('pdf')->put($path, $decoded);

        return $path;
    }
}
