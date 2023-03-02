<?php

namespace App\Exports;

use App\Models\OD_cn\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Concerns\WithStyles;
// use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
// use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;


class StockOrderExport implements
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
        return view('exports.orders.stock-orders', [
            'orders' => Order::query()
                ->whereIn('order.order_id', preg_split("/[,]/", $this->orders['orderIds']))
                ->form()
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
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
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
