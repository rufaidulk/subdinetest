<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderStatisticController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mostSold = DB::table('order_products AS op')
                        ->select(DB::raw('count(*) as product_count, op.product_id, p.name as product'))
                        ->leftJoin('products AS p', 'p.id', '=' ,'op.product_id')
                        ->whereBetween(DB::raw('DATE(op.created_at)'), [date('Y-m-d', strtotime("-10 days")), date('Y-m-d')])
                        ->groupBy('op.product_id')->orderBy('product_count', 'desc')->limit(5)
                        ->get();

        $leastSold = DB::table('order_products AS op')
                        ->select(DB::raw('count(*) as product_count, op.product_id, p.name as product'))
                        ->leftJoin('products AS p', 'p.id', '=' ,'op.product_id')
                        ->whereBetween(DB::raw('DATE(op.created_at)'), [date('Y-m-d', strtotime("-10 days")), date('Y-m-d')])
                        ->groupBy('op.product_id')->orderBy('product_count', 'asc')->limit(5)
                        ->get();
                        
        $response['data']['most_sold'] = $mostSold;
        $response['data']['least_sold'] = $leastSold;

        return $this->success($response, 'Order details', Response::HTTP_OK); 
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
        //
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
}
