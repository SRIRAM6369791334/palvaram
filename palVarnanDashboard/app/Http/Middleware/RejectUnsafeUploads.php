<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Safety net for every dashboard upload. Controllers store images with
 * ->store('...', 'public'), which names the file from its detected MIME type,
 * and several of them had no file-type rule. This rejects any upload whose
 * real content type (finfo, not the browser's claim) is not an allowed image,
 * spreadsheet or zip, and any file with a server-executable extension.
 */
class RejectUnsafeUploads
{
    private const ALLOWED_MIME = [
        // images
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        // bulk product upload: Excel / CSV + zip of images
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel', 'application/octet-stream', 'text/csv', 'text/plain',
        'application/zip', 'application/x-zip-compressed',
    ];

    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps',
        'htaccess', 'htm', 'html', 'js', 'svg', 'svgz', 'shtml', 'cgi', 'pl', 'py', 'sh', 'exe', 'bat',
    ];

    public function handle(Request $request, Closure $next)
    {
        foreach ($this->flatten($request->allFiles()) as $field => $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }
            $ext = strtolower($file->getClientOriginalExtension());
            $mime = $file->isValid() ? (string) $file->getMimeType() : '';
            $guessed = strtolower((string) $file->guessExtension());

            $bad = in_array($ext, self::BLOCKED_EXTENSIONS, true)
                || in_array($guessed, self::BLOCKED_EXTENSIONS, true)
                || !in_array($mime, self::ALLOWED_MIME, true)
                // text/plain and octet-stream are only acceptable for spreadsheet uploads
                || (in_array($mime, ['text/plain', 'application/octet-stream'], true)
                    && !in_array($ext, ['csv', 'txt', 'xls', 'xlsx'], true));

            if ($bad) {
                $message = 'Unsupported file type. Please upload a JPG, PNG, WEBP or GIF image.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => $message, 'errors' => [$field => [$message]]], 422);
                }
                return back()->withErrors([$field => $message])->withInput();
            }
        }

        return $next($request);
    }

    private function flatten(array $files, string $prefix = ''): array
    {
        $out = [];
        foreach ($files as $key => $value) {
            $name = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value)) {
                $out += $this->flatten($value, $name);
            } else {
                $out[$name] = $value;
            }
        }
        return $out;
    }
}
