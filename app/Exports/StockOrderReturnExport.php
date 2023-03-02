<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use App\Models\Stocks\StockReturnTemp;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class StockOrderReturnExport implements
    FromView,
    ShouldAutoSize,
    WithStyles,
    WithDefaultStyles,
    WithEvents,
    WithColumnFormatting
{
    private $orders;

    public function __construct(
        $orders
    ) {
        $this->orders = $orders;
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function view(): View
    {
        return view('exports.orders.stock-order-return', [
            'orders' => StockReturnTemp::query()
            ->whereIn('id',  preg_split("/[,]/", $this->orders['orderIds']))
            ->get()
        ]);
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'font' => [
                //'bold' => true,
                'size' => 10,
                'name' => 'Khmer MN'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }


    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            //'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,

        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet->getPageSetup()
                    ->setPaperSize(PageSetup::PAPERSIZE_A4)
                    ->setScale(100);
                // ->setPrintArea('A1:L41');
            }
        ];
    }
}
