<?php

namespace App\Exports;

use App\Models\ActivityLogger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ActivityLogExport implements
    FromCollection,
    ShouldAutoSize,
    WithColumnFormatting
{
    private $activity;

    public function __construct(
        $activity
    ) {
        $this->activity = $activity;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ActivityLogger::query()
        ->whereIn('id', preg_split("/[,]/", $this->activity['activityIds']))
        ->get()
        ->map(function($item){
            return [
                'log_name' => $item['log_name'],
                'description' => $item['description'],
                'properties' => $item['properties'],
                'event' => $item['event'],
                'buyer_name' => $item['buyer_name'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
            ];
        });
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
