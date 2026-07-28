<?php

namespace Beres\Shipping\DTOs;

class ShippingCostDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $service,
        public readonly string $description,
        public readonly float $cost,
        public readonly int $etd,
        public readonly string $etdNote,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'] ?? '',
            name: $data['name'] ?? '',
            service: $data['service'] ?? '',
            description: $data['description'] ?? '',
            cost: (float) ($data['cost'][0]['value'] ?? 0),
            etd: (int) ($data['cost'][0]['etd'] ?? 0),
            etdNote: $data['cost'][0]['note'] ?? '',
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'code'       => $this->code,
            'name'       => $this->name,
            'service'    => $this->service,
            'description' => $this->description,
            'cost'       => $this->cost,
            'etd'        => $this->etd,
            'etd_note'   => $this->etdNote,
        ];
    }

    /**
     * Format cost as currency.
     */
    public function formattedCost(): string
    {
        return 'Rp ' . number_format($this->cost, 0, ',', '.');
    }

    /**
     * Format ETD.
     */
    public function formattedEtd(): string
    {
        return "{$this->etd} hari";
    }
}
