<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Support\Cart\CartServices;
use App\Models\option;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function get()
    {
        request()->validate([
            'payment_method' => 'numeric|in:0,1',
        ]);

        $cartServices = app(CartServices::class);

        return $cartServices
            ->setUser(auth()->user())
            ->setIdentifier(request()->header('cart-identifier'))
            ->paymentMethod(request()->payment_method)
            ->shippingCost(request()->shipping_cost)
            ->getCart();
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
            'option_id' => 'required|numeric|min:1',
        ]);
      
        $cartServices = app(CartServices::class);

        $cartServices
            ->setUser(auth()->user())
            ->setIdentifier($request->header('cart-identifier'));

        $cartServices->addItem(
            $request->product_id,
            $request->quantity,
            $request->option_id,

        );

        return $cartServices->getCart();
    }

    public function updateItem(CartItem $cartItem, Request $request)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);

        if (option::findorfail($cartItem->option_id)->quantity < $request->quantity) {
            throw ValidationException::withMessages([
                'quantity' => __('The requested quantity is not available.'),
            ]);
        }

        $cartItem->update($request->only('quantity'));

        $cartServices = app(CartServices::class);

        $cart = $cartServices
            ->setUser(auth()->user())
            ->setIdentifier($request->header('cart-identifier'))
            ->getCart();

        $cartServices->updateTotals();


        return $cart->refresh();
    }

    public function update(Request $request)
    {
      

        $cartServices = app(CartServices::class);

        $cart = $cartServices
            ->setUser(auth()->user())
            ->setIdentifier($request->header('cart-identifier'))
            ->getCart();

        if ($value = $request->input('payment_method')) {
            $cart->forceFill(['payment_method' => $value]);
        }

        $cart->forceFill([
            'shipping_cost' => 120 
            ,
        ])->save();

        if ($value = $request->input('notes')) {
            $cart->forceFill(['notes' => $value]);
        }

      

        $cart->save();

        return $cart;
    }

    public function deleteItem(CartItem $cartItem, Request $request)
    {
        $cartItem->delete();

        $cartServices = app(CartServices::class);

        $cart = $cartServices
            ->setUser(auth()->user())
            ->setIdentifier($request->header('cart-identifier'))
            ->getCart();

        $cartServices->updateTotals();


        return $cart->refresh();
    }
}
