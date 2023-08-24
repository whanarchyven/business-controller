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
        $managerCoordinator->manager_id = 5;
        $managerCoordinator->coordinator_id = 6;
        $managerCoordinator->save();

        $managerCoordinator1 = new ManagerCoordinator();
        $managerCoordinator1->manager_id = 4;
        $managerCoordinator1->coordinator_id = 6;
        $managerCoordinator1->save();

        $managerCoordinator2 = new ManagerCoordinator();
        $managerCoordinator2->manager_id = 3;
        $managerCoordinator2->coordinator_id = 6;
        $managerCoordinator2->save();

        $managerCoordinator3 = new ManagerCoordinator();
        $managerCoordinator3->manager_id = 2;
        $managerCoordinator3->coordinator_id = 6;
        $managerCoordinator3->save();
    }
}
