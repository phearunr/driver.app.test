<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UserBulkExport implements
    FromCollection,
    ShouldAutoSize
{
    private $users;

    public function __construct(
        $users
    ) {
        $this->users = $users;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::query()
            ->whereIn(
                'id',
                preg_split("/[,]/", $this->users['entryIds'])
            )
            ->select([
                'id',
                'name',
                'email',
                'created_at'
            ])
            ->get()
            ->map(function($item){
                return [
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'role' => $item['role'],
                    'created_at' => $item['created_at'],
                ];
            });
    }
}
