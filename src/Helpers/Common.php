<?php

use CrudBooster\Helpers\CBPathUtil;
use CrudBooster\Modules\User\Models\User;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;

// function to sanitize url only current domain allowed
if(!function_exists('sanitizeUrl')) {
    function sanitizeUrl($url, $default = null): ?string
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = filter_var($url, FILTER_VALIDATE_URL);
        $current_domain = $_SERVER['HTTP_HOST'];
        if (!str_contains($url, $current_domain)) {
            return $default;
        }
        $parsed_url = parse_url($url);
        $path = $parsed_url['path'] ?? '';
        $query = $parsed_url['query'] ?? '';
        $fragment = $parsed_url['fragment'] ?? '';
        $url = $path . ($query ? '?' . $query : '') . ($fragment ? '#' . $fragment : '');
        $prefixToRemove = getCmsPath('/');
        $url = substr($url, strlen($prefixToRemove));
        return $url;
    }
}

if(!function_exists('cbIcon')) {
    /**
     * Get icon
     * @param $icon
     * @return string
     */
    function cbIcon($icon): string
    {
        return \CrudBooster\Components\Icon\Icon::valueOf($icon);
    }
}

if(!function_exists('min_var_export')) {
    function min_var_export($input, $noQuoteValueForKey = []): ?string
    {
        if (is_array($input)) {
            $buffer = [];
            foreach ($input as $key => $value) {
                $exportedValue = in_array($key, $noQuoteValueForKey) ? $value : var_export($value, true);
                $buffer[] = var_export($key, true) . "=>" . $exportedValue;
            }
            return "[" . implode(",", $buffer) . "]";
        } else {
            return var_export($input, true);
        }
    }
}

if(!function_exists('getLaravelSchemaTypeList')) {
    /**
     * Get Laravel Schema Type List
     */
    function getLaravelSchemaTypeList(): array
    {
        return [
            'string',
            'text',
            'integer',
            'date',
            'dateTime',
            'double',
            'bigInteger',
            'timestamp',
            'float',
            'json',
            'longText',
            'mediumInteger',
            'mediumText',
            'smallInteger',
            'time',
            'tinyInteger',
            'uuid',
            'boolean'
        ];
    }
}

if(!function_exists('getModelList')) {
    /**
     * Get model list
     */
    function getModelList(): array
    {
        $fileSystem = new Filesystem();
        $files = $fileSystem->allFiles(app_path());
        $models = [];
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            if (preg_match('/namespace\s+([a-zA-Z0-9_\\\\]+);/', $content, $namespaceMatches) &&
                preg_match('/class\s+([a-zA-Z0-9_]+)\s+/', $content, $classMatches)) {
                $namespace = $namespaceMatches[1];
                $class = $classMatches[1];
                $fullClassName = $namespace . '\\' . $class;
                if (class_exists($fullClassName) &&
                    (is_subclass_of($fullClassName, Model::class) || is_subclass_of($fullClassName, User::class))) {
                    $models[] = $fullClassName;
                }
            }
        }
        // sorting models by a-z
        sort($models);
        return $models;
    }
}

if(!function_exists('getPasswordResetUrl')) {
    /**
     * Get password reset url
     */
    function getPasswordResetUrl($token): string
    {
        return url(CBPathUtil::getCmsPath('auth/password-reset'), $token);
    }
}

if(!function_exists('getStorageUrl')) {
    /**
     * Get storage url safely
     * @param string $path
     * @param string|null $default Default image URL if file not found
     * @param string|null $disk Override storage disk per-call
     * @return string
     */
    function getStorageUrl($path, $default = null, ?string $disk = null): string
    {
        try {
            $diskUsed = $disk ?? (config('cb.storage_disk') ?? config('filesystems.default'));
            if (!$path || !Storage::disk($diskUsed)->exists($path)) {
                return $default ?? config('cb.no_image_browse', 'https://placehold.co/48');
            }

            switch ($diskUsed) {
                case 'public':
                    return Storage::disk($diskUsed)->url($path);
                default:
                    return Storage::disk($diskUsed)->temporaryUrl($path, now()->addMinutes(config('cb.storage_temporary_minutes', 5)));
            }
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::warning("Failed to get storage URL for path: {$path}", [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            return $default ?? config('cb.no_image_browse', 'https://placehold.co/48');
        }
    }
}

if(!function_exists('getStorageFileSize')) {
    /**
     * Get storage file size safely
     * @param string $path
     * @param string|null $disk Override storage disk per-call
     * @return string|int
     */
    function getStorageFileSize($path, ?string $disk = null): string|int
    {
        try {
            $diskUsed = $disk ?? (config('cb.storage_disk') ?? config('filesystems.default'));
            if (!$path || !Storage::disk($diskUsed)->exists($path)) {
                return 0;
            }
            return Storage::disk($diskUsed)->fileSize($path);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::warning("Failed to get file size for path: {$path}", [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            return 0;
        }
    }
}

if(!function_exists('getStorageFileExists')) {
    /**
     * Check if storage file exists safely
     * @param string $path
     * @param string|null $disk Override storage disk per-call
     * @return bool
     */
    function getStorageFileExists($path, ?string $disk = null): bool
    {
        try {
            $diskUsed = $disk ?? (config('cb.storage_disk') ?? config('filesystems.default'));
            return $path && Storage::disk($diskUsed)->exists($path);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::warning("Failed to check file existence for path: {$path}", [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            return false;
        }
    }
}

if(!function_exists('getCmsPath')) {
    /**
     * Get CMS Path
     * @param $path
     * @return string
     */
    function getCmsPath($path): string
    {
        return CBPathUtil::getCmsPath($path);
    }
}

if(!function_exists('getCmsUrl')) {
    /**
     * Get CMS URL
     * @param null|string $path
     * @return UrlGenerator|Application|string
     */
    function getCmsUrl($path = null): UrlGenerator|Application|string
    {
        return url(CBPathUtil::getCmsPath($path));
    }
}

if(!function_exists('getEditPath')) {
    /**
     * Get CMS Edit Path
     * @param $mainPath
     * @param $id
     * @return string
     */
    function getEditPath($mainPath, $id) {
        return CBPathUtil::getCmsPath($mainPath . '/' . $id . '/edit');
    }
}

if(!function_exists('getDetailPath')) {
    /**
     * Get CMS Detail Path
     * @param $mainPath
     * @param $id
     * @return string
     */
    function getDetailPath($mainPath, $id): string
    {
        return CBPathUtil::getCmsPath($mainPath . '/' . $id);
    }
}

if(!function_exists('getCreatePath')) {
    /**
     * Get CMS Create Path
     * @param $mainPath
     * @return string
     */
    function getCreatePath($mainPath): string
    {
        return CBPathUtil::getCmsPath($mainPath . '/create');
    }
}


