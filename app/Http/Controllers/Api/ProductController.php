<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\option;
use App\Http\Resources\ProductResource;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class ProductController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only([
            'myProducts',
            'toggleLock',
            'review',
            'store',
            'update',
            'favorite',
            'getFavorite',
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::simplePaginate();

        return ProductResource::collection($products);
    }


    public function myProducts()
    {
      //  $this->authorize('create', Product::class);

        $products = auth()->user()->shop->products()->simplePaginate();
        return ProductResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
       
      
        $product = $request->user()->shop->products()->create($request->all());

        $product->uploadFile('images');

        foreach($request->pu as $item)
        {
            $option = option::create([
                'product_id'  => $product->id ,
                'color'  => explode(',', $item)[0] ,
                'hex'=>explode(',', $item)[1] ,
                'size'  => explode(',', $item)[2] ,
                'quantity'  =>  explode(',', $item)[3],

            ]);

        }

        return new ProductResource($product);
     
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
