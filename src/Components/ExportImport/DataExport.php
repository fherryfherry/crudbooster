<?php

namespace CrudBooster\Components\ExportImport;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExport implements FromCollection, WithHeadings, ShouldAutoSize, WithDefaultStyles, WithStyles
{
    public $data;
    public $columns;
    public $title;

    public function __construct(Collection $data, array $columns, $title)
    {
        $this->data = $data;
        $this->columns = $columns;
        $this->title = $title;
    }
    private function transformValue($value)
    {
        return trim(strip_tags($value));
    }
    private function readRowValue($row, $key)
    {
        $plainKey = str_contains($key, '.') ? explode('.', $key)[1] : $key;
        $underscoreDotFirst = null;
        $underscoreDotLast = null;
        if (!str_contains($key, '.') && str_contains($key, '_')) {
            $posFirst = strpos($key, '_');
            if ($posFirst !== false) {
                $underscoreDotFirst = substr($key, 0, $posFirst) . '.' . substr($key, $posFirst + 1);
            }
            $posLast = strrpos($key, '_');
            if ($posLast !== false) {
                $underscoreDotLast = substr($key, 0, $posLast) . '.' . substr($key, $posLast + 1);
            }
        }
        // Deduplicate candidates while keeping order
        $candidates = [];
        foreach ([$key, $underscoreDotFirst, $underscoreDotLast, $plainKey] as $cand) {
            if ($cand !== null && !in_array($cand, $candidates, true)) {
                $candidates[] = $cand;
            }
        }
        foreach ($candidates as $k) {
            if (method_exists($row, 'getAttribute')) {
                $val = $row->getAttribute($k);
                if (!is_null($val)) return $val;
            }
            if (isset($row->{$k})) return $row->{$k};
        }
        return null;
    }
    #[\Override] public function collection()
    {
        return $this->data->map(function ($row) {
            return collect($this->columns)->filter(fn($f) => $f['exportable'])
                ->map(function ($col) use ($row) {
                    $key = isset($col['relation']) ? $col['relation']['key'] : $col['key'];
                    $value = $this->readRowValue($row, $key);
                    return $this->transformValue($value);
                })->toArray();
        });
    }

    #[\Override] public function headings(): array
    {
        return collect($this->columns)->filter(fn($f) => $f['exportable'])->map(fn($col) => $col['label'])->toArray();
    }

    #[\Override] public function defaultStyles(Style $defaultStyle)
    {
        // Configure the default styles
        $defaultStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $defaultStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $defaultStyle->getFont()->setSize(12);
        $defaultStyle->getAlignment()->setWrapText(true);
        $defaultStyle->getAlignment()->setIndent(1);
    }

    #[\Override] public function styles(Worksheet $sheet): void
    {
        $sheet->insertNewRowBefore(1, 1);
        $sheet->mergeCells('A1:' . $sheet->getHighestColumn() . '1');
        $sheet->setCellValue('A1', $this->title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Always set fit to page and fit to width
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        // Always set orientation to landscape for PDF export
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Auto-reduce font size if too many columns
        $colCount = count(array_filter($this->columns, fn($f) => $f['exportable']));
        $fontSize = 12;
        if ($colCount > 8 && $colCount <= 12) {
            $fontSize = 10;
        } elseif ($colCount > 12) {
            $fontSize = 8;
        }
        // Set font size for all exported cells (fix: use getStyle not getDefaultStyle)
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->getFont()->setSize($fontSize);

        $this->data->each(function ($row, $index) use ($sheet) {
            $sheet->getRowDimension($index+1)->setRowHeight(20);
        });
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cell = $sheet->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row);
                $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle('thin');
            }
        }

        // Paksa lebar kolom fixed dan wrap text jika kolom banyak
        if ($colCount > 8) {
            $width = $colCount > 12 ? 18 : 25;
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($colLetter)->setWidth($width);
                // Aktifkan wrap text di seluruh kolom
                $sheet->getStyle($colLetter . '1:' . $colLetter . $sheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);
            }
        }

        // Footer: right watermark app name, center page number
        $footer = $sheet->getHeaderFooter();
        // App name from setting module
        $appName = (function_exists('basicInfoSetting') && basicInfoSetting()->getAppName()) ? basicInfoSetting()->getAppName() : 'CRUDBooster';
        // Right: app name, Center: page number
        $footer->setOddFooter('&CPage &P of &N&R' . $appName);
        $footer->setEvenFooter('&CPage &P of &N&R' . $appName);
    }
}
