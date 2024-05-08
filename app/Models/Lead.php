<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function jobType()
    {
        return $this->belongsTo(ServiceType::class, 'job_type');
    }

    public function getManagerId()
    {
        return $this->belongsTo(User::class, 'manager_id')->withTrashed();
    }

    public function getOperatorId()
    {
        return $this->belongsTo(User::class, 'operator_id')->withTrashed();
    }

    public function repair()
    {
        return $this->hasOne(Repair::class, 'lead_id');
    }

    public function profit(){
        $lead=$this;
        $repair=$this->repair;
        $marge=$lead->issued*0.6-($repair->master?$lead->issued*($repair->master_boost?0.15:0.1):0)-$repair->materialPrice();
//        dd($marge);
        return $marge;
    }
    public function marge(){
        $lead=$this;
        $marge=$this->profit();



        $marge_percent=round($marge/($lead->issued?$lead->issued:1)*100);
//        dd($marge);
        return $marge_percent;
    }
}
