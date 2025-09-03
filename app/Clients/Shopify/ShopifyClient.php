<?php

namespace App\Clients\Shopify;

use App\Clients\Contracts\IStoreClient;
use App\Models\Store;
use App\Models\StoreCredential;
use App\Support\DTO\OrderDTO;
use App\Support\DTO\OrderLineDTO;
use App\Support\DTO\PageDTO;
use App\Support\DTO\ProductDTO;
use Illuminate\Support\Facades\Http;

final class ShopifyClient implements IStoreClient
{
    private ?Store $store = null;
    private ?StoreCredential $cred = null;

    public function forStore(Store $store): self
    {
        $this->store = $store;
        $this->cred = $store->credential; // access_token en credentials['access_token']
        return $this;
    }

    public function healthCheck(): bool
    {
        if (!$this->cred || empty($this->cred->credentials['access_token'])) {
            return false;
        }

        $domain = $this->store->domain;
        $resp = Http::withHeaders([
                'X-Shopify-Access-Token' => $this->cred->credentials['access_token'],
            ])
            ->timeout(config('services.http.timeout'))
            ->retry(2, 200)
            ->get("https://{$domain}/admin/api/2024-10/shop.json");

        return $resp->ok();
    }

    public function getProducts(array $filters = [], array $pagination = ['page' => 1, 'limit' => 50]): PageDTO
    {
        $domain = $this->store->domain;
        $limit = min(max((int)($pagination['limit'] ?? 50), 1), 250);

        $resp = Http::withHeaders([
                'X-Shopify-Access-Token' => $this->cred->credentials['access_token'] ?? '',
            ])
            ->timeout(config('services.http.timeout'))
            ->retry(2, 200)
            ->get("https://{$domain}/admin/api/2024-10/products.json", [
                'limit' => $limit,
                'page' => $pagination['page'] ?? 1,
            ]);

        $data = $resp->json('products', []);
        $items = [];
        foreach ($data as $p) {
            $variant = $p['variants'][0] ?? null;
            $price = $variant['price'] ?? 0.0;
            $sku = $variant['sku'] ?? null;
            $image = $p['image']['src'] ?? ($p['images'][0]['src'] ?? null);
            $items[] = new ProductDTO(
                externalId: (string)$p['id'],
                name: $p['title'] ?? '',
                sku: $sku ?: null,
                currency: 'USD',
                price: (float)$price,
                imageUrl: $image,
            );
        }

        return new PageDTO(items: $items, page: (int)($pagination['page'] ?? 1), limit: $limit);
    }

    public function getOrders(\DateTimeInterface $from, \DateTimeInterface $to, array $pagination = ['page' => 1, 'limit' => 50]): PageDTO
    {
        $domain = $this->store->domain;
        $limit = min(max((int)($pagination['limit'] ?? 50), 1), 250);

        $resp = Http::withHeaders([
                'X-Shopify-Access-Token' => $this->cred->credentials['access_token'] ?? '',
            ])
            ->timeout(config('services.http.timeout'))
            ->retry(2, 200)
            ->get("https://{$domain}/admin/api/2024-10/orders.json", [
                'status' => 'any',
                'limit' => $limit,
                'created_at_min' => $from->format(DATE_ATOM),
                'created_at_max' => $to->format(DATE_ATOM),
            ]);

        $data = $resp->json('orders', []);
        $items = [];
        foreach ($data as $o) {
            $lines = [];
            foreach ($o['line_items'] ?? [] as $li) {
                $lines[] = new OrderLineDTO(
                    productExternalId: (string)($li['product_id'] ?? ''),
                    name: $li['name'] ?? '',
                    sku: $li['sku'] ?? null,
                    quantity: (int)($li['quantity'] ?? 0),
                    currency: $o['currency'] ?? 'USD',
                    unitPrice: (float)($li['price'] ?? 0),
                    total: (float)($li['price'] ?? 0) * (int)($li['quantity'] ?? 0),
                );
            }

            $items[] = new OrderDTO(
                externalId: (string)$o['id'],
                number: $o['name'] ?? (string)$o['id'],
                status: $o['financial_status'] ?? ($o['fulfillment_status'] ?? 'unknown'),
                customerName: trim(($o['customer']['first_name'] ?? '').' '.($o['customer']['last_name'] ?? '')),
                customerEmail: $o['contact_email'] ?? ($o['email'] ?? ''),
                createdAt: new \DateTimeImmutable($o['created_at'] ?? 'now'),
                lines: $lines,
                currency: $o['currency'] ?? 'USD',
                total: (float)($o['total_price'] ?? 0),
            );
        }

        return new PageDTO(items: $items, page: (int)($pagination['page'] ?? 1), limit: $limit);
    }
}
