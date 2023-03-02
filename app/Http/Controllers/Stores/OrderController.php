<?php

namespace App\Http\Controllers\Stores;

use Exception;
use Carbon\Carbon;

use Inertia\Inertia;
use App\Models\Recipe;
use App\Models\Currency;
use App\Models\RecipeItem;
use App\Models\OD_cn\Order;
use Illuminate\Support\Str;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use App\Exports\RecipeExport;
use App\Exports\OrderBulkExport;
use App\Models\RecipeFreezeNumber;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Resources\Stores\OrderResource;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): object
    {
        try {
            return Inertia::render('Stores/IndexOrders', [
                'entries' => OrderResource::collection(
                    Order::query()
                        ->form()
                        ->where(['order.store_id' => request('store', 1590)])
                        ->status(request('status'))
                        ->month(request('month'))
                        ->lastRate(request('rate', 4000))
                        ->search(request('term'))
                        ->orderBy('order.order_id', 'DESC')
                        ->paginate(request('pager', 15))
                ),
                'stores' => [],

                // 'stores' => Order::query()
                //     ->whereIn('order_state', [10, 20, 30, 35, 40])
                //     ->select(DB::raw('count(*) as store_count,store_id, store_name'))
                //     ->groupBy(['store_id', 'store_name'])
                //     ->orderByDesc('store_count')
                //     ->limit(25)
                //     ->get()
                //     ->map(function ($item) {
                //         return [
                //             'value' => $item->store_id,
                //             'label' => ucfirst($item->store_name)
                //         ];
                //     })->unique('value')->values(),

                'statuses' => [
                    // ['value' => 10, 'label' => 'In-reviews'],
                    ['value' => 20, 'label' => 'To Ship'],
                    ['value' => 35, 'label' => 'Delivered'],
                    ['value' => 30, 'label' => 'To Receive'],
                    ['value' => 40, 'label' => 'Completed']
                ],
                'months' => DB::connection('live_mysql')->table('order')
                    ->selectRaw('distinct from_unixtime(add_time, "01-%m-%Y") as value, from_unixtime(add_time, "%M %Y") as label, from_unixtime(add_time, "%Y %m") as date')
                    ->where(['store_id' => request('store', 1590)])
                    ->whereIn('order_state', [20, 30, 35, 40])
                    ->orderByDesc('date')
                    ->get(),
                'rates' => ExchangeRate::query()
                    ->where(['currency_id' => 1])
                    ->select([
                        'amount As value',
                        'amount As label'
                    ])
                    ->get(),
                'pagers' => [
                    ['value' => 15, 'label' => 15],
                    ['value' => 30, 'label' => 30],
                    ['value' => 45, 'label' => 45],
                    ['value' => 65, 'label' => 60],
                    ['value' => 100, 'label' => 100]
                ],
                'queryParams' => request()->all(['store', 'status', 'month', 'rate', 'term', 'pager'])
            ]);
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @param order id
     */

    public function show($id)
    {
        try {

            return Order::query()
                ->with('products')
                ->form()
                ->where(['order.order_id' => $id])
                ->get()
                ->map(function ($item) {
                    return [
                        'order_id' => $item['order_id'],
                        'order_sn' => $item['order_sn'],
                        'order_state' => $item['order_state'],
                        'buyer_id' => $item['buyer_id'],
                        'buyer_name' => $item['buyer_name'],
                        'store_id' => $item['store_id'],
                        'store_name' => $item['store_name'],
                        'delivery_fee' => $item['delivery_fee'],
                        'total_quantity' => $item['products_sum_goods_num'],
                        'products' => collect($item->products)->map(function ($product) {
                            return [
                                'id' => $product['goods_id'],
                                'name' => $product['goods_name'],
                                'unit_price' => $product['goods_price'],
                                'quantity' => $product['goods_num'],
                                'return_quantity' => 0
                            ];
                        })
                    ];
                })[0];
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource of order details.
     * Export to .xlsx
     * @return \Illuminate\Http\Response
     * @param order id
     */

    public function download()
    {
        try {

            // recipe freeze numbers if not existing
            $this->recipe_freeze_numbers(request('store_id'));

            $_order = Order::query()
                ->with(['bayer', 'recipe', 'products'])
                ->where(['order.order_id' => request('id')])
                ->form()
                ->lastRate(request('rate', 4000))
                ->get()
                ->map(function ($item) {
                    return [
                        'order_sn' => $item['order_sn'],
                        'order_date' => $item['order_date'],
                        'order_state' => $item['order_state_id'],
                        'recipe_sn' =>  Str::of($item['order_sn'])->substr(8) ?? 0,
                        'recipe_number' => $item->recipe['full_number'] ?? NULL,
                        'buyer_name' => $item->buyer_name,
                        'bayer_mobile' => $item->bayer['reciver_info']['phone'],
                        'buyer_address' => $item->bayer['reciver_info']['address'],
                        'store_id' => $item->store_id,
                        'store_name' => $item->store_name,
                        'buyer_id' => $item->buyer_id,
                        'buyer_name' => $item->buyer_name,
                        'product_rows' => count($item->products),
                        'products' => collect($item->products)->map(function ($product) {
                            return [
                                'id' => $product['goods_id'],
                                'order_product_name' => $product['goods_name'],
                                'name' => $product['parrent_product'][0]['languages'][2]['goods_name'],
                                'quantity' => $product['goods_num'],
                                'unit_price' => $product['goods_price'],
                                'total_price' => $product['goods_pay_price']
                            ];
                        }),
                        'currency' => '$',
                        'exchange_rate' => request('rate', 4000),
                        'total_quantity' => $item->products_sum_goods_num,
                        'delivery_fee' => $item['delivery_fee'],
                        'total_price' => $item->goods_amount,
                        'grand_total_price' => $item->grand_total_price,
                        'grand_total_price_riel' => $item->grand_total_price_riel,
                        'grand_total_price_riel_recipe' => number_format($item->grand_total_price_riel, 0, ',', ','),
                    ];
                })[0];

            $recipe =  Recipe::query()
                ->where([
                    'order_sn' => $_order['recipe_sn'],
                    'recipe_number' => $_order['recipe_number']
                ])
                ->first();

            if ($recipe) {
                $recipe->increment('downloaded', 1);
            } else {

                $recipe = Recipe::query()->create([
                    'recipe_number' => $_order['recipe_number'],
                    'store_id' => $_order['store_id'],
                    'store_name' => $_order['store_name'],
                    'buyer_id' => $_order['buyer_id'],
                    'buyer_name' => $_order['buyer_name'],
                    'bayer_mobile' => $_order['bayer_mobile'],
                    'buyer_address' => $_order['buyer_address'],
                    'order_sn' => $_order['recipe_sn'],
                    'order_date' => $_order['order_date'],
                    'order_state' => $_order['order_state'] ?? 1,
                    'exchange_rate' => $_order['exchange_rate'],
                    'exchange_rate_id' => 1,
                    'total_quantity' => $_order['total_quantity'],
                    'delivery_fee' => $_order['delivery_fee'],
                    'grand_total_price' => $_order['grand_total_price'],
                    'grand_total_price_riel' => $_order['grand_total_price_riel'],
                    'comment' => NULL,
                    'downloaded' => 1,
                    'authorized_by' => auth()->id()
                ]);

                collect($_order['products'])
                    ->map(function ($item) use ($recipe) {
                        return RecipeItem::create([
                            'recipe_id' => $recipe['id'],
                            'product_id' => $item['id'],
                            'product_name' => $item['name'],
                            'unit_price' => $item['unit_price'],
                            'quantity' => $item['quantity'],
                            'total_price' => $item['total_price']
                        ]);
                    });
            }

            // QrCode if not existing
            // QrCode::format('png')->generate(
            //     $order['order_sn'],
            //     public_path('/QRCode/' . $order['order_sn'] . '.png')
            // );

            return Excel::download(
                new RecipeExport(
                    ['order' => $_order],
                    $_order['product_rows'],
                    $_order['order_sn']
                ),
                $_order['order_sn'] . '.xlsx'
            );

            // return view('exports.orders.stock-in-out', ['order' => $_order]);


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
                new OrderBulkExport([
                    'orderIds' => request('entryIds')
                ]),
                date('d-m-Y H:m:s', strtotime(now())) . '-' . 'orders.xlsx'
            );
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }

    /**
     * CHECK auto generate recipe_freeze_numbers.
     * For current month records.
     * @return \Illuminate\Http\Response
     * @param int $order:store_id
     */

    private function recipe_freeze_numbers($store_id = 0): bool
    {
        $recipe_freeze_number = Order::query()
            ->form()
            // ->recipeNumber()
            ->where([
                'order.store_id' => $store_id
            ])
            ->when(
                RecipeFreezeNumber::query()
                    ->where([
                        'store_id' => $store_id
                    ])->exists(),
                function ($query) {
                    $query->whereBetween(
                        'order.add_time',
                        [
                            strtotime(Carbon::now()->startOfMonth()),
                            strtotime(Carbon::now()->endOfMonth())
                        ]
                    );
                }
            )
            ->orderBy('order.order_id', 'asc')
            ->get()
            ->map(function ($item) {
                if (!RecipeFreezeNumber::query()
                    ->where([
                        'store_id' => $item['store_id'],
                        'order_sn' => $item['order_sn']
                    ])
                    ->exists()) {
                    RecipeFreezeNumber::create([
                        'store_id' => $item['store_id'],
                        'order_sn' => $item['order_sn'],
                        'number' => $item['rnumber'],
                        'order_date' =>  date('Y-m-d H:i:s', strtotime($item['order_date'])),
                    ]);
                }
            });

        return !empty($recipe_freeze_number) ? true : false;
    }

    private function recipeItems($recipe = [], $order = [])
    {
        if ($products = $order['products']) {
            foreach ($products as $product) {
                RecipeItem::create([
                    'recipe_id' => $recipe['id'],
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'unit_price' => $product['unit_price'],
                    'quantity' => $product['quantity'],
                    'total_price' => $product['total_price']
                ]);
            }
            return true;
        }
        return false;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exchangeRate(Request $request, $id)
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
                    } else if (isset($item['id'])) {
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
}
