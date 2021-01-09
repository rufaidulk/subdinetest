<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use App\Jobs\StockShortEmailJob;
use Illuminate\Support\Facades\Validator;

class OrderController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.product_id' => 'required|exists:'. Product::class . ',id',
            '*.quantity' => 'required|integer'
        ], [], ['*.product_id' => 'product', '*.quantity' => 'quantity']);
        
        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $products = Product::whereIn('id', array_column($request->all(), 'product_id'))->get();
        $this->assertStockShortage($request, $products);

        DB::beginTransaction();
        
        try
        {   
            $order = $this->createOrder($request, $products);
            $this->createOrderProducts($request, $order, $products);
            
            DB::commit();
        }
        catch (Exception $ex) 
        {
            DB::rollBack();
            logger($ex);
            return $this->error('Something is no right', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $response['data'] = new OrderResource($order);

        return $this->success($response, 'Order Created', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $response['data'] = new OrderResource($order);

        return $this->success($response, 'Order details', Response::HTTP_OK); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    private function createOrder(Request $request, $products)
    {
        $order = new Order();
        $order->ref_no = 'OD' . rand(0001, 9999);
        $order->total_quantity = array_sum(array_column($request->all(), 'quantity'));
        $order->total_amount = $products->sum('quantity');
        $order->save();

        return $order;
    }

    private function createOrderProducts(Request $request, Order $order, $products)
    {
        foreach ($request->all() as $attributes)
        {
            $price = $products->where('id', $attributes['product_id'])->first()->price;
            $data[] = [
                'order_id' => $order->id,
                'product_id' => $attributes['product_id'],
                'quantity' => $attributes['quantity'],
                'price' => $price,
                'line_total' => $price * $attributes['quantity'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        DB::table('order_products')->insert($data);
    }

    private function assertStockShortage(Request $request, $products)
    {
        $shortProducts = [];
        foreach ($request->all() as $attributes)
        {
            $product = $products->where('id', $attributes['product_id'])->first();
            if ($product->alert_quantity && ($product->quantity - $attributes['quantity']) <= $product->alert_quantity) {
                $shortProducts[] = $product->id;
            }
        }

        if (empty($shortProducts)) {
            return;
        }
        StockShortEmailJob::dispatch($shortProducts);
    }
}
