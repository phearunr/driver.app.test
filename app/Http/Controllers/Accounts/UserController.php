<?php

namespace App\Http\Controllers\Accounts;

use Exception;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Exports\UserBulkExport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Accounts\UserResource;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Accounts/Users/Index', [
            'entries' =>  UserResource::collection(
                User::query()
                    ->whereNotIn('id', [1, 4])
                    ->hasRole(request('role'))
                    ->status(request('status'))
                    ->month(request('month'))
                    ->search(request('term'))
                    ->orderByDesc('id')
                    ->paginate()
            ),
            'roles' => Role::query()
                ->select([
                    'id As value',
                    'name As label'
                ])
                ->whereNotIn('id', [1, 2, 3])
                ->get(),
            'statuses' => [
                // ['value' => '1', 'label' => 'Online'],
                // ['value' => '2', 'label' => 'Offline'],
                // ['value' => '3', 'label' => 'Active'],
                // ['value' => '4', 'label' => 'Disable'],
                ['value' => '5', 'label' => 'Trashed'],
            ],
            'months' => [
                ['value' => 'logged-in-today', 'label' => 'Logged In Today'],
                ['value' => 'logged-in-last-7-days', 'label' => 'Logged In Last 7 Days'],
                ['value' => 'logged-in-last-30-days', 'label' => 'Logged In Last 30 Days'],
                ['value' => 'not-logged-in-for-30-days', 'label' => 'Not Logged In For 30 Days']
            ],
            'queryParams' => request()->all(['role', 'status', 'lastLoginAt', 'month', 'term'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $input)
    {
        request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required| min:4| max:7 |confirmed',
            'password_confirmation' => 'required| min:4'
        ]);

        DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                $user->assignRole([5]);
                $this->createTeam($user);
            });
        });
        return to_route('accounts.users.index');
    }

    /**
     * Create a personal team for the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));
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
            User::query()->findOrFail($id)
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return Inertia::render('Accounts/Users/Edit', [
            'users' => $user
        ]);
    }

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function updatePassword(User $user)
    {
        $input = request()->validate([
            'password' => 'required| min:4| max:7|confirmed',
            'password_confirmation' => 'required| min:4'
        ]);

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }

    public function UpdateUserProfileInformation(User $user)
    {

        $input = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
        ])->save();
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
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
            foreach (User::find(request('entryIds')) as $entry) {
                $entry->delete();
                $entry->update(['status' => 5]);
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
                new UserBulkExport([
                    'entryIds' => request('entryIds')
                ]),
                date('d-M-Y H:m:s', strtotime(now())) . 'users.xlsx'
            );
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }


    /**
     *  Restore user data
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */

    public function restore($id)
    {
        User::where('id', $id)->withTrashed()->restore();
    }
    /**
     * Force delete user data
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */

    public function forceDelete($id)
    {
        User::where('id', $id)->withTrashed()->forceDelete();
    }

    /**
     * Restore all archived users
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function restoreAll()
    {
        User::onlyTrashed()->restore();

    }
}
