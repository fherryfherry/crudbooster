<?php

namespace CrudBooster\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use CrudBooster\Modules\ModuleRegistrar;

class ExportPdfController extends Controller
{
    public function export(Request $request)
    {
        $payload = $request->input('key');
        $decrypted = json_decode(decrypt($payload), true);
        $module = $decrypted['module'] ?? null;
        $title = $decrypted['title'] ?? 'Exported Data';
        $moduleInfo = ModuleRegistrar::getModules($module);
        $serviceClass = null;
        if ($moduleInfo && isset($moduleInfo['browseModuleClass'])) {
            $browseClass = $moduleInfo['browseModuleClass'];
            
            // Cek static method getter terlebih dahulu
            if (method_exists($browseClass, 'getModelServiceStatic')) {
                $serviceClass = $browseClass::getModelServiceStatic();
            }
            
            // Jika masih null, cek instance method getter
            if (!$serviceClass && method_exists($browseClass, 'getModelService')) {
                $serviceClass = (new $browseClass())->getModelService();
            }
            
            // Fallback: cek static property langsung
            if (!$serviceClass && property_exists($browseClass, 'modelService')) {
                $serviceClass = $browseClass::$modelService ?? null;
            }
            
            // Fallback: cek instance property langsung
            if (!$serviceClass && property_exists($browseClass, 'modelService')) {
                $serviceClass = (new $browseClass())->modelService ?? null;
            }
            
            // Fallback: cek parent static property
            if (!$serviceClass) {
                $parent = get_parent_class($browseClass);
                if ($parent && property_exists($parent, 'modelService')) {
                    $serviceClass = $parent::$modelService ?? null;
                }
            }
        }
        if (!$serviceClass || !class_exists($serviceClass)) {
            \Log::error('Export PDF: Service class not found for module ['.$module.']', ['moduleInfo' => $moduleInfo ?? null]);
            abort(404, 'Service class for module ['.$module.'] not found or not registered.');
        }

        // Get columns from browse component
        $browseComponent = new $browseClass();
        $browseComponent->init();
        $columns = $browseComponent->getExportableColumns();
        
        // Get filter and search from session or request
        $filter = session("cb_filter_{$module}", []);
        $search = session("cb_search_{$module}", '');
        $sortBy = session("cb_sortBy_{$module}", null);
        $sortType = session("cb_sortType_{$module}", 'asc');
        
        // Create a mock request with the data
        $mockRequest = new \Illuminate\Http\Request();
        $mockRequest->merge([
            'filter' => $filter,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortType' => $sortType,
            'columns' => $columns,
            // Include browse-level hooks so relations/aliases (e.g., cb_roles.name as cb_role_users_name) are selected
            'hookQuery' => method_exists($browseComponent, 'getExportHookQuery') ? $browseComponent->getExportHookQuery() : [],
            'hookSearch' => method_exists($browseComponent, 'getExportHookSearch') ? $browseComponent->getExportHookSearch() : []
        ]);

        $appName = function_exists('basicInfoSetting') && basicInfoSetting()->getAppName() ? basicInfoSetting()->getAppName() : 'CRUDBooster';

        // Ambil hooks dari browse component
        $hookQuery = method_exists($browseComponent, 'getExportHookQuery') ? $browseComponent->getExportHookQuery() : [];
        $hookSearch = method_exists($browseComponent, 'getExportHookSearch') ? $browseComponent->getExportHookSearch() : [];

        // Gunakan getPaginate agar konsisten dengan XLS/CSV (alias dari hookQuery ikut terbawa)
        $maxExportLimit = config('cb.max_export_limit', 100000);
        $result = $serviceClass::getPaginate(
            filter: $filter,
            search: $search,
            sortBy: $sortBy,
            sortType: $sortType,
            perPage: $maxExportLimit,
            columns: $columns,
            hookQuery: $hookQuery,
            hookSearch: $hookSearch
        )->getCollection();

        // Normalisasi ke array agar Blade mudah membaca key dot/underscore
        $data = collect($result)->map(function($row){
            return method_exists($row, 'toArray') ? $row->toArray() : (array)$row;
        })->toArray();

        $pdf = Pdf::loadView('cb.themes::export.pdf', ['data' => $data, 'columns' => $columns, 'title' => $title, 'appName' => $appName])
            ->setPaper('a4', 'landscape');
        return $pdf->stream(sprintf('exported-%s-%s.pdf', Str::slug($title), date('Y-m-d_His')));
    }
}