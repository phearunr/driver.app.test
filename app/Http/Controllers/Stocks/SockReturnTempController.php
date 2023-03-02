<?php

namespace App\Http\Controllers\Stocks;

use Exception;
use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Stocks\StockReturnTemp;
use App\Exports\StockOrderReturnExport;
use App\Models\Stocks\StockReturnItemsTemp;
use App\Http\Resources\Stocks\StockReturnResource;

class SockReturnTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $stores = StockReturnTemp::query()
            ->take(15)
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->store_id,
                    'label' => ucfirst($item->store_name)
                ];
            })->unique('value')->values();

        $months = DB::connection('dev_mysql')->table('stock_returns_temp')
            ->selectRaw('distinct date_format(created_at, "01-%m-%Y") as value, date_format(created_at, "%M %Y") as label')
            ->where(['store_id' => request('store', 1590)])
            ->orderByDesc('value')
            ->get();

        $entries = StockReturnResource::collection(
            StockReturnTemp::query()
                ->with('updated_by')
                ->status(request('status'))
                ->month(request('month'))
                ->search(request('term'))
                ->orderByDesc('id')
                ->paginate()
        );

        return Inertia::render('Stocks/IndexStockReturn', [
            'entries' => $entries,
            'stores' => $stores,
            'statuses' => [
                // ['value' => 10, 'label' => 'To Pay'],
                ['value' => 20, 'label' => 'To Ship'],
                ['value' => 35, 'label' => 'Delivered'],
                ['value' => 30, 'label' => 'To Receive'],
                ['value' => 40, 'label' => 'Completed']
            ],
            'months' => $months,
            'queryParams' => request()->all(['store', 'status', 'month', 'term'])
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
                'order_id' => ['required'],
                'order_sn' => [
                    'required',
                    'unique:dev_mysql.stock_returns_temp,order_sn'
                ],
                'buyer_id' => ['required'],
                'store_id' => ['required'],
                'products.*.id' => ['required'],
                'products.*.name' => ['required'],
                'products.*.unit_price' => ['required'],
                'products.*.quantity' => ['required', 'numeric', 'min:1'],
                'products.*.return_quantity' => ['numeric', 'lte:products.*.quantity'],
            ],
            [
                'order_sn.unique' => 'The order number is already return.',
                'products.*.id.required' => 'name is required',
                'products.*.quantity.min' => 'qty must be at least :min',
                'products.*.return_quantity.lte' => 'The return quantity must be smaller than quantity',
                'return_quantity.*.return_quantity.min' => 'The returm qty must be at least :min',
                'return_quantity.*.return_quantity.numeric' => 'The returm qty must be at least :numeric',
            ]
        );

        if (!empty($request['products'])) {

            $stockReturnTemp = StockReturnTemp::create([
                'order_id' => $request['order_id'],
                'order_sn' => $request['order_sn'],
                'order_state' => $request['order_state'] ?? null,
                'buyer_id' => $request['buyer_id'],
                'buyer_name' => $request['buyer_name'],
                'store_id' => $request['store_id'],
                'store_name' => $request['store_name'],
                'total_quantity' => $request['total_quantity'],
                'remarked' => $request['remarked'] ?? null,
                'updated_by' => $request->user()->id
            ]);

            foreach ($request['products'] as $prodct) {
                StockReturnItemsTemp::create([
                    'product_id' => $prodct['id'],
                    'product_name' => $prodct['name'],
                    'unit_price' => $prodct['unit_price'],
                    'quantity' => $prodct['quantity'],
                    'return_quantity' => $prodct['return_quantity'],
                    'stock_returns_temp_id' => $stockReturnTemp->id,
                    'updated_by' => $request->user()->id
                ]);
            }
        }
        return redirect()->back();
    }

    /**
     * Store a newly updated resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        $request->validate(
            [
                'order_id' => ['required'],
                'buyer_id' => ['required'],
                'store_id' => ['required'],
                'products.*.id' => ['required'],
                'products.*.name' => ['required'],
                'products.*.unit_price' => ['required'],
                'products.*.quantity' => ['required', 'numeric', 'min:1'],
                'products.*.return_quantity' => ['numeric', 'lte:products.*.quantity'],
            ],
            [
                'products.*.id.required' => 'The name is required',
                'products.*.quantity.min' => 'The quantity must be at least :min',
                'products.*.return_quantity.lte' => 'The return quantity must be smaller than quantity',
                'return_quantity.*.return_quantity.min' => 'The returm quantity must be at least :min',
                'return_quantity.*.return_quantity.numeric' => 'The returm quantity must be at least :numeric',
            ]
        );

        if (!empty($request['products'])) {
            foreach ($request['products'] as $prodct) {
                StockReturnItemsTemp::query()
                    ->findOrFail($prodct['id'])
                    ->update([
                        'return_quantity' => $prodct['return_quantity'],
                    ]);
            }
        }
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        return response()->json(
            new StockReturnResource(
                StockReturnTemp::query()
                    ->with('products')
                    ->findOrFail($id)
            )
        );
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
            foreach (StockReturnTemp::find(request('entryIds')) as $entry) {
                $entry->delete();
            }
            return redirect()->back();
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }

    public function bulkExports()
    {
        try {

            request()->validate([
                'entryIds' => ['required']
            ]);

            return Excel::download(
                new StockOrderReturnExport([
                    'orderIds' => request('entryIds')
                ]),
                date('d-M-Y H:m:s', strtotime(now())) . 'stock-order-return.xlsx'
            );
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }
}
