<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ShopOrderResource;
use App\Support\Cart\CartServices;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    private $repository;

    /**
     * Create Order Controller instance.
     *
     * @param \App\Repositories\OrderRepository $repository
     */
    public function __construct(OrderRepository $repository)
    {
        $this->middleware('auth:sanctum');

        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = auth()->user()->orders()->simplePaginate();

        return OrderResource::collection($orders);
    }

    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function shopOrders()
    {
        $orders = auth()->user()->shop->orders()->simplePaginate();

        return ShopOrderResource::collection($orders);
    }

    /**
     * Display the specified order.
     *
     * @param \App\Models\Order $order
     * @return \App\Http\Resources\OrderResource
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Display the specified order.
     *
     * @param \App\Http\Requests\Api\OrderRequest $request
     * @return \App\Http\Resources\OrderResource
     */
    public function store(Request $request)
    {
        $cartServices = app(CartServices::class);

        $cart = $cartServices
            ->setUser($request->user())
            ->setIdentifier($request->header('cart-identifier'))
            ->getCart();



        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => [__('The cart is empty')],
            ]);
        }
        
     
        if (! $cart->payment_method) {
            throw ValidationException::withMessages([
                'cart' => [__('The payment method not set in the cart')],
            ]);
        }

        $order = $this
            ->repository
            ->setUser($request->user())
            ->create($cart);
           
          
          //  $this->sendmail($order);
          

          return new OrderResource($order);
        }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
