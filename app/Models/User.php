<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasRolesAndPermissions;
use App\Http\Controllers\SalaryControllerl;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndPermissions, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getManagersByCoordinator()
    {
        return ManagerCoordinator::where([['coordinator_id', '=', Auth::user()->id]])->get();
    }

    public function payedSalary($date)
    {
//        $date = Carbon::today();
        $date = preg_split("/[^1234567890]/", $date);
        $salary = Salary::where(["year" => intval($date[0]), "month" => intval($date[1]), "user_id" => $this->id])->first();
        if ($salary) {
            return $salary->salary;
        } else {
            return 0;
        }
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city')->first();
    }

    public function stagers($date){
        return User::whereBetween('created_at', [Carbon::createFromDate($date)->startOfMonth()->toDateString(), Carbon::createFromDate($date)->endOfMonth()->toDateString()])->where(["mentor_id"=>$this->id])->get();
    }

    public function shortname(){
        if(count(explode(' ',$this->name))>=3){
            return explode(' ',$this->name)[0].' '.explode(' ',$this->name)[1];
        }
        else{
            return $this->name;
        }
    }


    public function coordinatorCity()
    {
     return $this->hasManyThrough(
         City::class, // Целевая модель, которую вы хотите получить
         CoordinatorCity::class, // Промежуточная модель
         'user_id', // Внешний ключ в промежуточной модели CoordinatorCity
         'id', // Локальный ключ в модели User
         'id', // Локальный ключ в целевой модели City
         'city_id' // Внешний ключ в промежуточной модели CoordinatorCity
     )->get();
    }


    public function addSalary($salary,$date)
    {
        app(\App\Http\Controllers\SalaryController::class)->addSalary($this, $salary,$date);
    }

    public function salary($date)
    {
        if ($this->hasRole('manager')) {
            return app(\App\Http\Controllers\SalaryController::class)->getManagerSalary($this, $date);
        } elseif ($this->hasRole('director')) {
            return app(\App\Http\Controllers\SalaryController::class)->getDirectorSalary($this, $date);
        } elseif ($this->hasRole('master')) {
            return app(\App\Http\Controllers\SalaryController::class)->getMasterSalary($this, $date);
        } elseif ($this->hasRole('operator')) {
            return app(\App\Http\Controllers\SalaryController::class)->getOperatorSalary($this, $date);
        }
        return 'salary -';
    }

    public function deductions($date)
    {
//        $date = Carbon::today()->toDateString();
        $date = explode('-', $date);
        $startDate = Carbon::createFromDate($date[0], $date[1], 1)->startOfMonth();
        $endDate = Carbon::createFromDate($date[0], $date[1], 1)->endOfMonth();
        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $this->id, "type" => 'minus'])->get();
        $totalDeduction = 0;
        foreach ($deductions as $deduction) {
            $totalDeduction += $deduction->amount;
        }
        return $totalDeduction;
    }




}
