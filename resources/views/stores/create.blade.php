<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">+ Tienda</h2>
  </x-slot>

  <div class="py-6 max-w-xl">
    <form method="POST" action="{{ route('stores.store') }}" x-data>
      @csrf
      <label class="block mb-2">Nombre
        <input name="name" class="border p-2 w-full" required>
      </label>

      <label class="block mb-2">Plataforma
        <select name="platform" class="border p-2 w-full" required>
          <option value="shopify">Shopify</option>
          <option value="woocommerce">WooCommerce</option>
        </select>
      </label>

      <label class="block mb-2">Dominio
        <input name="domain" placeholder="my-shop.myshopify.com o tienda.com" class="border p-2 w-full" required>
      </label>

      <button class="bg-blue-600 text-white px-4 py-2">Guardar</button>
    </form>
  </div>
</x-app-layout>
