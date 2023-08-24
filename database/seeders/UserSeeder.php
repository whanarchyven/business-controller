<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //Permissions

        $createLead = Permission::where('slug', 'create-lead')->first();
        $readLead = Permission::where('slug', 'read-lead')->first();
        $updateLead = Permission::where('slug', 'update-lead')->first();
        $deleteLead = Permission::where('slug', 'delete-lead')->first();
        $rejectLead = Permission::where('slug', 'reject-lead')->first();
        $refundLead = Permission::where('slug', 'refund-lead')->first();
        $manageLeadToManager = Permission::where('slug', 'manage-lead-to-manager')->first();
        $createRepair = Permission::where('slug', 'create-repair')->first();
        $readRepair = Permission::where('slug', 'read-repair')->first();
        $updateRepair = Permission::where('slug', 'update-repair')->first();
        $deleteRepair = Permission::where('slug', 'delete-repair')->first();
        $rejectRepair = Permission::where('slug', 'reject-repair')->first();
        $refundRepair = Permission::where('slug', 'refund-repair')->first();
        $manageRepairToMaster = Permission::where('slug', 'manage-repair-to-master')->first();
        $createUser = Permission::where('slug', 'create-user')->first();
        $readUser = Permission::where('slug', 'read-user')->first();
        $updateUser = Permission::where('slug', 'update-user')->first();
        $deleteUser = Permission::where('slug', 'delete-user')->first();
        $refundUser = Permission::where('slug', 'refund-user')->first();
        $readSelfSalary = Permission::where('slug', 'read-self-salary')->first();
        $readOthersSalary = Permission::where('slug', 'read-others-salary')->first();
        $manageRolesAndPemissions = Permission::where('slug', 'manage-roles-and-permissions')->first();
        $createTransactions = Permission::where('slug', 'create-transactions')->first();
        $readTransactions = Permission::where('slug', 'read-transactions')->first();
        $updateTransactions = Permission::where('slug', 'update-transactions')->first();
        $deleteTransactions = Permission::where('slug', 'delete-transactions')->first();
        $approveTransactions = Permission::where('slug', 'approve-transactions')->first();
        $createNomenclature = Permission::where('slug', 'create-nomenclature')->first();
        $readNomenclature = Permission::where('slug', 'read-nomenclature')->first();
        $updateNomenclature = Permission::where('slug', 'update-nomenclature')->first();
        $deleteNomenclature = Permission::where('slug', 'delete-nomenclature')->first();
        $createIncome = Permission::where('slug', 'create-income')->first();
        $readIncome = Permission::where('slug', 'read-income')->first();
        $updateIncome = Permission::where('slug', 'update-income')->first();
        $deleteIncome = Permission::where('slug', 'delete-income')->first();
        $createExpedinture = Permission::where('slug', 'create-expedinture')->first();
        $readExpedinture = Permission::where('slug', 'read-expedinture')->first();
        $updateExpedinture = Permission::where('slug', 'update-expedinture')->first();
        $deleteExpedinture = Permission::where('slug', 'delete-expedinture')->first();


        //Roles

        $operator = Role::where('slug', 'operator')->first();
        $manager = Role::where('slug', 'manager')->first();
        $master = Role::where('slug', 'master')->first();
        $coordinator = Role::where('slug', 'coordinator')->first();
        $director = Role::where('slug', 'director')->first();
        $admin = Role::where('slug', 'admin')->first();


        $tempOperator = new User();
        $tempOperator->name = 'Пробный оператор';
        $tempOperator->email = 'operator@gmail.com';
        $tempOperator->password = bcrypt('secret');
        $tempOperator->city = 1;
        $tempOperator->save();
        $tempOperator->roles()->attach($operator);
        $tempOperator->permissions()->attach($createLead);

        $tempManager = new User(); //2
        $tempManager->name = 'Пробный менеджер';
        $tempManager->email = 'manager@gmail.com';
        $tempManager->password = bcrypt('secret');

        $tempManager->city = 1;
        $tempManager->save();
        $tempManager->roles()->attach($manager);
        $tempManager->permissions()->attach($readLead);

        $tempManager2 = new User(); //3
        $tempManager2->name = 'Наталья Агафонова';
        $tempManager2->email = 'manager2@gmail.com';
        $tempManager2->password = bcrypt('secret');

        $tempManager2->city = 1;
        $tempManager2->save();
        $tempManager2->roles()->attach($manager);
        $tempManager2->permissions()->attach($readLead);

        $tempManager3 = new User(); //4
        $tempManager3->name = 'Иван Дулин';
        $tempManager3->email = 'manager3@gmail.com';
        $tempManager3->password = bcrypt('secret');

        $tempManager3->city = 2;
        $tempManager3->save();
        $tempManager3->roles()->attach($manager);
        $tempManager3->permissions()->attach($readLead);

        $tempManager4 = new User(); //5
        $tempManager4->name = 'Артём Леонтьев';
        $tempManager4->email = 'manager4@gmail.com';
        $tempManager4->password = bcrypt('secret');

        $tempManager4->city = 3;
        $tempManager4->save();
        $tempManager4->roles()->attach($manager);
        $tempManager4->permissions()->attach($readLead);


        $tempMaster = new User();
        $tempMaster->name = 'Пробный мастер';
        $tempMaster->email = 'master@gmail.com';
        $tempMaster->password = bcrypt('secret');
        $tempMaster->city = 1;
        $tempMaster->save();
        $tempMaster->roles()->attach($master);
        $tempMaster->permissions()->attach($readSelfSalary);

        $tempCoordinator = new User(); //6
        $tempCoordinator->name = 'Пробный координатор';
        $tempCoordinator->email = 'coordinator@gmail.com';
        $tempCoordinator->password = bcrypt('secret');
        $tempCoordinator->city = 1;
        $tempCoordinator->save();
        $tempCoordinator->roles()->attach($coordinator);
        $tempCoordinator->permissions()->attach(
            $readSelfSalary,
        );

        $tempDirector = new User();
        $tempDirector->name = 'Пробный директор';
        $tempDirector->email = 'director@gmail.com';
        $tempDirector->password = bcrypt('secret');
        $tempDirector->city = 1;
        $tempDirector->save();
        $tempDirector->roles()->attach($director);
        $tempDirector->permissions()->attach(
            $createLead,

        );


    }
}
