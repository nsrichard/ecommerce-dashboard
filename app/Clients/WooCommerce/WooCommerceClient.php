<?php

namespace App\Clients\WooCommerce;

use App\Clients\Contracts\IStoreClient;
use App\Models\Store;
use App\Models\StoreCredential;
use App\Support\DTO\OrderDTO;
use App\Support\DTO\OrderLineDTO;
use App\Support\DTO\PageDTO;
use App\Support\DTO\ProductDTO;
use Illuminate\Support\Facades\Http;

final class WooCommerceClient implements IStoreClient
{
    private ?Store $store = null;
    private ?StoreCredential $cred = null;

    public function forStore(Store $store): self
    {
        $this->store = $store;
        $this->cred = $store->credential;
        return $this;
    }

    private function base(): string
    {
        $base = $this->cred->credentials['base_url'] ?? '';
        return rtrim($base, '/').'/wp-json/wc/v3';
    }

    private function authParams(): array
    {
        return [
            'consumer_key' => $this->cred->credentials['key'] ?? '',
            'consumer_secret' => $this->cred->credentials['secret'] ?? '',
        ];
    }

    public function healthCheck(): bool
    {
        if (!$this->cred || empty($this->cred->credentials['key'])) {
            return false;
        }
        $resp = Http::timeout(config('services.http.timeout'))
            ->retry(2, 200)
            ->get($this->base().'/system_status', $this->authParams());

        return $resp->ok();
    }

    public function getProducts(array $filters = [], array $pagination = ['page' => 1, 'limit' => 50]): PageDTO
    {
        $page = (int)($pagination['page'] ?? 1);
        $limit = min(max((int)($pagination['limit'] ?? 50), 1), 100);

        $resp = Http::timeout(config('services.http.timeout'))
            ->retry(2, 200)
            ->get($this->base().'/products', array_merge($this->authParams(), [
                'page' => $page,
                'per_page' => $limit,
            ]));

        $items = [];
        foreach ($resp->json() ?? [] as $p) {
            $price = (float)($p['price'] ?? 0);
            $currency = $p['currency'] ?? 'USD';
            $image = $p['images'][0]['src'] ?? null;

            $items[] = new ProductDTO(
                externalId: (string)$p['id'],
                name: $p['name'] ?? '',
                sku: $p['sku'] ?: null,
                currency: $currency,
                price: $price,
                imageUrl: $image,
            );
        }

        return new PageDTO(items: $items, page: $page, limit: $limit);
    }

    public function getOrders(\DateTimeInterface $from, \DateTimeInterface $to, array $pagination = ['page' => 1, 'limit' => 50]): PageDTO
    {
        $page = (int)($pagination['page'] ?? 1);
        $limit = min(max((int)($pagination['limit'] ?? 50), 1), 100);

        $resp = Http::timeout(config('services.http.timeout'))
            ->retry(2, 200)
            ->get($this->base().'/orders', array_merge($this->authParams(), [
                'page' => $page,
                'per_page' => $limit,
                'after' => $from->format(DATE_ATOM),
                'before' => $to->format(DATE_ATOM),
            ]));

        $items = [];
        foreach ($resp->json() ?? [] as $o) {
            $lines = [];
            foreach ($o['line_items'] ?? [] as $li) {
                $lines[] = new OrderLineDTO(
                    productExternalId: (string)($li['product_id'] ?? ''),
                    name: $li['name'] ?? '',
                    sku: $li['sku'] ?? null,
                    quantity: (int)($li['quantity'] ?? 0),
                    currency: $o['currency'] ?? 'USD',
                    unitPrice: (float)($li['price'] ?? 0),
                    total: (float)($li['total'] ?? 0),
                );
            }

            $customerName = trim(($o['billing']['first_name'] ?? '').' '.($o['billing']['last_name'] ?? ''));

            $items[] = new OrderDTO(
                externalId: (string)$o['id'],
                number: (string)($o['number'] ?? $o['id']),
                status: $o['status'] ?? 'unknown',
                customerName: $customerName,
                customerEmail: $o['billing']['email'] ?? '',
                createdAt: new \DateTimeImmutable($o['date_created'] ?? 'now'),
                lines: $lines,
                currency: $o['currency'] ?? 'USD',
                total: (float)($o['total'] ?? 0),
            );
        }

        return new PageDTO(items: $items, page: $page, limit: $limit);
    }
}
