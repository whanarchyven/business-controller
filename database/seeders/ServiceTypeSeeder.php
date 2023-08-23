<?php

namespace Database\Seeders;

use \App\Models\ServiceType;

use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $windows = new ServiceType();
        $windows->name = 'Окна';
        $windows->save();

        $pvh = new ServiceType();
        $pvh->name = 'Конструкция ПВХ';
        $pvh->save();


        $multiprofile = new ServiceType();
        $multiprofile->name = 'Многопрофиль';
        $multiprofile->save();

        $multiprofile = new ServiceType();
        $multiprofile->name = 'Электрика';
        $multiprofile->save();
    }
}
