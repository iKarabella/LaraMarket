<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\DeleteCatRequest;
use App\Http\Requests\Admin\Catalog\ManageRequest;
use App\Http\Requests\Admin\Catalog\SetCatSortRequest;
use App\Http\Requests\Admin\Catalog\StoreCatRequest;
use App\Http\Requests\Admin\Catalog\StoreOfferRequest;
use App\Http\Requests\Admin\Catalog\StoreProductRequest;
use App\Http\Resources\Admin\Catalog\OfferResource;
use App\Http\Resources\Admin\Catalog\ProductResource;
use App\Models\Category;
use App\Models\EntityValue;
use App\Models\Offer;
use App\Models\Product;
use App\Traits\MarketControllerTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    use MarketControllerTrait;

    public function index(ManageRequest $request): Response
    {
        $products = Product::with(['offers', 'categories', 'offers', 'media']);

        if($request->category) {
            $request->session()->put('catalogManage.filters.category', $request->category);
            $products->whereRaw("id IN (SELECT product_id FROM product_categories WHERE category_id = ?)", $request->category);
        }

        return Inertia::render('Admin/Catalog/Manage', [
            'categories'=>Category::all(),
            'navigation'=>$this->getNavigation('categories'),
            'products'=>ProductResource::collection($products->orderBy('id', 'desc')->paginate(50)),
            'filters'=>['category'=>$request->session()->get('catalogManage.filters.category', null)]
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

    public function products(Request $request, $link=null): Response
    {
        if ($link!=null) $product=Product::whereLink($link)->with(['categories', 'offers', 'media'])->firstOrFail();
        else
        {
            $product=[
                'id'=>null,
                'title'=>'',
                'description'=>'',
                'visibility'=>false,
                'categories'=>[],
                'created_at'=>null,
                'updated_at'=>null,
            ];

            if ($request->category) {
                $product['categories'][]=Category::whereId($request->category)->first();
            }
            $product=collect($product);
        }
        
        return Inertia::render('Admin/Catalog/EditProduct', [
            'product'=>$product->toArray(),
            'categories'=>Category::all(),
            'navigation'=>$this->getNavigation('categories'),
            'measures'=>EntityValue::whereEntity(1)->get()->toArray()
        ]);
    }

    public function storeProduct(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::whereId($request->id)->firstOrNew();

        $product->fill($request->validated())->save();
        $product->categories()->sync(collect($request->categories)->pluck('id'));

        return redirect()->route('admin.products.edit', [$product->link]);
    }

    public function offer(Request $request, $link, $offer_id=null):Response
    {
        $product=Product::whereLink($link)->with(['categories'])->firstOrFail();

        if ($offer_id) $offer = Offer::whereId($offer_id)->firstOrFail();
        else $offer= (object) [
            'id'=>null,
            'product_id'=>$product->id,
            'title'=>'',
            'baseprice'=>'0.00',
            'price'=>'0.00',
            'barcode'=>'',
            'art'=>'',
            'media'=>[],
            'visibility'=>true
        ];

        return Inertia::render('Admin/Catalog/EditOffer', [
            'product'=>ProductResource::make($product)->resolve(),
            'offer'=>OfferResource::make($offer)->resolve(),
            'navigation'=>$this->getNavigation('categories'),
        ]);
    }

    public function storeOffer(StoreOfferRequest $request):RedirectResponse
    {
        $product = Product::whereId($request->product_id)->first(['link']);
        $offer = Offer::whereId($request->id)->firstOrNew();
        $offer->fill($request->validated())->save();
        return redirect()->route('admin.products.edit', [$product->link]);
    }
}
