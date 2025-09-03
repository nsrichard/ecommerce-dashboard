<?php

namespace Database\Factories;

use App\Domain\Enums\Platform;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        $platform = $this->faker->randomElement([Platform::SHOPIFY->value, Platform::WOOCOMMERCE->value]);

        return [
            'name' => $this->faker->company(),
            'platform' => $platform,
            'domain' => $platform === Platform::SHOPIFY->value
                ? $this->faker->unique()->domainWord().'.myshopify.com'
                : 'tienda-'.$this->faker->unique()->domainWord().'.com',
            'status' => 'disconnected',
        ];
    }

    public function shopify(): self
    {
        return $this->state(fn () => [
            'platform' => Platform::SHOPIFY->value,
            'domain' => $this->faker->unique()->domainWord().'.myshopify.com',
        ]);
    }

    public function woocommerce(): self
    {
        return $this->state(fn () => [
            'platform' => Platform::WOOCOMMERCE->value,
            'domain' => 'tienda-'.$this->faker->unique()->domainWord().'.com',
        ]);
    }
}
