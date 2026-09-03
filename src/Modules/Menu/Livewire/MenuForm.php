<?php

namespace CrudBooster\Modules\Menu\Livewire;

use CrudBooster\Attributes\OnFormSaved;
use CrudBooster\Components\Type\Select\Function\Select;
use CrudBooster\Livewire\BaseFormComponent;
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Modules\Menu\Models\CBMenu;
use CrudBooster\Modules\Menu\Services\CBMenuService;
use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\PageBuilder\Services\CbPageService;
use Illuminate\Support\Facades\Log;

class MenuForm extends BaseFormComponent
{
    public $pageTitle = 'Menu Management';
    protected $modelService = CBMenuService::class;
    protected $modelName = CBMenu::class;

    public function init(): void
    {
        $this->makeForm([
            Form::add(
                label: 'Parent Menu',
                key: 'parent_id',
                type: 'select',
                placeholder: '- Parent Menu -',
                helpText: 'Leave `Parent Menu` if this menu is a parent menu',
                option: Select::option()->dataset(
                    CBMenu::query()->whereNull('parent_id')->get()->map(function ($menu) {
                        return ['key' => $menu->id, 'label' => $menu->name];
                    })->toArray()
                )
            ),
            Form::add(label: 'Menu Name', key: 'name', validation: 'required'),
            Form::add(label: 'Icon (SVG)', key: 'icon', type: 'selectIcon', validation: 'required'),
            [
                Form::add(label: 'Type', key: 'menu_type', type: 'select', validation: 'required', placeholder: '** Please select a type', option: Select::option()->dataset([
                    ['key' => 'MODULE', 'label' => 'CRUD Module'],
                    ['key' => 'PAGE_BUILDER', 'label' => 'Page Builder'],
                    ['key' => 'URL', 'label' => 'Url Link'],
                ])),
                Form::add(label: 'Module', key: 'menu_value', type: 'select', validation: 'required', placeholder: '** Please select a module')
                    ->option(Select::option()->dataset(
                        collect(ModuleRegistrar::getModules())->map(function ($module) {
                            return ['key' => $module['key'], 'label' => $module['name']];
                        })->toArray()
                    ))
                    ->showOn(function ($data) {
                        return ($data['menu_type'] ?? '') == 'MODULE';
                    }),
                Form::add(label: 'Page Builder', key: 'menu_value', type: 'select', validation: 'required', placeholder: '** Please select a page')
                    ->option(Select::option()->dataset(
                        CbPageService::getListKeyLabel()
                    ))
                    ->showOn(function ($data) {
                        return ($data['menu_type'] ?? '') == 'PAGE_BUILDER';
                    }),
                Form::add(label: 'Input URL', key: 'menu_value', type: 'url', validation: 'required', placeholder: 'e.g. http...')->showOn(function ($data) {
                    return ($data['menu_type'] ?? '') == 'URL';
                }),
            ],
            Form::add(label: 'For Dashboard', key: 'is_dashboard', type: 'select', option: Select::option()->dataset([
                ['key' => 0, 'label' => 'No'],
                ['key' => 1, 'label' => 'Yes'],
            ])),
            Form::add(label: 'Tag', key: 'tag', type: 'text', placeholder: 'Optional', helpText: 'This tag is used to categorize the menu'),
        ]);
    }

    #[OnFormSaved]
    public function rebuildCacheOnSaved($model, $data, $id = null)
    {
        cache()->forget('cb_menu_all');
    }
}
