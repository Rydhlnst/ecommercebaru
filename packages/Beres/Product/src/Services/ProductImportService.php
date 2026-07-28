<?php

namespace Beres\Product\Services;

use Beres\Product\Models\ProductActivityLog;
use Illuminate\Support\Facades\Log;
use Webkul\Product\Models\Product;

class ProductImportService
{
    /**
     * Import products from CSV.
     */
    public function importFromCsv(string $filePath, int $userId): array
    {
        $results = [
            'total'     => 0,
            'success'   => 0,
            'failed'    => 0,
            'errors'    => [],
        ];

        try {
            $handle = fopen($filePath, 'r');
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                $results['total']++;
                $data = array_combine($headers, $row);

                try {
                    $this->processRow($data, $userId);
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $results['total'],
                        'error' => $e->getMessage(),
                    ];
                }
            }

            fclose($handle);
        } catch (\Exception $e) {
            Log::error('Product import failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        return $results;
    }

    /**
     * Process a single row.
     */
    protected function processRow(array $data, int $userId): void
    {
        $product = Product::where('sku', $data['sku'] ?? '')->first();

        if ($product) {
            $product->update([
                'name'  => $data['name'] ?? $product->name,
                'price' => $data['price'] ?? $product->price,
            ]);
        } else {
            Product::create([
                'sku'   => $data['sku'],
                'name'  => $data['name'],
                'price' => $data['price'],
                'status' => $data['status'] ?? 1,
            ]);
        }
    }

    /**
     * Export products to CSV.
     */
    public function exportToCsv(array $filters = []): string
    {
        $products = Product::query();

        if (!empty($filters['ids'])) {
            $products->whereIn('id', $filters['ids']);
        }

        $products = $products->get();

        $tempFile = tempnam(sys_get_temp_dir(), 'product_export_');
        $handle = fopen($tempFile, 'w');

        // Headers
        fputcsv($handle, ['id', 'sku', 'name', 'price', 'status', 'quantity']);

        foreach ($products as $product) {
            fputcsv($handle, [
                $product->id,
                $product->sku,
                $product->name,
                $product->price,
                $product->status,
                $product->totalQuantity(),
            ]);
        }

        fclose($handle);

        return $tempFile;
    }
}
