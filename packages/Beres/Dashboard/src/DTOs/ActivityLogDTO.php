<?php

namespace Beres\Dashboard\DTOs;

class ActivityLogDTO
{
    public function __construct(
        public readonly ?int $userId,
        public readonly string $userType,
        public readonly string $action,
        public readonly ?string $subjectType,
        public readonly ?int $subjectId,
        public readonly ?string $description,
        public readonly ?array $properties,
        public readonly ?string $ipAddress,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            userType: $data['user_type'] ?? 'admin',
            action: $data['action'],
            subjectType: $data['subject_type'] ?? null,
            subjectId: $data['subject_id'] ?? null,
            description: $data['description'] ?? null,
            properties: $data['properties'] ?? null,
            ipAddress: $data['ip_address'] ?? null,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'user_id'       => $this->userId,
            'user_type'     => $this->userType,
            'action'        => $this->action,
            'subject_type'  => $this->subjectType,
            'subject_id'    => $this->subjectId,
            'description'   => $this->description,
            'properties'    => $this->properties,
            'ip_address'    => $this->ipAddress,
        ];
    }
}
