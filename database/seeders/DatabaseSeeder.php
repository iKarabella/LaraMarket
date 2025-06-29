<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(!Entity::whereId(1)->exists())
        {
            $entity = Entity::create(['id'=>1, 'name' => 'Единицы измерения', 'description' => 'Единицы измерения']);
        
            DB::table('entity_values')->insert([
                ['id'=>1, 'entity' => $entity->id, 'value' => 'г.', 'description'=>'Граммы', 'available'=>true],
                ['id'=>2, 'entity' => $entity->id, 'value' => 'кг.', 'description'=>'Килограммы', 'available'=>true],
                ['id'=>3, 'entity' => $entity->id, 'value' => 'шт.', 'description'=>'Количество', 'available'=>true],
                ['id'=>4, 'entity' => $entity->id, 'value' => 'л.', 'description'=>'Литр', 'available'=>true],
            ]);
        }

        if(!Entity::whereId(2)->exists())
        {
            $entity = Entity::create(['id'=>2, 'name' => 'Статус заказа', 'description' => 'Статусы заказа']);
        
            DB::table('entity_values')->insert([
                ['id'=>5, 'entity' => $entity->id, 'value' => 'Создан', 'description'=>'Создан', 'available'=>true],
                ['id'=>6, 'entity' => $entity->id, 'value' => 'Ожидает оплаты', 'description'=>'Ожидает оплаты', 'available'=>true],
                ['id'=>7, 'entity' => $entity->id, 'value' => 'Оплачен', 'description'=>'Оплачен', 'available'=>true],
                ['id'=>8, 'entity' => $entity->id, 'value' => 'В сборке', 'description'=>'В сборке', 'available'=>true],
                ['id'=>9, 'entity' => $entity->id, 'value' => 'Отправлен', 'description'=>'Отправлен', 'available'=>true],
                ['id'=>10, 'entity' => $entity->id, 'value' => 'Готов к выдаче', 'description'=>'Готов к выдаче', 'available'=>true],
                ['id'=>11, 'entity' => $entity->id, 'value' => 'Отменен', 'description'=>'Отменен', 'available'=>true],
                ['id'=>12, 'entity' => $entity->id, 'value' => 'Получен', 'description'=>'Получен', 'available'=>true],
            ]);
        }
    }
}
