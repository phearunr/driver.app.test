<?php

namespace Database\Seeders;


use App\Models\Team;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Factories\UserFactory;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User ;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        collect([
            [
                'email' => 'phearun.reth.info@gmail.com',
                'password' => Hash::make('P@$$w0rd'),
                'name' => 'phearun.reth',
                'role_id' => 2
            ],
            [
                'email' => 'user01@piikmall.com',
                'password' => Hash::make('P@$$w0rd'),
                'name' => 'user01',
                'role_id' => 5
            ],
            [
                'email' => 'd.chanveasna11@gmail.com',
                'password' => Hash::make('P@$$w0rd'),
                'name' => 'd.chanveasna',
                'role_id' => 4
            ],

            [
                'email' => 'chip.sokear@gmail.com',
                'password' => Hash::make('P@$$w0rd'),
                'name' => 'chip.sokear',
                'role_id' => 5
            ],
            [
                'email' => 'user02@piikmall.com',
                'password' => Hash::make('P@$$w0rd'),
                'name' => 'user02',
                'role_id' => 5
            ],
            [
                'email' => 'store.owner@piikmall.com',
                'password' => Hash::make('P@$$w0rd'),
                'name' => 'store.owner',
                'role_id' => 5
            ],

            [
                'email' => 'driverman@piikmall.com',
                'password' => Hash::make('P@$$w0rd'),
                'name' => 'Driverman',
                'role_id' => 7
            ],
            [
                'email' => 'customer@piikmall.com',
                'password' => Hash::make('P@$$w0rd'),
                'name' => 'customer',
                'role_id' => 8
            ]
        ])->map(function ($item) {
            $user = User::create([
                'name' => $item['name'],
                'password' => $item['password'],
                'email' => $item['email']
            ]);

            $users = User::find($user->id);
            $users->ownedTeams()->save(
                Team::forceCreate([
                    'user_id' => $user->id,
                    'name' => explode(' ', $user->name, 2)[0] . "'s Team",
                    'personal_team' => true,
                ])
            );
            $user->assignRole([$item['role_id']]);

        });
    }
}
