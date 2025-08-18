<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = User::where('role', 'seller')->get();
        return view('admin.sellers', compact('sellers'));
    }
}
