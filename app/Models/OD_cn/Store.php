<?php

namespace App\Models\OD_cn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme;

class Store extends Model
{
    use HasFactory;
    protected $connection = 'live_mysql';
    protected $table = 'store';
    protected $primaryKey = 'store_id';

    public function member(): object
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id')
            ->select([
                'member_id',
                'member_name',
                'member_mobile',
                'member_email'
            ]);
    }
}
