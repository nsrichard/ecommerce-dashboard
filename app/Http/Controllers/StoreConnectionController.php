<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreCredential;
use App\Clients\StoreClientFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreConnectionController extends Controller
{
    public function showWooForm(Store $store)
    {
        abort_if($store->platform->value !== 'woocommerce', 404);

        return view('stores.connect_woocommerce', compact('store'));
    }

    public function storeWooCredentials(Request $request, Store $store, StoreClientFactory $factory)
    {
        abort_if($store->platform->value !== 'woocommerce', 404);

        $data = $request->validate([
            'base_url' => ['required', 'url'],
            'key'      => ['required', 'string'],
            'secret'   => ['required', 'string'],
        ]);

        // Guardar credenciales cifradas
        $cred = StoreCredential::updateOrCreate(
            ['store_id' => $store->id],
            [
                'auth_type'   => 'apikey',
                'credentials' => [
                    'base_url' => rtrim($data['base_url'], '/'),
                    'key'      => $data['key'],
                    'secret'   => $data['secret'],
                ],
                'rotated_at'  => now(),
            ]
        );

        // Validar con healthCheck
        $client   = $factory->for($store);
        $ok       = $client->healthCheck();

        if (! $ok) {
            // Conexión fallida: eliminar credencial
            $cred->delete();
            return back()
                ->withErrors(['base_url' => 'No se pudo conectar. Verifica URL, key y secret.'])
                ->withInput();
        }

        // Conexión exitosa
        $store->update(['status' => 'connected']);

        return redirect()
            ->route('stores.index')
            ->with('status', "WooCommerce conectada para {$store->name}.");
    }
}
