<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:user']);
    }
    public function index()
    {
        return view('user.userhome');
    }
    public function products(Request $request)
    {
        $products = Product::select('products.id as pid', 'products.name', 'images.url', 'products.sku')
        ->leftJoin('prices', 'products.id', '=', 'prices.product_id')
        ->leftJoin('images', 'products.id', '=', 'images.product_id')
        ->whereNull('products.deleted_at')
        ->groupBy('products.id', 'products.name', 'images.url', 'products.sku')
        ->get();
        return view('user.store', compact('products'));
    }

}
