<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Conectar WooCommerce — {{ $store->name }}</h2>
  </x-slot>

  <div class="py-6 max-w-lg mx-auto">
    <form method="POST" action="{{ route('stores.connect.woocommerce.store', $store) }}">
      @csrf

      <label class="block mb-4">
        <span class="text-gray-700">Base URL</span>
        <input type="url" name="base_url" 
               value="{{ old('base_url') }}" 
               class="mt-1 block w-full border rounded p-2" required>
        @error('base_url')
          <p class="text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </label>

      <label class="block mb-4">
        <span class="text-gray-700">Consumer Key</span>
        <input type="text" name="key" 
               value="{{ old('key') }}" 
               class="mt-1 block w-full border rounded p-2" required>
        @error('key')
          <p class="text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </label>

      <label class="block mb-4">
        <span class="text-gray-700">Consumer Secret</span>
        <input type="text" name="secret" 
               value="{{ old('secret') }}" 
               class="mt-1 block w-full border rounded p-2" required>
        @error('secret')
          <p class="text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </label>

      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
        Conectar a WooCommerce
      </button>
    </form>
  </div>
</x-app-layout>
