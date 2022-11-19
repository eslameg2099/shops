<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Shop;
use App\Models\option;

use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\ShopOrder;
use Illuminate\Support\Facades\DB;
use Laraeast\LaravelSettings\Facades\Settings;
use Illuminate\Validation\ValidationException;

class OrderRepository
{
    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    protected $order;

    /**
     * The user instance.
     *
     * @var \App\Models\Customer
     */
    protected $user;

    /**
     * Get the order instance.
     *
     * @return \App\Models\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the order instance.
     *
     * @param \App\Models\Order $order
     * @return \App\Repositories\OrderRepository
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get the user instance.
     *
     * @return \App\Models\Customer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the user instance.
     *
     * @param \App\Models\Customer $user
     * @return \App\Repositories\OrderRepository
     */
    public function setUser(Customer $user)
    {
        $this->user = $user;

        return $this;
    }

   

    /**
     * Store the newly created order in the storage.
     *
     * @param Cart $cart
     * @return \App\Models\Order
     */
    public function create(Cart $cart)
    {
        DB::beginTransaction();
        $data = $this->qualifyData($cart);

        $order = $this->getUser()->orders()->create($data);

        foreach ($data['orders'] as $orderItem) {
            $shopOrder = $order->shopOrders()->create($orderItem);

            foreach ($orderItem['items'] as $item) {
                if ($product = option::where('id', $item['option_id'])->first()) {

                    if ($product->quantity < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'quantity' => __('The requested quantity is not available.'),
                        ]);
                    }

                    $product->update(['quantity' => $product->quantity - $item['quantity']]);
                }

                $shopOrder->items()->create($item);
               // broadcast(new ShopOrderCreated($shopOrder))->toOthers();

            }


        }

        // TODO: save cart payment to order.

        $cart->delete();

        $this->setOrder($order);
        DB::commit();

        return $this->getOrder();
    }

    /**
     * Qualify data for the order.
     *
     * @param Cart $cart
     * @return array
     */
    public function qualifyData(Cart $cart)
    {
        $address = $cart->address;

        $order = [
            'payment_method' => $cart->payment_method,
            'shipping_cost' => $cart->shipping_cost_shop,
            'discount' => $cart->discount,
            'notes' => $cart->notes,
            'sub_total' => $cart->sub_total,
            'total' => $cart->sub_total ,
            
            'products' => $cart->items->map(function (CartItem $item) {
                return [
                    'id' => $item->product_id,
                    'shop_id' => $item->product->shop_id,
                    'price' => $item->product->getPrice(),
                    'quantity' => $item->quantity,
                    'option_id' => $item->option_id,
                 
                ];
            })->toArray(),
            'orders' => [],
        ];

        foreach (collect($order['products'])->groupBy('shop_id') as $shopId => $shopOrder) {
            $items = $shopOrder->map(function ($item) {
                return collect($item)->except('shop_id')->toArray();
            });
         
            $subTotal = $items->map(function ($item) {
            
                return ['total' => $item['price'] * $item['quantity']];
            })->sum('total');
          
        

          //  $discount = $shippingCost / 100 * $order['discount_percentage'];

            $order['orders'][] = [
                'shop_id' => $shopId,
                'sub_total' => $subTotal,
             
                'discount' => $cart->discount_percentage,
                'items' => $items->map(function ($item) {
                    return [
                        'product_id' => $item['id'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'option_id' => $item['option_id'],
                    ];
                }),
            ];
        }

        $order['shipping_cost'] = collect($order['orders'])->sum('shipping_cost');
        $order['sub_total'] = collect($order['orders'])->sum('sub_total');

        return $order;
    }

    /**
     * Get the shipping cost value.
     *
     * @param \App\Models\Shop $shop
     * @param \App\Models\Address $address
     * @return float
     */
    public function calculateShippingCost(CartItem $item)
    {
        if($item->shipping == '1')
        {
           return $item->z;
        }
        else 0;

    }
}
