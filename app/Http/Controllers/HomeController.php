<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'featuredBooks' => Book::active()->featured()->inStock()->latest()->limit(8)->get(),
            'newBooks' => Book::active()->latest()->limit(8)->get(),
            'bestSellers' => Book::active()->orderByDesc('sales_count')->limit(8)->get(),
            'categories' => Category::active()->whereNull('parent_id')->orderBy('name')->limit(6)->get(),
        ]);
    }
}
