<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Catalog\StoreProductMediaRequest;
use App\Http\Requests\Admin\Catalog\RemoveMediaRequest;
use App\Http\Requests\Admin\Catalog\StoreProductMediaSortingRequest;
use App\Models\ProductMedia;
use App\Traits\MarketControllerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ProductMediaController extends Controller
{
    use MarketControllerTrait;

    /**
     * Сохранить медиа
     * 
     * @param App\Http\Requests\Admin\Catalog\StoreProductMediaRequest $request
     * @return void
     */
    public function storeProductMedia(StoreProductMediaRequest $request):void
    {
        $dir = 'images/'.(empty($request->product_id)?'offers':'products').'/'.(empty($request->product_id)?$request->offer_id:$request->product_id).'/';
        
        $validated = $request->validated();
        
        foreach($validated['files'] as $file)
        {
            if (!Storage::disk('public')->exists($dir)) Storage::disk('public')->makeDirectory($dir);

            $image = Image::read($file);
            $filename  = uniqid().'.'.$file->getClientOriginalExtension();
            $thumbname = 'thumb_'.$filename;
            $side = $image->width()<$image->height() ? $image->width() : $image->height();
            $image->crop($side, $side)->save(storage_path('app/public/'.$dir.$filename));
            $image->resize(350, 350)->save(storage_path('app/public/'.$dir.$thumbname));

            ProductMedia::create([
                'product_id'=>$validated['product_id'], 
                'offer_id'=>$validated['offer_id'], 
                'type'=>$file->getMimeType(),
                'path'=>$dir.$filename, 
                'preview'=>$dir.$thumbname
            ]);
        }
    }

    public function storeProductMediaSorting(StoreProductMediaSortingRequest $request)
    {   
        $validated = $request->validated();

        DB::transaction(function() use ($validated) 
        {
            foreach ($validated['files'] as $file) 
            {
                ProductMedia::whereId($file['id'])->update(['sort'=>$file['sort']]);
            }
        });
    }

    /**
     * Удалить медиа
     * 
     * @param App\Http\Requests\Admin\Catalog\RemoveMediaRequest $request
     * @return void
     */
    public function removeProductMedia(RemoveMediaRequest $request)
    {
        $file = ProductMedia::whereId($request->id)->first();

        Storage::disk('public')->delete([$file->path, $file->preview]);

        $file->delete();
    }
}
