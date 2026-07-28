<?php

namespace Beres\Customer\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Customer\Services\CustomerService;
use Webkul\Customer\Models\Customer;
use Illuminate\Support\Facades\Response;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * Display customer listing.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'name', 'email', 'status', 'group_id',
            'sort_by', 'sort_order', 'per_page',
        ]);

        $customers = $this->customerService->search($filters);

        return view('beres-customer::customers.index', [
            'customers' => $customers,
            'filters'   => $filters,
        ]);
    }

    /**
     * Display customer detail.
     */
    public function show($id)
    {
        $customer = Customer::with(['group', 'addresses', 'orders'])->findOrFail($id);
        $customerDto = $this->customerService->getCustomerDto($customer);
        $stats = $this->customerService->getCustomerStats($id);
        $activityLog = $this->customerService->getActivityLog($id);
        $notes = $this->customerService->getNotes($id);

        return view('beres-customer::customers.show', [
            'customer'     => $customerDto,
            'stats'        => $stats,
            'activityLog'  => $activityLog,
            'notes'        => $notes,
        ]);
    }

    /**
     * Add customer note.
     */
    public function addNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $note = $this->customerService->addNote($id, $request->input('note'));

        return response()->json([
            'success' => true,
            'data'    => $note,
        ]);
    }

    /**
     * Update customer note.
     */
    public function updateNote(Request $request, $customerId, $noteId)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $result = $this->customerService->updateNote($noteId, $request->input('note'));

        return response()->json([
            'success' => $result,
        ]);
    }

    /**
     * Delete customer note.
     */
    public function deleteNote($customerId, $noteId)
    {
        $result = $this->customerService->deleteNote($noteId);

        return response()->json([
            'success' => $result,
        ]);
    }

    /**
     * Export customers to CSV.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['ids']);
        $filePath = $this->customerService->exportToCsv($filters);

        return response()->download($filePath, 'customers_export_' . date('Y-m-d_His') . '.csv', [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
