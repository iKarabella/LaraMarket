<?php

namespace App\Console\Commands\Manticore;

use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use App\Services\ManticoreSearch\ManticoreService;
use Illuminate\Console\Command;

class TableIndexing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Manticore:indexing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Индексировать таблицы в ManticoreSearch';

    private $manticore;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $this->info("Ин");
        $this->warn("Индексация таблиц в manticoresearch");

        if(!env('MANTICORE_MYSQL_HOST', false) || !env('MANTICORE_MYSQL_PORT', false))
        {
            $this->error("Нет данных для подключения к серверу Manticoresearch");
        }
    
        $this->manticore = new ManticoreService(env('MANTICORE_MYSQL_HOST'), env('MANTICORE_MYSQL_PORT'));

        $this->manticore->dropTable('products');
        $this->manticore->createTable('products', 'product_id int, title text, short_description text, price int, code string, image string', 'stem_en, stem_ru', '2');
        $counter = 0;

        Product::with(['offers'])->whereVisibility(true)
               ->whereHas('offers', function ($query) {
                       $query->whereVisibility(true);
               })
               ->select(['id', 'title', 'short_description', 'code'])
               ->chunk(500, function($products) use (&$counter)
                {
                   $values = $products->map( function($product) {
                        if ($product->offers->count()) {
                            $price = $product->offers->sortBy('price')->first()->price;
                            $description=preg_replace('/[\'"]/ui', '', $product->short_description);
                            $title=preg_replace('/[\'"]/ui', ' ', $product->title);
                            return "({$product->id}, '$title', '$description', $price, '{$product->code}', 'image')";
                        }
                    });

                    $count = $this->manticore->addDocuments('products', 'product_id, title, short_description, price, code, image', $values->implode(','));

                    if (is_integer($count)) $counter+=$count;
                });
        
        $this->info("Добавлено строк (products): $counter");

        
        $this->manticore->dropTable('users');
        $this->manticore->createTable('users', 'user_id int, info text', 'stem_en, stem_ru');
        $counter = 0;

        User::select(['id', 'name', 'surname', 'phone', 'email'])
               ->chunk(500, function($users) use (&$counter)
                {
                   $values = $users->map( function($user) {
                        $info = preg_replace('/[\'"]/ui', ' ', "{$user->name} {$user->surname} {$user->phone} {$user->email}");
                        return "({$user->id}, '$info')";
                    });

                    $count = $this->manticore->addDocuments('users', 'user_id, info', $values->implode(','));

                    if (is_integer($count)) $counter+=$count;
                    else $this->error($count);
                });
        
        $this->info("Добавлено строк (users): $counter");
    }
}