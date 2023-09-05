<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\City;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = City::all();

        foreach ($cities as $city) {
            $budget = new Budget(["city_id" => $city->id, "money" => 0]);
            $budget->save();
        }
    }
}
