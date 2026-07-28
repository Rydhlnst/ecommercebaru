<?php

namespace Beres\Inventory\DTOs;

class StockHistoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $productId,
        public readonly string $productName,
        public readonly ?string $inventorySourceName,
        public readonly string $action,
        public readonly int $quantity,
        public readonly int $oldQuantity,
        public readonly int $newQuantity,
        public readonly ?string $note,
        public readonly ?string $userName,
        public readonly ?string $createdAt,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            productId: $data['product_id'],
            productName: $data['product']['name'] ?? 'Unknown',
            inventorySourceName: $data['inventory_source']['name'] ?? null,
            action: $data['action'],
            quantity: $data['quantity'],
            oldQuantity: $data['old_quantity'],
            newQuantity: $data['new_quantity'],
            note: $data['note'] ?? null,
            userName: $data['user']['name'] ?? null,
            createdAt: $data['created_at'] ?? null,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'id'                    => $this->id,
            'product_id'            => $this->productId,
            'product_name'          => $this->productName,
            'inventory_source_name' => $this->inventorySourceName,
            'action'                => $this->action,
            'quantity'              => $this->quantity,
            'old_quantity'          => $this->oldQuantity,
            'new_quantity'          => $this->newQuantity,
            'note'                  => $this->note,
            'user_name'             => $this->userName,
            'created_at'            => $this->createdAt,
        ];
    }
}
