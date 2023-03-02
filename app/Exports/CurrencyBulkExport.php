<?php

namespace App\Exports;

use App\Models\Currency;
use Maatwebsite\Excel\Concerns\FromCollection;

class CurrencyBulkExport implements FromCollection
{
    private $currecy;

    public function __construct(
        $currecy
    ) {
        $this->currecy = $currecy;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Currency::query()
            ->with('exchange_rates')
            ->whereIn(
                'id',
                preg_split("/[,]/", $this->currecy['entryIds'])
            )
            ->get()
            ->map(function($item){
                return [
                    'name' => $item['name'],
                    'rates' => $item['rates'],
                    'created_at' => $item['created_at'],
                ];
            });
    }
}
