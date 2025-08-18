<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the seller's assigned products.
     */
    public function index()
    {
        $products = auth()->user()->assignedProducts()
            ->with(['category', 'seller'])
            ->get();

        return view('seller.products.index', compact('products'));
    }
}
