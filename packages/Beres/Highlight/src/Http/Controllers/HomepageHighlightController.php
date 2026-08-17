<?php

namespace Beres\Highlight\Http\Controllers;

use Beres\Highlight\Models\HomepageHighlight;
use Beres\Highlight\Repositories\HomepageHighlightRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\ResponseCache\Facades\ResponseCache;
use Webkul\Category\Models\Category;
use Webkul\Product\Models\Product;

class HomepageHighlightController extends Controller
{
    public function __construct(
        protected HomepageHighlightRepository $repository
    ) {}

    /**
     * Render the Homepage Manager page.
     */
    public function index()
    {
        $sections = HomepageHighlight::getSectionDefinitions();

        $data = [];

        foreach ($sections as $key => $meta) {
            $data[$key] = [
                'meta' => $meta,
                'highlights' => $this->repository->getBySection($key),
                'count' => $this->repository->countActive($key),
            ];
        }

        return view('beres-highlight::index', [
            'sections' => $data,
        ]);
    }

    /**
     * Pin a product or category to a section.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section' => ['required', 'string'],
            'entity_type' => ['required', 'in:product,category'],
            'entity_id' => ['required', 'integer'],
        ]);

        $meta = HomepageHighlight::getSectionDefinitions()[$validated['section']] ?? null;

        if (! $meta || $meta['type'] !== $validated['entity_type']) {
            return response()->json([
                'success' => false,
                'message_key' => 'type_mismatch',
            ], 422);
        }

        if ($this->repository->exists($validated['section'], $validated['entity_type'], $validated['entity_id'])) {
            return response()->json([
                'success' => false,
                'message_key' => 'already_pinned',
            ], 409);
        }

        $count = $this->repository->countActive($validated['section']);

        if ($count >= $meta['limit']) {
            return response()->json([
                'success' => false,
                'message_key' => 'section_full',
            ], 422);
        }

        $highlight = $this->repository->add(
            $validated['section'],
            $validated['entity_type'],
            $validated['entity_id']
        );

        $entity = $highlight->entity_type === HomepageHighlight::TYPE_PRODUCT
            ? $highlight->product
            : $highlight->category;

        ResponseCache::clear();

        return response()->json([
            'success' => true,
            'message_key' => 'pin_success',
            'highlight' => [
                'id' => $highlight->id,
                'entity_id' => $highlight->entity_id,
                'name' => $entity?->name ?? '—',
                'sku' => $entity?->sku ?? null,
                'slug' => $entity?->slug ?? null,
            ],
        ]);
    }

    /**
     * Unpin a highlight.
     */
    public function destroy(int $id)
    {
        $highlight = HomepageHighlight::findOrFail($id);

        $highlight->delete();
        ResponseCache::clear();

        return response()->json([
            'success' => true,
            'message_key' => 'unpin_success',
        ]);
    }

    /**
     * Reorder highlights within a section.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'section' => ['required', 'string'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $this->repository->reorder($validated['section'], $validated['ids']);

        ResponseCache::clear();

        return response()->json([
            'success' => true,
            'message_key' => 'reorder_success',
        ]);
    }

    /**
     * Search products or categories for the pin picker.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:1'],
            'entity_type' => ['required', 'in:product,category'],
            'section' => ['required', 'string'],
        ]);

        $excludeIds = $this->repository->getActiveBySection($validated['section'])
            ->pluck('entity_id')
            ->all();

        if ($validated['entity_type'] === HomepageHighlight::TYPE_PRODUCT) {
            $results = Product::query()
                ->whereDoesntHave('parent')
                ->where(function ($q) use ($validated) {
                    $q->where('name', 'like', "%{$validated['q']}%")
                        ->orWhere('sku', 'like', "%{$validated['q']}%");
                })
                ->when(! empty($excludeIds), fn ($q) => $q->whereNotIn('id', $excludeIds))
                ->limit(10)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'price' => core()->currency($p->getTypeInstance()->getMinimalPrice() ?? 0),
                ]);
        } else {
            $results = Category::query()
                ->where('name', 'like', "%{$validated['q']}%")
                ->when(! empty($excludeIds), fn ($q) => $q->whereNotIn('id', $excludeIds))
                ->limit(10)
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                ]);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}
