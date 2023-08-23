<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manager = new Role();
        $manager->name = 'Operator';
        $manager->slug = 'operator';
        $manager->save();


        $manager = new Role();
        $manager->name = 'manager';
        $manager->slug = 'manager';
        $manager->save();

        $master = new Role();
        $master->name = 'master';
        $master->slug = 'master';
        $master->save();

        $coordinator = new Role();
        $coordinator->name = 'coordinator';
        $coordinator->slug = 'coordinator';
        $coordinator->save();


        $director = new Role();
        $director->name = 'director';
        $director->slug = 'director';
        $director->save();


        $admin = new Role();
        $admin->name = 'admin';
        $admin->slug = 'admin';
        $admin->save();
    }
}