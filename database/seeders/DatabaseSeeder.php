<?php

namespace Database\Seeders;

use App\Models\Budget;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(RoleSeeder::class);
//        $this->call(PermissionSeeder::class);
//        $this->call(ServiceTypeSeeder::class);
//        $this->call(CitySeeder::class);
        $this->call(UserSeeder::class);
//        $this->call(ManagerCoordinatorSeeder::class);
//        $this->call(TransactionStateSeeder::class);
//        $this->call(BudgetSeeder::class);
    }
}
