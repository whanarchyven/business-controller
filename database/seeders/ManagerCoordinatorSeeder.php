<?php

namespace Database\Seeders;

use App\Models\ManagerCoordinator;
use Illuminate\Database\Seeder;

class ManagerCoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managerCoordinator = new ManagerCoordinator();
        $managerCoordinator->manager_id = 6;
        $managerCoordinator->coordinator_id = 8;
        $managerCoordinator->save();

        $managerCoordinator1 = new ManagerCoordinator();
        $managerCoordinator1->manager_id = 10;
        $managerCoordinator1->coordinator_id = 8;
        $managerCoordinator1->save();

        $managerCoordinator2 = new ManagerCoordinator();
        $managerCoordinator2->manager_id = 12;
        $managerCoordinator2->coordinator_id = 8;
        $managerCoordinator2->save();
    }
}
