<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Products24hKickController extends Controller
{
    public function index(Request $request)
    {
        Product::query()->where('updated_at', '>', Carbon::now()->subDay())
            ->each(function ($product) {
                $product->attachTag('Not Synced');
            });
    }
}
