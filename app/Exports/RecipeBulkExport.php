<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Recipe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class RecipeBulkExport implements
    FromCollection,
    ShouldAutoSize,
    WithColumnFormatting
{
    private $recipe;

    public function __construct(
        $recipe
    ) {
        $this->recipe = $recipe;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Recipe::query()
            ->with('authorizedby')
            ->whereIn(
                'id',
                preg_split("/[,]/", $this->recipe['entryIds'])
            )
            ->get()
            ->map(function($item){
                return [
                    'recipe_number' => $item['recipe_number'],
                    'order_sn' => $item['order_sn'],
                    'buyer_name' => $item['buyer_name'],
                    'bayer_mobile' => $item['bayer_mobile'],
                    'exchange_rate' => $item['exchange_rate'],
                    'total_quantity' => $item['total_quantity'],
                    'grand_total_price' => $item['grand_total_price'],
                    'grand_total_price_riel' => $item['grand_total_price_riel'],
                    'grand_total_price_riel' => $item['grand_total_price_riel'],
                    'authorizedby' => $item['authorizedby']['name']
                ];
            });
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
