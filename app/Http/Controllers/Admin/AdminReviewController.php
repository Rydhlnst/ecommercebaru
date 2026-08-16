<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminReview;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = new LengthAwarePaginator(collect(), 0, 15, 1);

        try {
            if (Schema::hasTable('admin_reviews')) {
                $reviews = AdminReview::with('product')->latest()->paginate(15);
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.review.index', compact('reviews'));
    }

    public function approve($id)
    {
        $review = AdminReview::findOrFail($id);
        $review->update(['is_approved' => true]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil disetujui.');
    }

    public function destroy($id)
    {
        $review = AdminReview::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil dihapus.');
    }
}
