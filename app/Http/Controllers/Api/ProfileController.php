<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Http\Requests\Api\ProfileRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Shop;
use App\Models\User;
use App\Http\Resources\ShopResource;

class ProfileController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display the authenticated user resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show()
    {
        return auth()->user()->getResource();
    }

    /**
     * Update the authenticated user profile.
     *
     * @param \App\Http\Requests\Api\ProfileRequest $request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();

        $user->update($request->allWithHashedPassword());

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatars');
        }

        return $user->refresh()->getResource();
    }


    public function shop(Shop $shop = null)
    {
        if (! $shop) {
            $shop = auth()->user()->shop;
        }

        $data = [];

       

        return response()->json([
            'shop' => new ShopResource($shop),
            'data' => $data,
        ]);
    }
}
