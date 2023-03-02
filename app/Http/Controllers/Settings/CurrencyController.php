<?php

namespace App\Http\Controllers\Settings;

use Exception;
use Inertia\Inertia;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Exports\CurrencyBulkExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Settings\CurrencyResource;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Settings/IndexCurrencies', [
            'entries' => CurrencyResource::collection(
                Currency::query()
                    ->status(request('status'))
                    ->month(request('month'))
                    ->search(request('term'))
                    ->orderByDesc('id')
                    ->paginate()
            ),
            'statuses' => [
                ['value' => 'trashed', 'label' => 'Trashed']
            ],
            'months' => DB::table('currencies')
                ->selectRaw('distinct STRFTIME("01-%m-%Y", created_at) as value, STRFTIME("%m-%Y",created_at) as label')
                ->orderByDesc('value')
                ->get(),
            'queryParams' => request()->all(['status', 'month', 'term'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => [
                    'required',
                    'unique:currencies,name'
                ],
                'rates.*.amount' => ['required', 'numeric', 'digits:4'],
            ],
            [
                'rates.*.amount.required' => 'The Amount is required',
                'rates.*.amount.digits' => 'The Amount must be :digits digits',
                'rates.*.amount.numeric' => 'The Amount must be at least :numeric',
            ]
        );

        DB::transaction(function () use ($request) {
            return tap(
                Currency::create([
                    'name' => $request['name']
                ]),
                function (Currency $currency) {
                    collect(request('rates'))
                        ->map(function ($item) use ($currency) {
                            $currency->exchange_rates()->create([
                                'amount' => $item['amount']
                            ]);
                        });
                }
            );
        });
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminat
     * e\Http\Response
     */
    public function show($id)
    {
        return response()->json(
            Currency::query()
                ->with('exchange_rates', function ($query) {
                    $query->select(['currency_id', 'id', 'amount']);
                })->findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'required|unique:currencies,name,' . $id,
                'rates.*.amount' => ['required', 'numeric', 'digits:4'],
            ],
            [
                'rates.*.amount.required' => 'The Amount is required',
                'rates.*.amount.digits' => 'The Amount must be :digits digits',
                'rates.*.amount.numeric' => 'The Amount must be at least :numeric',
            ]
        );

        if ($currency = Currency::findOrFail($id)) {

            $currency->update([
                'name' => request('name')
            ]);

            collect(request('rates'))
                ->map(function ($item) use ($id) {
                if (isset($item['id']) && isset($item['deleted'])) {
                    ExchangeRate::query()
                        ->where(['id' => $item['id']])
                        ->delete();
                } else if(isset($item['id'])){
                    ExchangeRate::query()
                    ->where(['id' => $item['id']])
                    ->update(['amount' => $item['amount']]);
                } else {
                    ExchangeRate::query()
                        ->create([
                            'currency_id' => $id,
                            'amount' => $item['amount']
                    ]);
                }
            });
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy()
    {
        try {
            request()->validate([
                'entryIds' => ['required', 'array']
            ]);
            foreach (Currency::when(request('status') == 'trashed', function($query){
                $query->withTrashed();
            })->find(request('entryIds')) as $entry) {
                if(request('status') == 'trashed'){
                    $entry->forceDelete();
                }else{
                    $entry->delete();
                }
            }
            return redirect()->back();

        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }

    public function bulkAction()
    {
        try {

            request()->validate([
                'entryIds' => ['required']
            ]);

            return Excel::download(
                new CurrencyBulkExport([
                    'entryIds' => request('entryIds')
                ]),
                date('d-m-Y H:m:s', strtotime(now())) . 'currency.xlsx'
            );
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }
}
