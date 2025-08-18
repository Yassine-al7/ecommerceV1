<?php

namespace App\Http\Controllers\Admin;

use App\Models\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::all();
        return view('admin.sellers', compact('sellers'));
    }

    public function create()
    {
        return view('admin.sellers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:sellers',
            'phone' => 'required|string|max:15',
        ]);

        Seller::create($request->all());
        return redirect()->route('admin.sellers.index')->with('success', 'Seller created successfully.');
    }

    public function edit(Seller $seller)
    {
        return view('admin.sellers.edit', compact('seller'));
    }

    public function update(Request $request, Seller $seller)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:sellers,email,' . $seller->id,
            'phone' => 'required|string|max:15',
        ]);

        $seller->update($request->all());
        return redirect()->route('admin.sellers.index')->with('success', 'Seller updated successfully.');
    }

    public function destroy(Seller $seller)
    {
        $seller->delete();
        return redirect()->route('admin.sellers.index')->with('success', 'Seller deleted successfully.');
    }
}
