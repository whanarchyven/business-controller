<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $city1 = new City();
        $city1->name = 'Казань';
        $city1->save();

        $city1 = new City();
        $city1->name = 'Нижний Новгород';
        $city1->save();

        $city1 = new City();
        $city1->name = 'Краснодар';
        $city1->save();
    }
}
