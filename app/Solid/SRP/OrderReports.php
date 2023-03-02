<?php

namespace App\Solid;

use Illuminate\Support\Facades\DB;

class OrderReports
{
    public function between($startDate, $endtDate)
    {
        return DB::table('users')
            ->whereBetween('created_at', [$startDate, $endtDate])
            ->latest()
            ->get();
    }

}
