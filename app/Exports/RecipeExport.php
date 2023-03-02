<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RecipeExport implements
    FromView,
    ShouldAutoSize,
    WithStyles,
    WithDefaultStyles,
    WithColumnFormatting,
    WithEvents,
    WithDrawings
{
    private $order;
    private $qrcodePath;
    protected $product_rows;

    public function __construct(
        $order,
        $product_rows,
        $qrcodePath
    ) {
        $this->order = $order;
        $this->qrcodePath = $qrcodePath;
        $this->product_rows = $product_rows;
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function view(): View
    {
        return view('exports.orders.recipe', $this->order);
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'font' => [
                //'bold' => true,
                'size' => 9,
                'name' => 'Khmer OS Battambang',
                'heigh' => 100
            ],
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_BOTTOM,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $arrgs =  [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_BOTTOM,

                ],
            ],
            'A7:J7' => [
                'font' => [
                    // 'bold' => true,
                    'color' => array('rgb' => '000000'),
                    //'underline' => true
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_TOP,

                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                    'inside' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'f1f1f1'],
                ]
            ],
        ];

        if ($row_number = $this->product_rows + 8) {

            $arrgs += [
                'A8:J' . $row_number => [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                        'inside' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ]
                    ],
                    'alignment' => [
                        // 'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_BOTTOM,

                    ],
                ],
                'I' .($row_number + 1) .':J'. ($row_number + 2) => [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                        'inside' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_TOP,

                    ],
                ],
                'A' . ($row_number + 3) => [
                    'font' => [
                        'underline' => true
                    ]
                ],
                'A' . ($row_number + 4) . ':J' . ($row_number + 4)  => [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ],
                'A' . ($row_number + 5) => [
                    'font' => [
                        'underline' => true
                    ]
                ],

                'C' . ($row_number + 6) . ':I' . ($row_number + 6)  => [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DASHED,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ],
                'C' . ($row_number + 7) . ':I' . ($row_number + 7)  => [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DASHED,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ],
                'C' . ($row_number + 8) . ':I' . ($row_number + 8)  => [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DASHED,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ],
                'A' . ($row_number + 6) . ':J' . ($row_number + 9) => [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ]
                    ]
                ],
                'A' . ($row_number + 10) => [
                    'font' => [
                        'underline' => true
                    ]
                ],
                'A' . ($row_number + 11) . ':J' . ($row_number + 12) => [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ]
                    ]
                ],
            ];
        }
        return $arrgs;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet->getPageSetup()
                    ->setPaperSize(PageSetup::PAPERSIZE_A5)
                    ->setScale(100)
                    ->setPrintArea('A1:J43');
                $event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
                $event->sheet->getPageSetup()->setHorizontalCentered(true);

                $event->sheet->getPageMargins()->setTop(0.5);
                $event->sheet->getPageMargins()->setRight(0.2);
                $event->sheet->getPageMargins()->setLeft(0.1);
                $event->sheet->getPageMargins()->setBottom(0.25);
                $event->sheet->getHeaderFooter(0.25);
               // $event->sheet->getColumnDimension('J')->setWidth(10);
            },
            AfterSheet::class => function (AfterSheet $event) {


                $event->sheet->getStyle( 'A4:F5')->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],

                ])->getAlignment()->setWrapText(true);

                $event->sheet->getDelegate()->getRowDimension(($this->product_rows + 14))->setRowHeight(14);
                $event->sheet->getDelegate()->getRowDimension(($this->product_rows + 15))->setRowHeight(14);
                $event->sheet->getDelegate()->getRowDimension(($this->product_rows + 16))->setRowHeight(14);
                $event->sheet->getDelegate()->getRowDimension(($this->product_rows + 17))->setRowHeight(14);

                $event->sheet->getStyle( 'A'.($this->product_rows + 14).':B'. ($this->product_rows + 17))->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],

                ])
                ->getAlignment()->setWrapText(true);
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            // 'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('QRCode');
        $drawing->setDescription('QRCode');
        $drawing->setPath(public_path('/QRCode/6000000013719701.png'));
        $drawing->setHeight(55);
        $drawing->setWidth(55);
        $drawing->setOffsetY(10);
        $drawing->setOffsetX(0);
        $drawing->setCoordinates('J'. ($this->product_rows + 14));

        return [$drawing];
    }
}
