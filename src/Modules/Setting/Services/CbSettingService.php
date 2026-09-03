<?php

namespace CrudBooster\Modules\Setting\Services;

use CrudBooster\Domain\Services\BaseService;
use CrudBooster\Helpers\CbUploader;
use CrudBooster\Modules\Setting\Models\CbSetting;
use Illuminate\Support\Facades\Cache;

class CbSettingService extends BaseService
{
    protected static string $model = CbSetting::class;
    protected static int $cacheTime = 60 * 24 * 365;

    public static function createOrUpdate($name, array $data)
    {
        $appEnv = config('app.env');
        self::validateAppEnv($appEnv);
        $update = CbSetting::where('name', $name)->first();
        $update = $update ?? new CbSetting();
        $update->name = $name;

        // convert file to string
        foreach ($data as $key => $value) {
            if (is_object($value) && get_class($value) == 'Livewire\Features\SupportFileUploads\TemporaryUploadedFile') {
                $data[$key] = CbUploader::uploadFromLivewire($value);
            }
        }

        // get environment
        if($appEnv=='production') {
            $update->production_setting = array_merge($update->production_setting ?? [], $data);
            $update->save();
        } elseif ($appEnv=='staging') {
            $update->staging_setting = array_merge($update->staging_setting ?? [], $data);
            $update->save();
        } elseif ($appEnv=='development' || $appEnv == 'local') {
            $update->development_setting = array_merge($update->development_setting ?? [], $data);
            $update->save();
        }
        // save to cache
        self::saveToCache($appEnv, $name, $data);
    }

    // get setting
    public static function get($name)
    {
        $appEnv = config('app.env');
        self::validateAppEnv($appEnv);
        $data = Cache::get('cb-setting-' . $appEnv . '-' . $name);
        if (!$data) {
            $data = CbSetting::where('name', $name)->first();
            if ($data) {
                if ($appEnv == 'production') {
                    $data = $data->production_setting;
                } else if ($appEnv == 'staging') {
                    $data = $data->staging_setting;
                } else if ($appEnv == 'development' || $appEnv == 'local') {
                    $data = $data->development_setting;
                }
                if($data) {
                    self::saveToCache($appEnv, $name, $data);
                }
            }
        }
        return $data;
    }

    /**
     * @param mixed $appEnv
     * @param $key
     * @param array $data
     * @return void
     */
    private static function saveToCache(mixed $appEnv, $key, ?array $data): void
    {
        $cacheKey = 'cb-setting-' . $appEnv . '-' . $key;
        Cache::forget($cacheKey);
        Cache::put($cacheKey, $data, static::$cacheTime);
    }

    /**
     * @param mixed $appEnv
     * @return void
     */
    private static function validateAppEnv(mixed $appEnv): void
    {
        if (!in_array($appEnv, ['production', 'staging', 'development', 'local'])) {
            throw new \InvalidArgumentException('Invalid environment');
        }
    }
}
