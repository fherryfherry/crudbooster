<?php

namespace CrudBooster\Components\ExportImport;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;

class DataTemplate implements FromCollection, WithHeadings, ShouldAutoSize, WithDefaultStyles
{
    use Exportable;

    public $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }
    #[\Override] public function collection()
    {
        return collect([]);
    }

    #[\Override] public function headings(): array
    {
        return $this->fields;
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
}