<?php

namespace App\Http\Controllers\OAuth;

use App\Domain\Enums\Platform;
use App\Models\Store;
use App\Models\StoreCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ShopifyOAuthController
{
    public function redirect(Request $request, Store $store)
    {
        abort_if($store->platform !== Platform::SHOPIFY, 404);

        $state = Str::uuid()->toString();
        session(['shopify_oauth_state' => $state]);

        $params = http_build_query([
            'client_id' => config('services.shopify.client_id'),
            'scope' => config('services.shopify.scopes', 'read_products,read_orders'),
            'redirect_uri' => config('services.shopify.redirect_uri'),
            'state' => $state,
        ]);

        $shop = $store->domain;
        return redirect("https://{$shop}/admin/oauth/authorize?{$params}");
    }

    public function callback(Request $request, Store $store)
    {
        abort_if($store->platform !== Platform::SHOPIFY, 404);

        $state = $request->string('state');
        if ($state !== session('shopify_oauth_state')) {
            return redirect()->route('stores.index')->withErrors('Estado OAuth inválido.');
        }

        $code = $request->string('code');
        $shop = $store->domain;

        $resp = Http::asForm()->post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => config('services.shopify.client_id'),
            'client_secret' => config('services.shopify.client_secret'),
            'code' => $code,
        ]);

        if (!$resp->ok()) {
            return redirect()->route('stores.index')->withErrors('No se pudo obtener access_token.');
        }

        $accessToken = $resp->json('access_token');
        StoreCredential::updateOrCreate(
            ['store_id' => $store->id],
            [
                'auth_type' => 'oauth',
                'credentials' => ['access_token' => $accessToken],
                'scopes' => explode(',', config('services.shopify.scopes', 'read_products,read_orders')),
            ]
        );

        $store->update(['status' => 'connected']);

        return redirect()->route('stores.index')->with('status', 'Shopify conectada.');
    }
}
