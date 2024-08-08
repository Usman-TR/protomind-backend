<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamController extends Controller
{
    public function stream(Request $request, $mediaId): StreamedResponse
    {
        $media = Media::findOrFail($mediaId);

        if (!$media) {
            abort(404, 'Media not found');
        }

        $path = $media->getPath();
        $size = $media->size;
        $mime = $media->mime_type;

        $start = 0;
        $length = $size;
        $status = 200;

        if ($request->server('HTTP_RANGE')) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $request->server('HTTP_RANGE'), $matches)) {
                $start = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                    $length = $end - $start + 1;
                } else {
                    $length = $size - $start;
                }
                $status = 206;
            }
        }

        return new StreamedResponse(function() use ($path, $start, $length) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);
            $buffer = 1024 * 8;
            $remaining = $length;
            while (!feof($handle) && $remaining > 0) {
                $readLength = min($buffer, $remaining);
                echo fread($handle, $readLength);
                $remaining -= $readLength;
                flush();
            }
            fclose($handle);
        }, $status, [
            'Content-Type' => $mime,
            'Content-Length' => $length,
            'Content-Range' => "bytes $start-" . ($start + $length - 1) . "/$size",
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
