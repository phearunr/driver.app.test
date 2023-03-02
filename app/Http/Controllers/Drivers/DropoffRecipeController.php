<?php

namespace App\Http\Controllers\Drivers;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Models\Drivers\Driver;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Drivers\DropoffClaim;
use App\Models\Drivers\DropoffRecipe;
use App\Http\Resources\Drivers\DropoffClaimResource;
use App\Http\Resources\Reports\DriverDropoffResource;
use App\Http\Resources\Drivers\DropoffClaimDetailsResource;

class DropoffRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Reports/IndexDriverDropoffs', [
            'entries' => DropoffClaimResource::collection(
                DropoffClaim::query()
                    ->form()
                    ->orderByDesc('id')
                    ->paginate()
            ),
            'scanOutBys' => Recipe::join('users', 'recipes.authorized_by', '=', 'users.id')
                ->selectRaw('distinct recipes.authorized_by As c, users.name As label')
                ->get(),
            'dropoffBys' => User::query()
                ->whereHas('roles', function ($role) {
                    $role->where('id', 7);
                })
                ->select([
                    'id As value',
                    'name As label'
                ])
                ->get(),
            'statuses' => [
                ['value' => 'scan-out', 'label' => 'Scan out'],
                ['value' => 'drop-off ', 'label' => 'Drop off']
            ],
            'months' => DB::table('recipes')
                ->selectRaw('distinct STRFTIME("01-%m-%Y", created_at) as value, STRFTIME("%m-%Y",created_at) as label')
                ->orderByDesc('value')
                ->get(),
            'pagers' => [
                ['value' => 15, 'label' => 15],
                ['value' => 30, 'label' => 30],
                ['value' => 45, 'label' => 45],
                ['value' => 65, 'label' => 60],
                ['value' => 100, 'label' => 100]
            ],
            'queryParams' => request()->all(['pager', 'scanOutBy', 'dropoffBy', 'status', 'month', 'term'])
        ]);
    }

    public function recipe_filter()
    {
        try {

            request()->validate(['term' => ['required']]);

            return response()->json(
                Recipe::leftJoin(
                    'recipe_items',
                    'recipes.id',
                    '=',
                    'recipe_items.recipe_id'
                )
                    ->search(request('term'))
                    ->select(
                        'recipe_id',
                        'recipe_number',
                        'product_id',
                        'product_name',
                        'quantity As order_quantity',
                        'quantity As drop_off_quantity',
                        '1 As drop_off_quantity_status'
                    )
                    ->whereNotIn(
                        'recipes.id',
                        DB::table('dropoff_recipes')->pluck('recipe_id')->toArray()
                    )
                    ->get()
            );
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'items.*.recipe_id' => [
                        'required',
                        'unique:dropoff_recipes,recipe_id'
                    ],
                    'items.*.order_quantity' => ['required', 'numeric'],
                ],
                [
                    'items.*.recipe_id.required' => 'The recipe is required',
                    'items.*.recipe_id.unique' => 'The recipe is :unique',
                    'items.*.order_quantity.numeric' => 'The Order Quantity must be at least :numeric',
                ]
            );

            DB::transaction(function () use ($request) {
                $dropoffClaim = DropoffClaim::query()->create([
                    'scanouted_by' => auth()->user()->roles[0]['id'] === 5 ? auth()->id() : 6,
                    'scanouted_at' => now(),
                    'dropoffed_by' => auth()->user()->roles[0]['id'] === 7 ? auth()->id() : 7,
                    'dropoffed_at' => auth()->user()->roles[0]['id'] === 7 ? now() : NULL,
                    'status' => auth()->user()->roles[0]['id'] === 7 ? 1 : 0,
                    'comments' => $request['comments'],
                ]);
                foreach ($request['items'] as $item) {

                    if ($dropoffRecipe = DropoffRecipe::firstOrCreate([
                        'dropoff_claim_id' => $dropoffClaim->id,
                        'recipe_id' => $item['recipe_id']
                    ])) {

                        $dropoffRecipe->dropoffRecipeItems()->create([
                            'product_id' => $item['product_id'],
                            'product_name' => $item['product_name'],
                            'order_quantity' => $item['order_quantity'],
                            'drop_off_quantity' => $item['drop_off_quantity'],
                            'drop_off_quantity_status' => $item['drop_off_quantity_status']
                        ]);
                    }
                }
                return true;
            });
            return redirect()->back();
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
     */

    public function show($id)
    {
        return new DropoffClaimDetailsResource(
            DropoffClaim::query()
                ->form()
                ->findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        try {

            return request()->user()->roles;

            if ($dropoffClaim = DropoffClaim::findOrFial(request('id'))) {
                $dropoffClaim->update([
                    'dropoffed_by' => auth()->user()->roles[0]['id'] === 7 ? auth()->id() : 7,
                    'dropoffed_at' => auth()->user()->roles[0]['id'] === 7 ? now() : NULL,
                    'status' => auth()->user()->roles[0]['id'] === 7 ? 1 : 0,
                ]);
            }
            return redirect()->back();
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
