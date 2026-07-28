<?php

namespace Beres\Shipping\DTOs;

class ShippingServiceDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly array $services,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        $services = [];
        if (isset($data['costs'])) {
            foreach ($data['costs'] as $cost) {
                $services[] = ShippingCostDTO::fromArray($cost);
            }
        }

        return new self(
            code: $data['code'] ?? '',
            name: $data['name'] ?? '',
            services: $services,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'code'     => $this->code,
            'name'     => $this->name,
            'services' => array_map(fn($s) => $s->toArray(), $this->services),
        ];
    }
}
