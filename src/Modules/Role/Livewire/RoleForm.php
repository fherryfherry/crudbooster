<?php

namespace CrudBooster\Modules\Role\Livewire;

use CrudBooster\Components\Type\JsonChecklist\Function\JsonChecklist;
use CrudBooster\Components\Type\Text\Function\Text;
use CrudBooster\Livewire\BaseFormComponent;
use CrudBooster\Livewire\FormBuilder\Form;
use CrudBooster\Modules\ModuleRegistrar;
use CrudBooster\Modules\Role\Enum\RolePermission;
use CrudBooster\Modules\Role\Models\CBRole;
use CrudBooster\Modules\Role\Services\CbRoleService;
use Illuminate\Support\Str;

class RoleForm extends BaseFormComponent
{
    public $pageTitle = "Role Management";
    protected $modelService = CbRoleService::class;
    protected $modelName = CBRole::class;

    public function init(): void
    {
        $modules = $this->getModules();
        $this->makeForm([
            Form::add(label: 'Role Name', key: 'name', validation: 'required')
                ->option(Text::option()->uppercase())
                ->readonlyOn(function ($role) {
                    return isset($role['id']) && $role['id'] == 1;
                }),
            Form::add(label: 'Permissions', key: 'permissions', type: 'jsonChecklist', validation: 'required', option: JsonChecklist::option()->dataset($modules, 'Module', ['Create', 'Read', 'Update', 'Delete']))
        ]);
    }

    private function getModules()
    {
        return collect(ModuleRegistrar::getModules())->map(function ($module) {
            $checklist = null;
            if($module['additional']['permissionAvailable'] ?? false) {
                $checklist = collect($module['additional']['permissionAvailable'])->map(function ($permission) {
                    $name = $permission instanceof RolePermission ? $permission->name : $permission;
                    return ucwords(strtolower($name));
                })->toArray();
            }
            return [
                'key' => $module['key'],
                'name' => $module['name'],
                'slug'=> Str::slug(strtolower($module['name'])),
                'checklist'=> $checklist,
                'is_disabled'=> !$checklist
            ];
        })->sort(function ($a, $b) {
            return $a['name'] <=> $b['name'];
        })->values()->toArray();
    }
}
