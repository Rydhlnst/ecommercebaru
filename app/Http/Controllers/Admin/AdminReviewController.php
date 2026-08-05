<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminReview;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = AdminReview::with('product')->latest()->paginate(15);

        return view('admin.review.index', compact('reviews'));
    }

    public function approve(AdminReview $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil disetujui.');
    }

    public function destroy(AdminReview $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil dihapus.');
    }
}
