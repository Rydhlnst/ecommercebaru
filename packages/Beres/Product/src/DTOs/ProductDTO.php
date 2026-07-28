<?php

namespace Beres\Product\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $sku,
        public readonly float $price,
        public readonly ?float $specialPrice,
        public readonly int $quantity,
        public readonly string $status,
        public readonly string $visibility,
        public readonly ?string $description,
        public readonly ?string $metaTitle,
        public readonly ?string $metaDescription,
        public readonly array $categories,
        public readonly array $images,
        public readonly array $attributeValues,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            sku: $data['sku'],
            price: (float) $data['price'],
            specialPrice: isset($data['special_price']) ? (float) $data['special_price'] : null,
            quantity: (int) $data['quantity'],
            status: $data['status'],
            visibility: $data['visibility'],
            description: $data['description'] ?? null,
            metaTitle: $data['meta_title'] ?? null,
            metaDescription: $data['meta_description'] ?? null,
            categories: $data['categories'] ?? [],
            images: $data['images'] ?? [],
            attributeValues: $data['attribute_values'] ?? [],
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'sku'               => $this->sku,
            'price'             => $this->price,
            'special_price'     => $this->specialPrice,
            'quantity'          => $this->quantity,
            'status'            => $this->status,
            'visibility'        => $this->visibility,
            'description'       => $this->description,
            'meta_title'        => $this->metaTitle,
            'meta_description'  => $this->metaDescription,
            'categories'        => $this->categories,
            'images'            => $this->images,
            'attribute_values'  => $this->attributeValues,
        ];
    }

    /**
     * Check if product is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Get effective price.
     */
    public function getEffectivePrice(): float
    {
        return $this->specialPrice ?? $this->price;
    }
}
