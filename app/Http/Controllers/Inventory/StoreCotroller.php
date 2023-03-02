<?php

namespace App\Http\Controllers\Inventory;

use App\Models\Role;

use App\Models\Team;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Store;
use App\Models\OD_cn\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Inventory\StoreResource;

class StoreCotroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Inventory/IndexStores', [
            'entries' => StoreResource::collection(
                Store::query()
                    ->status(request('status'))
                    ->month(request('month'))
                    ->search(request('term'))
                    ->orderByDesc('id')
                    ->paginate()
            ),
            'roles' => [],
            'statuses' => [
                ['value' => 'trashed', 'label' => 'Trashed'],
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
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'required',
        ]);

        $existing = Role::where('slug', $data['slug'])->first();

        if (!$existing) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);
            return $role;
        }
        return response(['error' => 1, 'message' => 'role already exists'], 409);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|Role
     */
    public function update(Request $request, Role $role = null)
    {
        if (!$role) {
            return response(['error' => 1, 'message' => 'role doesn\'t exist'], 404);
        }

        $role->name = $request->name ?? $role->name;

        if ($request->slug) {
            if ($role->slug != 'admin' && $role->slug != 'super-admin') {
                //don't allow changing the admin slug, because it will make the routes inaccessbile due to faile ability check
                $role->slug = $request->slug;
            }
        }
        $role->update();

        return $role;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if ($role->slug != 'admin' && $role->slug != 'super-admin') {
            //don't allow changing the admin slug, because it will make the routes inaccessbile due to faile ability check
            $role->delete();

            return response(['error' => 0, 'message' => 'role has been deleted']);
        }

        return response(['error' => 1, 'message' => 'you cannot delete this role'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function dataSync()
    {
        Store::query()->find(1)
        ??
        collect(
            Order::query()
                ->with('store')
                ->whereHas(
                    'store',
                    fn ($q) => $q->where('sc_id', '<>', 4)
                )
                ->select([
                    DB::raw("distinct store_id, store_name")
                ])
                ->take(10000)
                ->get()
                ->map(function ($item) {
                    return [
                        'value' => $item->store_id,
                        'label' => ucfirst($item->store_name),
                        'owner_id' => $item->store['member_id'],
                    ];
                })->unique('value')->values()
            )
        ->map(function ($item) {
            Store::query()
                ->create([
                    'name' => $item['label'],
                    'reference_id' => $item['value'],
                    'owner_id' => $item['owner_id']
                ]);
        });

        // return collect(
        //     Store::query()
        //     ->with('owner')
        //     ->get()
        // )
        // ->map(function ($item) {
        //     $user = User::create([
        //         'name' => $item['owner']['member_name'] ?? $item['owner']['member_mobile'],
        //         'password' => Hash::make('P@$$w0rd'),
        //         'email' => $item['owner']['member_email'] ?? $item['owner']['member_mobile'].'@piikmall.com.kh'
        //     ]);
        //     $user->ownedTeams()->save(
        //         Team::forceCreate([
        //             'user_id' => $user->id,
        //             'name' => explode(' ', $user->name, 2)[0] . "'s Team",
        //             'personal_team' => true,
        //         ])
        //     );
        //     $user->roles()->attach([
        //         5
        //     ]);
        // });

        return to_route('inventory.stores.index');
    }
}
