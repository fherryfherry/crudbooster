<?php

namespace CrudBooster\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use CrudBooster\Components\ExportImport\DataExport;
use CrudBooster\Modules\ModuleRegistrar;

class ExportCsvController extends Controller
{
    public function export(Request $request)
    {
        $payload = $request->input('key');
        $decrypted = json_decode(decrypt($payload), true);
        $module = $decrypted['module'] ?? null;
        $title = $decrypted['title'] ?? 'Exported Data';

        $moduleInfo = ModuleRegistrar::getModules($module);
        $serviceClass = null;
        $browseClass = null;
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
            \Log::error('Export CSV: Service class not found for module ['.$module.']', ['moduleInfo' => $moduleInfo ?? null]);
            abort(404, 'Service class for module ['.$module.'] not found or not registered.');
        }

        // Ambil columns dari browse component
        $browseComponent = new $browseClass();
        $browseComponent->init();
        $columns = $browseComponent->getExportableColumns();

        // Ambil filter & search dari session
        $filter = session("cb_filter_{$module}", []);
        $search = session("cb_search_{$module}", '');
        $sortBy = session("cb_sortBy_{$module}", null);
        $sortType = session("cb_sortType_{$module}", 'asc');

        // Ambil hooks dari browse component
        $hookQuery = method_exists($browseComponent, 'getExportHookQuery') ? $browseComponent->getExportHookQuery() : [];
        $hookSearch = method_exists($browseComponent, 'getExportHookSearch') ? $browseComponent->getExportHookSearch() : [];

        // Limit maksimum export
        $maxExportLimit = config('cb.max_export_limit', 100000);

        // Ambil data menggunakan service (tanpa closure di columns, namun dengan hooks)
        $data = $serviceClass::getPaginate(
            filter: $filter,
            search: $search,
            sortBy: $sortBy,
            sortType: $sortType,
            perPage: $maxExportLimit,
            columns: $columns,
            hookQuery: $hookQuery,
            hookSearch: $hookSearch
        )->getCollection();

        // Download sebagai CSV
        return Excel::download(
            new DataExport($data, $columns, $title),
            sprintf('exported-%s-%s.csv', Str::slug($title), date('Y-m-d_His')),
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}