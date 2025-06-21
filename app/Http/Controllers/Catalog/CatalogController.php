<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Traits\BreadcrumbTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    use BreadcrumbTrait;

    public function index(Request $request, ?string $category=null): Response
    {
        $products = Product::with(['offers', 'categories', 'media']);

        if($category) 
        {
            if (!Category::whereCode($category)->exists()) abort(404);

            $products->whereRaw("id IN (SELECT product_id FROM product_categories WHERE category_id IN (
                WITH RECURSIVE CatsTree AS (
                    SELECT id, parent FROM categories WHERE id = (SELECT id FROM categories WHERE code = ?)
                    UNION ALL
                    SELECT c.id, c.parent FROM categories c
                    JOIN CatsTree ON CatsTree.id = c.parent
                 )
                 SELECT DISTINCT id FROM CatsTree
            ))", [$category]);
        }

        return Inertia::render('Catalog/Catalog', [
            'products'=>ProductResource::collection($products->orderBy('id', 'desc')->paginate(50)),
            'breadcrumb'=>$this->getBreadcrumb(cat:$category),
            'filters'=>[]
        ]);
    }

    public function notifyAboutAdmission(Request $request)
    {
        //
    }
}
