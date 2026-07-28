<?php

namespace Beres\Customer\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Customer\Services\CustomerService;
use Webkul\Customer\Models\Customer;
use Illuminate\Support\Facades\Response;

class CustomerApiController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * Get customers list.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'name', 'email', 'status', 'group_id',
            'sort_by', 'sort_order', 'per_page',
        ]);

        $customers = $this->customerService->search($filters);

        return response()->json([
            'success' => true,
            'data'    => $customers,
        ]);
    }

    /**
     * Get customer detail.
     */
    public function show($id)
    {
        $customer = Customer::with(['group', 'addresses', 'orders'])->findOrFail($id);
        $customerDto = $this->customerService->getCustomerDto($customer);
        $stats = $this->customerService->getCustomerStats($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'customer' => $customerDto->toArray(),
                'stats'    => $stats->toArray(),
            ],
        ]);
    }

    /**
     * Get customer activity log.
     */
    public function activityLog($id)
    {
        $activityLog = $this->customerService->getActivityLog($id);

        return response()->json([
            'success' => true,
            'data'    => $activityLog,
        ]);
    }

    /**
     * Get customer notes.
     */
    public function notes($id)
    {
        $notes = $this->customerService->getNotes($id);

        return response()->json([
            'success' => true,
            'data'    => $notes,
        ]);
    }
}
