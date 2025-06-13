<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasuresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        if(!Entity::whereName('Единицы измерения')->count())
        {
            $entity = Entity::create(['name' => 'Единицы измерения', 'description' => 'Единицы измерения']);
        
            DB::table('entity_values')->insert([
                ['entity' => $entity->id, 'value' => 'г.', 'description'=>'Граммы', 'available'=>true],
                ['entity' => $entity->id, 'value' => 'кг.', 'description'=>'Килограммы', 'available'=>true],
                ['entity' => $entity->id, 'value' => 'шт.', 'description'=>'Количество', 'available'=>true],
                ['entity' => $entity->id, 'value' => 'л.', 'description'=>'Литр', 'available'=>true],
            ]);
        }
    }
}
