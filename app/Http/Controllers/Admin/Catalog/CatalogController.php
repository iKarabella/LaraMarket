<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\DeleteCatRequest;
use App\Http\Requests\Admin\Catalog\SetCatSortRequest;
use App\Http\Requests\Admin\Catalog\StoreCatRequest;
use App\Models\Category;
use App\Models\Product;
use App\Traits\MarketControllerTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    use MarketControllerTrait;

    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Catalog/Manage', [
            'categories'=>Category::all(),
            'navigation'=>$this->getNavigation('categories'),
        ]);
    }

    public function storeCat(StoreCatRequest $request)
    {
        $validated = $request->validated();

        Category::firstOrNew(['id'=>$validated['id']])->fill($validated)->save();
    }

    public function deleteCat(DeleteCatRequest $request)
    {
        Category::whereId($request->id)->delete();
    }

    public function sortingCategory(SetCatSortRequest $request)
    {
        Category::whereId($request->id)->update(['sort' => $request->sort]);
    }

    public function products(Request $request, $link=null)
    {
        if ($link!=null) $product=Product::whereLink($link)->with(['categories', 'offers'])->firstOrFail();
        else if ($request->id) $product=Product::whereId($request->id)->with(['categories', 'offers'])->firstOrFail();
        else
        {
            $product=collect([
                'id'=>null,
                'title'=>'',
                'description'=>'',
                'visibility'=>false,
                'categories'=>[],
                'created_at'=>null,
                'updated_at'=>null,
            ]);
            
            if ($request->category) {
                $product['categories'] = Category::whereId($request->category)->first();
            }
        }
        
        return Inertia::render('Admin/Catalog/EditProduct', [
            'status' => session('status'),
            'product'=>$product->toArray(),
            'categories'=>Category::all(),
        ]);
    }
}
