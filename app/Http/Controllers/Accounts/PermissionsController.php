<?php

namespace App\Http\Controllers\Accounts;
use App\Models\Role;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Http\Resources\Accounts\PermissionResource;
use App\Models\Permission;

class PermissionsController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return Inertia::render('Accounts/IndexPermissions', [
            'entries' =>  PermissionResource::collection(
                Permission::query()
                    //->with('permissions')
                    ->whereNotIn('id', [1,2])
                    ->status(request('status'))
                    ->month(request('month'))
                    ->search(request('term'))
                    ->orderByDesc('id')
                    ->paginate()
            ),
            'models' => [],
            'statuses' => [
                ['value' => 'trashed', 'label' => 'Trashed'],
                ['value' => 'active', 'label' => 'Active'],
            ],
            'months' => DB::table('roles')
            ->selectRaw('distinct STRFTIME("01-%m-%Y", created_at) as value, STRFTIME("%m-%Y",created_at) as label')
            ->orderByDesc('value')
            ->get(),
            'queryParams' => request()->all(['model', 'status', 'month','term'])
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'required',
        ]);

        $existing = Role::where('slug', $data['slug'])->first();

        if (! $existing) {
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
    public function show(Role $role) {
        return $role;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|Role
     */
    public function update(Request $request, Role $role = null) {
        if (! $role) {
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
    public function destroy(Role $role) {
        if ($role->slug != 'admin' && $role->slug != 'super-admin') {
            //don't allow changing the admin slug, because it will make the routes inaccessbile due to faile ability check
            $role->delete();

            return response(['error' => 0, 'message' => 'role has been deleted']);
        }

        return response(['error' => 1, 'message' => 'you cannot delete this role'], 422);
    }
}
