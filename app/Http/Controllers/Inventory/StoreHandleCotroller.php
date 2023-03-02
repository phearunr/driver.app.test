<?php

namespace App\Http\Controllers\Inventory;

use Exception;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;

use App\Models\Store;
use App\Models\StoreHandle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use App\Http\Resources\Inventory\StoreHandleResource;

class StoreHandleCotroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Inventory/IndexStoreHandles', [
            'entries' =>  StoreHandleResource::collection(
                User::query()
                    ->with(['store_handles'])
                    ->whereHas('store_handles', function ($quey) {
                        $quey->status(request('status'));
                        $quey->month(request('month'));
                        $quey->search(request('term'));
                        $quey->where('store_handles.store_id', '<>', 0);
                    })
                    ->orderByDesc('id')
                    ->paginate()
            ),
            'roles' => [],
            'stores' => Store::query()
                ->select([
                    'id',
                    'name'
                ])
                ->when(request('term'), function ($query, $term) {
                    $query->where('name', 'like', "%$term%");
                })
                ->limit(15)
                ->get(),
            'users' => User::query()
                ->when(request('term'), function ($query, $term) {
                    $query->where('name', 'like', "%$term%");
                })
                ->whereHas('roles', function ($role) {
                    $role->whereIn('role_id', [4, 5]);
                })
                ->select([
                    'id',
                    'name'
                ])->get(),
            'statuses' => [
                ['value' => 'trashed', 'label' => 'Trashed']
            ],
            'months' => DB::table('roles')
                ->selectRaw('distinct STRFTIME("01-%m-%Y", created_at) as value, STRFTIME("%m-%Y",created_at) as label')
                ->orderByDesc('value')
                ->get(),
            'queryParams' => request()->all(['role', 'status', 'lastLoginAt', 'month', 'term'])
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
        $cred = $request->validate(
            [
                'userId' => [
                    'required',
                    Rule::unique('store_handles', 'user_id')->withoutTrashed()
                ],
                'storeIds' => ['required', 'array'],
            ],
            [
                'userId.required' => 'The handle name is :required.',
                'userId.unique' => 'The handle name is already.',
                'storeIds.required' => 'The store name is required',
                'storeIds.array' => 'The store name must be :array'
            ]
        );

        if( $storeIds = request('storeIds')){
            collect($storeIds)
                ->each(function ($item) use ($cred) {
                   // return $item;
                    StoreHandle::create([
                        'store_id' => $item ?? 0,
                        'user_id' => $cred['userId']
                    ]);
            });
        }
        return to_route('inventory.storehandles.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \App\Models\Role $role
     */
    public function show(Role $role)
    {
        return $role;
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

            StoreHandle::query()
                ->whereIn('user_id', request('entryIds'))
                ->delete();
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
