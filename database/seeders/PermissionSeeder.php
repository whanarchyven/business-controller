<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ////LEADS

        //CRUD

        $createLead = new Permission();
        $createLead->name = 'Create lead';
        $createLead->slug = 'create-lead';
        $createLead->save();


        $readLead = new Permission();
        $readLead->name = 'Read lead';
        $readLead->slug = 'read-lead';
        $readLead->save();

        $updateLead = new Permission();
        $updateLead->name = 'Update lead';
        $updateLead->slug = 'update-lead';
        $updateLead->save();

        $deleteLead = new Permission();
        $deleteLead->name = 'Delete lead';
        $deleteLead->slug = 'delete-lead';
        $deleteLead->save();

        //

        //CHANGE LEAD STATUS

        $rejectLead = new Permission();
        $rejectLead->name = 'Reject lead';
        $rejectLead->slug = 'reject-lead';
        $rejectLead->save();

        $refundLead = new Permission();
        $refundLead->name = 'Refund lead';
        $refundLead->slug = 'refund-lead';
        $refundLead->save();

        $manageLeadToManager = new Permission();
        $manageLeadToManager->name = 'Manage lead to manager';
        $manageLeadToManager->slug = 'manage-lead-to-manager';
        $manageLeadToManager->save();

        //

        //// REPAIRS

        //CRUD

        $createRepair = new Permission();
        $createRepair->name = 'Create repair';
        $createRepair->slug = 'create-repair';
        $createRepair->save();


        $readRepair = new Permission();
        $readRepair->name = 'Read repair';
        $readRepair->slug = 'read-repair';
        $readRepair->save();

        $updateRepair = new Permission();
        $updateRepair->name = 'Update repair';
        $updateRepair->slug = 'update-repair';
        $updateRepair->save();

        $deleteRepair = new Permission();
        $deleteRepair->name = 'Delete repair';
        $deleteRepair->slug = 'delete-repair';
        $deleteRepair->save();

        //

        //CHANGE REPAIR STATUS

        $rejectRepair = new Permission();
        $rejectRepair->name = 'Reject repair';
        $rejectRepair->slug = 'reject-repair';
        $rejectRepair->save();

        $refundRepair = new Permission();
        $refundRepair->name = 'Refund repair';
        $refundRepair->slug = 'refund-repair';
        $refundRepair->save();

        $manageRepairToMaster = new Permission();
        $manageRepairToMaster->name = 'Manage repair to master';
        $manageRepairToMaster->slug = 'manage-repair-to-master';
        $manageRepairToMaster->save();

        //


        ////USERS(EMPLOYERS)

        $createUser = new Permission();
        $createUser->name = 'Create user';
        $createUser->slug = 'create-user';
        $createUser->save();

        $readUser = new Permission();
        $readUser->name = 'Read user';
        $readUser->slug = 'read-user';
        $readUser->save();

        $updateUser = new Permission();
        $updateUser->name = 'Update user';
        $updateUser->slug = 'update-user';
        $updateUser->save();

        $deleteUser = new Permission();
        $deleteUser->name = 'Delete user';
        $deleteUser->slug = 'delete-user';
        $deleteUser->save();

        $refundUser = new Permission();
        $refundUser->name = 'Refund user';
        $refundUser->slug = 'refund-user';
        $refundUser->save();

        $readSelfSalary = new Permission();
        $readSelfSalary->name = 'Read self salary';
        $readSelfSalary->slug = 'read-self-salary';
        $readSelfSalary->save();

        $readOthersSalary = new Permission();
        $readOthersSalary->name = 'Read others salary';
        $readOthersSalary->slug = 'read-others-salary';
        $readOthersSalary->save();

        $manageRolesAndPemissions = new Permission();
        $manageRolesAndPemissions->name = 'Manage roles and permissions';
        $manageRolesAndPemissions->slug = 'manage-roles-and-permissions';
        $manageRolesAndPemissions->save();
        ////

        ////TRANSACTIONS

        $createTransactions = new Permission();
        $createTransactions->name = 'Create transactions';
        $createTransactions->slug = 'create-transactions';
        $createTransactions->save();

        $readTransactions = new Permission();
        $readTransactions->name = 'Read transactions';
        $readTransactions->slug = 'read-transactions';
        $readTransactions->save();

        $updateTransactions = new Permission();
        $updateTransactions->name = 'Update transactions';
        $updateTransactions->slug = 'update-transactions';
        $updateTransactions->save();

        $deleteTransactions = new Permission();
        $deleteTransactions->name = 'Delete transactions';
        $deleteTransactions->slug = 'delete-transactions';
        $deleteTransactions->save();

        $approveTransactions = new Permission();
        $approveTransactions->name = 'Approve transactions';
        $approveTransactions->slug = 'approve-transactions';
        $approveTransactions->save();


        ////WAREHOUSE

        //NOMENCLATURE

        $createNomenclature = new Permission();
        $createNomenclature->name = 'Create nomenclature';
        $createNomenclature->slug = 'create-nomenclature';
        $createNomenclature->save();

        $readNomenclature = new Permission();
        $readNomenclature->name = 'Read nomenclature';
        $readNomenclature->slug = 'read-nomenclature';
        $readNomenclature->save();

        $updateNomenclature = new Permission();
        $updateNomenclature->name = 'Update nomenclature';
        $updateNomenclature->slug = 'update-nomenclature';
        $updateNomenclature->save();

        $deleteNomenclature = new Permission();
        $deleteNomenclature->name = 'Delete nomenclature';
        $deleteNomenclature->slug = 'delete-nomenclature';
        $deleteNomenclature->save();

        //INCOME

        $createIncome = new Permission();
        $createIncome->name = 'Create income';
        $createIncome->slug = 'create-income';
        $createIncome->save();

        $readIncome = new Permission();
        $readIncome->name = 'Read income';
        $readIncome->slug = 'read-income';
        $readIncome->save();

        $updateIncome = new Permission();
        $updateIncome->name = 'Update income';
        $updateIncome->slug = 'update-income';
        $updateIncome->save();

        $deleteIncome = new Permission();
        $deleteIncome->name = 'Delete income';
        $deleteIncome->slug = 'delete-income';
        $deleteIncome->save();


        //EXPEDINTURE

        $createExpedinture = new Permission();
        $createExpedinture->name = 'Create expedinture';
        $createExpedinture->slug = 'create-expedinture';
        $createExpedinture->save();

        $readExpedinture = new Permission();
        $readExpedinture->name = 'Read expedinture';
        $readExpedinture->slug = 'read-expedinture';
        $readExpedinture->save();

        $updateExpedinture = new Permission();
        $updateExpedinture->name = 'Update expedinture';
        $updateExpedinture->slug = 'update-expedinture';
        $updateExpedinture->save();

        $deleteExpedinture = new Permission();
        $deleteExpedinture->name = 'Delete expedinture';
        $deleteExpedinture->slug = 'delete-expedinture';
        $deleteExpedinture->save();
    }
}