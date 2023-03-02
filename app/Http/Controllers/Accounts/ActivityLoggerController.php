<?php

namespace App\Http\Controllers\Accounts;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ActivityLogger;
use App\Exports\ActivityLogExport;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Resources\Accounts\ActivityLogResource;


class ActivityLoggerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return Inertia::render('Accounts/IndexActivityLogs', [
            'entries' =>  ActivityLogResource::collection(
                ActivityLogger::query()
                    ->status(request('status'))
                    ->month(request('month'))
                    ->search(request('term'))
                    ->orderByDesc('id')
                    ->paginate()
            ),
            'roles' => [],
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
    /*
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminat
    * e\Http\Response
    */

   public function show($id)
   {
       return response()->json(
           ActivityLogger::query()
            ->with(['subject', 'causer'])
            ->findOrFail($id)
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
             foreach (ActivityLogger::when(request('status') == 'trashed', function($query){
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

     public function bulkExports()
     {
         try {

             request()->validate([
                 'entryIds' => ['required']
             ]);

             return Excel::download(
                 new ActivityLogExport([
                     'activityIds' => request('entryIds')
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
