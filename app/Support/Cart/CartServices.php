<?php

namespace App\Support\Cart;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\option;

use App\Models\CartItem;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class CartServices
{

    protected $identifier;

    /**
     * @var \App\Models\Cart
     */
    protected $cart;

    /**
     * @var \App\Models\CartItem[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $items;

    /**
     * @var \App\Models\User|null
     */
    protected $user;

    /**
     * @var array
     */
    protected $cartData = [];

    public function __construct($identifier = null, User $user = null)
    {
        $this->cartData['identifier'] = $identifier;

        $this->user = $user;
    }
    public function setUser(?User $user)
    {
        $this->user = $user;

        return $this;
    }
    public function getIdentifier()
    {
        if (empty($this->cartData['identifier'])) {
            $this->setIdentifier(Str::uuid());
        }

        return $this->cartData['identifier'];
    }

    public function setIdentifier($identifier)
    {
        $this->cartData['identifier'] = $identifier;

        return $this;
    }

    public function paymentMethod($paymentMethod)
    {
        $this->cartData['payment_method'] = $paymentMethod;

        return $this;
    }

    /**
     * @param $shippingCost
     * @return $this
     */
    public function shippingCost($shippingCost)
    {
        $this->cartData['shipping_cost'] = $shippingCost;

        return $this;
    }

    public function getCart()
    {
        $cart = $this->getCartViaUser() ?: $this->getCartViaIdentifier();
        $cart = $cart ?: $this
            ->createCartForIdentifier()
            ->getCartViaIdentifier();

        $this->refreshItems();

        return $cart;
    }


    public function getCartViaUser()
    {
        if (! $this->getUser()) {
            return;
        }

        return $this->cart = Cart::where('user_id', $this->getUser()->id)->first();
    }

    /**
     * @return \App\Models\Cart|null
     */
    public function getCartViaIdentifier()
    {
        return $this->cart = Cart::where('identifier', $this->getIdentifier())->first();
    }

    /**
     * @return $this
     */
    public function createCartForIdentifier()
    {
        if ($user = $this->getUser()) {
            $this->cartData['user_id'] = $user->id;
        }

        $this->cart = Cart::create($this->cartData);

        return $this;
    }

    /**
     * @return $this
     */
    public function refreshItems()
    {
        $this->items = $this->cart->items()->get();

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }


    public function addItem($productId, int $quantity = 1, $option_id)
    {
        // Ensure that the given product id is the key of product.
        if ($productId instanceof Product) {
            $productId = $productId->id;
        }

        // Ensure that the given product is exists.
        if (! $product = Product::find($productId)) {
            
            throw ValidationException::withMessages([
                'quantity' => __('The requested product is not found.'),
            ]);
        }


        if (! $option = option::find($option_id)) {
            
            throw ValidationException::withMessages([
                'quantity' => __('The option is not found.'),
            ]);
        }




        // Ensure that the requested quantity is available.
       

        // Now we check if the item is already exists with the same color ans size
        // We update it's quantity
        // Otherwise we create a new item.

        $item = $this
            ->getCart()
            ->items()
            ->where('product_id', $product->id)
            ->where('option_id', $option->id)
            ->first();

        if (!$item) {
            $item = $this->getCart()->items()->create([
                'product_id' => $product->id,
                'price' => $product->getPrice(),
                'quantity' => $quantity,
                'option_id' => $option->id,
               
            ]);
           
           

        } else {
            // Ensure that the requested quantity is available.
            $newQuantity = $item->quantity + $quantity;
            if ($option->quantity < $newQuantity) {
                throw ValidationException::withMessages([
                    'quantity' => __('The requested quantity is not available.'),
                ]);
            }
            $item->update([
                'quantity' => $newQuantity,
            ]);
        }

        // Update the cart items.
        $this->refreshItems();
       // $this->shipping($item);
        $this->updateTotals();
        
        // Fire an event to handle real-time.

        return $this;
    }

    public function updateTotals()
    {
        $cart = $this->getCart();

        $cart->forceFill([
            'sub_total' => $cart->items->map(function (CartItem $item) {
                return $item->price * $item->quantity;
            })->sum(),
          
            
        ])->save();
        $this->cart = $cart->refresh();

        return $this;
    }

}