<?php

namespace App\Http\Controllers;

use Domain\Catalog\Models\Category;
use Domain\Product\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    public function __invoke(?Category $category): Application|Factory|View
    {
        $categories = Category::query()
            ->select('id', 'title', 'slug')
            ->has('products')
            ->get();

        $products = Product::query()
            ->select('id', 'title', 'slug', 'price', 'thumbnail', 'json_properties')
            ->search()
            ->withCategory($category)
            ->filtered()
            ->sorted()
            ->paginate(6);

        return view(
            'catalog.index',
            compact([
                'categories',
                'products',
                'category'
            ])
        );
    }
}
