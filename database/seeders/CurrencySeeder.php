<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ExchangeRate;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('currencies')->truncate();
        Schema::enableForeignKeyConstraints();

        collect([
            [
                'name' => 'USD',
                'rates' => [4000, 4050, 4100]
            ],

        ])->map(function ($item) {
            $currency = Currency::create([
                'name' => $item['name'],
            ]);

            collect($item['rates'])->map(function($item) use ($currency){
                ExchangeRate::query()->create([
                    'currency_id' => $currency['id'],
                    'amount' => $item
                ]);
            });

        });
    }
}
