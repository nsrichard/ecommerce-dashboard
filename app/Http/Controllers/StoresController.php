<?php

namespace App\Http\Controllers;

use App\Domain\Enums\Platform;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoresController extends Controller
{
    public function index()
    {
        $stores = Store::latest()->get();

        return view('stores.index', compact('stores'));
    }

    public function fragment()
    {
        $stores = Store::latest()->get();
        return view('stores.partials.list', compact('stores'));
    }


    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'platform' => ['required', Rule::in([Platform::SHOPIFY->value, Platform::WOOCOMMERCE->value])],
            'domain' => ['required', 'string', 'max:255'],
        ]);

        $data['domain'] = strtolower(trim($data['domain']));

        $exists = Store::where('platform', $data['platform'])
            ->where('domain', $data['domain'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['domain' => 'Ya existe una tienda con esa plataforma y dominio.'])->withInput();
        }

        $store = Store::create($data);

        return redirect()->route('stores.index')->with('status', "Store {$store->name} creada.");
    }
}
