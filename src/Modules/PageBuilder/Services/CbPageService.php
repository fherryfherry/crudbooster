<?php

namespace CrudBooster\Modules\PageBuilder\Services;

use CrudBooster\Domain\Services\BaseService;
use CrudBooster\Modules\PageBuilder\Models\CbPage;
use Illuminate\Support\Str;

class CbPageService extends BaseService
{
    protected static string $model = CbPage::class;

    public static function saveOrUpdate($data)
    {
        $data['path'] = Str::slug(strtolower($data['name']));
        if(isset($data[static::getPrimaryKey()])) {
            $page = CbPage::query()->find($data[static::getPrimaryKey()]);
            $page->update($data);
        } else {
            $page = CbPage::create($data);
            $data[static::getPrimaryKey()] = $page->{static::getPrimaryKey()};
        }
        return $data;
    }

    public static function getListKeyLabel()
    {
        return CbPage::query()->get()->map(function ($item) {
            return ['key' => $item[static::getPrimaryKey()], 'label' => $item['name']];
        })->toArray();
    }
}
