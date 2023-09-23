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


//        $tempOperator = new User();
//        $tempOperator->name = 'Пробный оператор';
//        $tempOperator->email = 'operator@gmail.com';
//        $tempOperator->password = bcrypt('secret');
//        $tempOperator->city = 1;
//        $tempOperator->save();
//        $tempOperator->roles()->attach($operator);
//        $tempOperator->permissions()->attach($createLead);
//
//        $tempManager = new User(); //2
//        $tempManager->name = 'Пробный менеджер';
//        $tempManager->email = 'manager@gmail.com';
//        $tempManager->password = bcrypt('secret');
//        $tempManager->city = 1;
//        $tempManager->save();
//        $tempManager->roles()->attach($manager);
//        $tempManager->permissions()->attach($readLead);
//
//
//        $tempManager2 = new User(); //3
//        $tempManager2->name = 'Наталья Агафонова';
//        $tempManager2->email = 'manager2@gmail.com';
//        $tempManager2->password = bcrypt('secret');
//        $tempManager2->city = 1;
//        $tempManager2->save();
//        $tempManager2->roles()->attach($manager);
//        $tempManager2->permissions()->attach($readLead);
//
//        $tempManager3 = new User(); //4
//        $tempManager3->name = 'Иван Дулин';
//        $tempManager3->email = 'manager3@gmail.com';
//        $tempManager3->password = bcrypt('secret');
//        $tempManager3->city = 2;
//        $tempManager3->save();
//        $tempManager3->roles()->attach($manager);
//        $tempManager3->permissions()->attach($readLead);
//
//        $tempManager4 = new User(); //5
//        $tempManager4->name = 'Артём Леонтьев';
//        $tempManager4->email = 'manager4@gmail.com';
//        $tempManager4->password = bcrypt('secret');
//        $tempManager4->city = 3;
//        $tempManager4->save();
//        $tempManager4->roles()->attach($manager);
//        $tempManager4->permissions()->attach($readLead);
//
//
//        $tempMaster = new User();
//        $tempMaster->name = 'Пробный мастер';
//        $tempMaster->email = 'master@gmail.com';
//        $tempMaster->password = bcrypt('secret');
//        $tempMaster->city = 1;
//        $tempMaster->save();
//        $tempMaster->roles()->attach($master);
//        $tempMaster->permissions()->attach($readSelfSalary);
//
//        $tempCoordinator = new User(); //6
//        $tempCoordinator->name = 'Пробный координатор';
//        $tempCoordinator->email = 'coordinator@gmail.com';
//        $tempCoordinator->password = bcrypt('secret');
//        $tempCoordinator->city = 1;
//        $tempCoordinator->save();
//        $tempCoordinator->roles()->attach($coordinator);
//        $tempCoordinator->permissions()->attach(
//            $readSelfSalary,
//        );
//
//        $tempDirector1 = new User();
//        $tempDirector1->documents='';
//        $tempDirector1->name = 'Генеральный директор';
//        $tempDirector1->email = 'Gen-director';
//        $tempDirector1->password = bcrypt('GoncharovV88');
//        $tempDirector1->city = 1;
//        $tempDirector1->isAdmin = true;
//        $tempDirector1->save();
//        $tempDirector1->roles()->attach($director);
//        $tempDirector1->permissions()->attach(
//            $createLead,
//        );
//
//        $tempDirector2 = new User();
//        $tempDirector2->documents='';
//        $tempDirector2->name = 'Генеральный руководитель';
//        $tempDirector2->email = 'Gen-ruk';
//        $tempDirector2->password = bcrypt('Eartem24');
//        $tempDirector2->city = 1;
//        $tempDirector2->isAdmin = true;
//        $tempDirector2->save();
//        $tempDirector2->roles()->attach($director);
//        $tempDirector2->permissions()->attach(
//            $createLead,
//        );
//
//        $tempDirector3 = new User();
//        $tempDirector3->documents='';
//        $tempDirector3->name = 'Складской аудитор';
//        $tempDirector3->email = 'AuditorKRD';
//        $tempDirector3->password = bcrypt('OkkKRD01');
//        $tempDirector3->city = 1;
//        $tempDirector3->isAdmin = true;
//        $tempDirector3->save();
//        $tempDirector3->roles()->attach($director);
//        $tempDirector3->permissions()->attach(
//            $createLead,
//        );
//
//        $tempDirector4 = new User();
//        $tempDirector4->documents='';
//        $tempDirector4->name = 'Системный администратор';
//        $tempDirector4->email = 'whanarchyvven';
//        $tempDirector4->password = bcrypt('Whaven1488!');
//        $tempDirector4->city = 1;
//        $tempDirector4->isAdmin = true;
//        $tempDirector4->save();
//        $tempDirector4->roles()->attach($director);
//        $tempDirector4->permissions()->attach(
//            $createLead,
//        );
//
//        $tempDirector5 = new User();
//        $tempDirector5->documents='';
//        $tempDirector5->name = 'temp director';
//        $tempDirector5->email = 'director';
//        $tempDirector5->password = bcrypt('secret');
//        $tempDirector5->city = 1;
//        $tempDirector5->isAdmin = true;
//        $tempDirector5->save();
//        $tempDirector5->roles()->attach($director);
//        $tempDirector5->permissions()->attach(
//            $createLead,
//        );
//
//        $tempDirector6 = new User();
//        $tempDirector6->documents='';
//        $tempDirector6->name = 'temp director';
//        $tempDirector6->email = 'director2';
//        $tempDirector6->password = bcrypt('secret');
//        $tempDirector6->city = 1;
//        $tempDirector6->isAdmin = false;
//        $tempDirector6->save();
//        $tempDirector6->roles()->attach($director);
//        $tempDirector6->permissions()->attach(
//            $createLead,
//        );


    }
}
