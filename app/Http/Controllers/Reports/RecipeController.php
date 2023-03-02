<?php

namespace App\Http\Controllers\Reports;

use Exception;
use Inertia\Inertia;
use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Exports\RecipeBulkExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Reports\RecipeResource;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Reports/IndexRecipes', [
            'entries' => RecipeResource::collection(
                Recipe::query()
                    ->with('authorizedby')
                    ->authorizedBy(request('authorizedBy'))
                    ->status(request('status'))
                    ->month(request('month'))
                    ->search(request('term'))
                    ->orderByDesc('id')
                    ->paginate()
            ),
            'authorizedBys' => Recipe::join('users', 'recipes.authorized_by', '=', 'users.id')
                ->selectRaw('distinct recipes.authorized_by As value, users.name As label')
                ->get(),
            'statuses' => [['value' => 'trashed', 'label' => 'Trashed']],
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
            'queryParams' => request()->all(['pager', 'authorizedBy', 'status', 'month', 'term'])
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
        //
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
            Recipe::query()
                ->with('recipeItems')
                ->findOrFail($id),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function bulkAction()
    {
        try {

            request()->validate([
                'entryIds' => ['required']
            ]);

            return Excel::download(
                new RecipeBulkExport([
                    'entryIds' => request('entryIds')
                ]),
                date('d-m-Y H:m:s', strtotime(now())) . 'currency.xlsx'
            );
        } catch (Exception $error) {
            return response([
                'exception' => get_class($error),
                'errors' => $error->getMessage()
            ], 400);
        }
    }
}
