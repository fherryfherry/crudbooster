<?php

namespace CrudBooster\Components\ExportAction;

use CrudBooster\Components\ExportImport\DataExport;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade\Pdf;

trait WithExportAction
{
    protected int $__maxExport;
    public function setMaxExport(int $maxExport): void
    {
        $this->__maxExport = $maxExport;
    }
    public function exportExcel(): ?BinaryFileResponse
    {
        if(!$this->buttonExportXls) return null;
        $maxExportLimit = config('cb.max_export_limit', 100000);
        $maxExportLimit = $this->__maxExport ?? $maxExportLimit;
        return Excel::download(new DataExport($this->getPaginate($maxExportLimit, $this->__getBrowseColumn(), $this->__getHookQuery())->getCollection(), $this->__getBrowseColumn(), $this->pageTitle), sprintf('exported-%s-%s.xlsx', Str::slug($this->pageTitle), date('Y-m-d_His')), \Maatwebsite\Excel\Excel::XLSX);
    }
    public function exportCsv(): ?BinaryFileResponse
    {
        if(!$this->buttonExportCsv) return null;
        $maxExportLimit = config('cb.max_export_limit', 100000);
        $maxExportLimit = $this->__maxExport ?? $maxExportLimit;
        return Excel::download(new DataExport($this->getPaginate($maxExportLimit, $this->__getBrowseColumn(), $this->__getHookQuery())->getCollection(), $this->__getBrowseColumn(), $this->pageTitle), sprintf('exported-%s-%s.csv', Str::slug($this->pageTitle), date('Y-m-d_His')), \Maatwebsite\Excel\Excel::CSV);
    }
    private function sanitizeExportData($data) {
        return collect($data)->map(function($row) {
            foreach ($row as $k => $v) {
                if (is_string($v)) {
                    $v = preg_replace('/[[:^print:]]/', '', $v);
                    $v = @iconv('UTF-8', 'UTF-8//IGNORE', $v);
                    $row[$k] = is_string($v) ? $v : '';
                }
            }
            return $row;
        });
    }
    public function exportPdf()
    {
        if(!$this->buttonExportPdf) return null;
        $maxExportLimit = config('cb.max_export_limit', 100000);
        $maxExportLimit = $this->__maxExport ?? $maxExportLimit;
        $data = $this->getPaginate($maxExportLimit, $this->__getBrowseColumn(), $this->__getHookQuery())->getCollection();
        // Sanitize and normalize to array so Blade can read dot/plain keys
        $data = $this->sanitizeExportData($data)->map(function($row){
            return method_exists($row, 'toArray') ? $row->toArray() : (array)$row;
        });
        $columns = $this->__getBrowseColumn();
        $title = $this->pageTitle;
        $appName = function_exists('basicInfoSetting') && basicInfoSetting()->getAppName() ? basicInfoSetting()->getAppName() : 'CRUDBooster';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cb.themes::export.pdf', compact('data', 'columns', 'title', 'appName'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream(sprintf('exported-%s-%s.pdf', \Illuminate\Support\Str::slug($title), date('Y-m-d_His')));
    }

    /**
     * Public getter for hookQuery used during export (for controllers)
     */
    public function getExportHookQuery(): array
    {
        return $this->__getHookQuery();
    }

    /**
     * Public getter for hookSearch used during export (for controllers)
     */
    public function getExportHookSearch(): array
    {
        return $this->__getHookSearch();
    }
}
