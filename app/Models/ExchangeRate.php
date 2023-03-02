<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeRate extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'exchange_rates';
    protected $primaryKey = 'id';

    protected $fillable = [
        'currency_id',
        'amount'
    ];

}
