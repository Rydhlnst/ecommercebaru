<?php

namespace Beres\Customer\Services;

use Beres\Customer\Contracts\CustomerActivityLogRepositoryInterface;
use Beres\Customer\Contracts\CustomerNoteRepositoryInterface;
use Beres\Customer\DTOs\CustomerDTO;
use Beres\Customer\DTOs\CustomerStatsDTO;
use Beres\Customer\Models\CustomerActivityLog;
use Webkul\Customer\Models\Customer;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function __construct(
        protected CustomerActivityLogRepositoryInterface $activityLogRepository,
        protected CustomerNoteRepositoryInterface $noteRepository
    ) {}

    /**
     * Get customer as DTO.
     */
    public function getCustomerDto(Customer $customer): CustomerDTO
    {
        return CustomerDTO::fromArray([
            'id'            => $customer->id,
            'first_name'    => $customer->first_name,
            'last_name'     => $customer->last_name,
            'email'         => $customer->email,
            'phone'         => $customer->phone,
            'status'        => $customer->status,
            'group'         => $customer->group->name ?? null,
            'orders_count'  => $customer->orders()->count(),
            'total_spent'   => $customer->orders()->sum('grand_total'),
            'addresses'     => $customer->addresses->toArray(),
            'created_at'    => $customer->created_at,
        ]);
    }

    /**
     * Get customer statistics.
     */
    public function getCustomerStats(int $customerId): CustomerStatsDTO
    {
        $customer = Customer::find($customerId);

        $orders = $customer->orders();
        $totalOrders = $orders->count();
        $totalSpent = (float) $orders->sum('grand_total');
        $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
        $lastOrderDate = $orders->latest()->value('created_at');

        return CustomerStatsDTO::fromArray([
            'customer_id'         => $customerId,
            'total_orders'        => $totalOrders,
            'total_spent'         => $totalSpent,
            'average_order_value' => $averageOrderValue,
            'last_order_date'     => $lastOrderDate,
            'wishlist_count'      => $customer->wishlist_items()->count(),
            'review_count'        => $customer->reviews()->count(),
        ]);
    }

    /**
     * Add customer note.
     */
    public function addNote(int $customerId, string $note, bool $isInternal = true): object
    {
        $adminId = auth()->guard('admin')->id();

        return $this->noteRepository->create([
            'customer_id' => $customerId,
            'admin_id'    => $adminId,
            'note'        => $note,
            'is_internal' => $isInternal,
        ]);
    }

    /**
     * Get customer notes.
     */
    public function getNotes(int $customerId, int $limit = 50): array
    {
        return $this->noteRepository->getByCustomer($customerId, $limit);
    }

    /**
     * Update customer note.
     */
    public function updateNote(int $noteId, string $note): bool
    {
        return $this->noteRepository->update($noteId, ['note' => $note]);
    }

    /**
     * Delete customer note.
     */
    public function deleteNote(int $noteId): bool
    {
        return $this->noteRepository->delete($noteId);
    }

    /**
     * Get customer activity log.
     */
    public function getActivityLog(int $customerId, int $limit = 50): array
    {
        return $this->activityLogRepository->getByCustomer($customerId, $limit);
    }

    /**
     * Search customers.
     */
    public function search(array $filters): array
    {
        $query = Customer::with(['group', 'addresses']);

        if (!empty($filters['name'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'LIKE', "%{$filters['name']}%")
                  ->orWhere('last_name', 'LIKE', "%{$filters['name']}%");
            });
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'LIKE', "%{$filters['email']}%");
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page'])->toArray();
        }

        return $query->get()->toArray();
    }

    /**
     * Export customers to CSV.
     */
    public function exportToCsv(array $filters = []): string
    {
        $customers = Customer::query();

        if (!empty($filters['ids'])) {
            $customers->whereIn('id', $filters['ids']);
        }

        $customers = $customers->get();

        $tempFile = tempnam(sys_get_temp_dir(), 'customer_export_');
        $handle = fopen($tempFile, 'w');

        // Headers
        fputcsv($handle, ['id', 'first_name', 'last_name', 'email', 'phone', 'status', 'created_at']);

        foreach ($customers as $customer) {
            fputcsv($handle, [
                $customer->id,
                $customer->first_name,
                $customer->last_name,
                $customer->email,
                $customer->phone,
                $customer->status ? 'Active' : 'Inactive',
                $customer->created_at,
            ]);
        }

        fclose($handle);

        return $tempFile;
    }
}
