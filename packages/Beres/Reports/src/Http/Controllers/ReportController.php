<?php

namespace Beres\Reports\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Reports\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Display reports index.
     */
    public function index()
    {
        return view('beres-reports::reports.index');
    }

    /**
     * Display revenue report.
     */
    public function revenue(Request $request)
    {
        $report = $this->reportService->getRevenueReport(
            $request->input('period', 'monthly'),
            $request->input('start_date'),
            $request->input('end_date')
        );

        return view('beres-reports::reports.revenue', [
            'report' => $report,
            'filters' => $request->only(['period', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Display orders report.
     */
    public function orders(Request $request)
    {
        $report = $this->reportService->getOrdersReport(
            $request->input('start_date'),
            $request->input('end_date')
        );

        return view('beres-reports::reports.orders', [
            'report' => $report,
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }

    /**
     * Display products report.
     */
    public function products(Request $request)
    {
        $report = $this->reportService->getProductsReport(
            $request->input('start_date'),
            $request->input('end_date')
        );

        return view('beres-reports::reports.products', [
            'report' => $report,
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }

    /**
     * Display customers report.
     */
    public function customers(Request $request)
    {
        $report = $this->reportService->getCustomersReport(
            $request->input('start_date'),
            $request->input('end_date')
        );

        return view('beres-reports::reports.customers', [
            'report' => $report,
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }

    /**
     * Export report to CSV.
     */
    public function export(Request $request, $type)
    {
        $report = match ($type) {
            'revenue'  => $this->reportService->getRevenueReport(
                $request->input('period', 'monthly'),
                $request->input('start_date'),
                $request->input('end_date')
            )['data'],
            'orders'   => $this->reportService->getOrdersReport(
                $request->input('start_date'),
                $request->input('end_date')
            )['daily_orders'],
            'products' => $this->reportService->getProductsReport(
                $request->input('start_date'),
                $request->input('end_date')
            )['top_selling'],
            'customers' => $this->reportService->getCustomersReport(
                $request->input('start_date'),
                $request->input('end_date')
            )['top_spenders'],
            default => [],
        };

        $filePath = $this->reportService->exportToCsv($report, $type);

        return response()->download($filePath, $type . '_report_' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
