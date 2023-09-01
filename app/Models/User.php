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

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndPermissions;

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

    public function getSalary()
    {
        $date = Carbon::today();
        $date = preg_split("/[^1234567890]/", $date);
        return Salary::where(["year" => intval($date[0]), "month" => intval($date[1]), "user_id" => $this->id])->first();
    }

    public function salary($salary)
    {
        app(\App\Http\Controllers\SalaryController::class)->addSalary($this, $salary);
    }
}
