<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;

    protected $fillable = ["lead_id",
        "master_id",
        "check",
        "repair_date",
        "contract_number",
        "works",
        "documents",
        "status",];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function master()
    {
        return $this->belongsTo(User::class, 'master_id')->withTrashed();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'repair_id');
    }

    public function materials()
    {
        return $this->hasManyThrough(NomenclatureExpense::class, Expense::class, 'repair_id', 'expense_id');
    }

    public function materialPrice()
    {
        $materials = $this->materials;
        $summ = 0;
        foreach ($materials as $material) {
            $summ += $material->quantity * $material->nomenclature->price;
        }
        return $summ;
    }

    public function getResult(string $clientName='',string $address='',string $phone='',int $manager_id=0,int $master_id=0,string $status)
    {
        $query = [];
        if ($clientName) {
            array_push($query,['client_fullname','LIKE', '%'.$clientName.'%']);
        }
        if($address){
            array_push($query,['address','LIKE', '%'.$address.'%']);
        }
        if($phone){
            array_push($query,['phone','LIKE', '%'.$phone.'%']);
        }
        if($manager_id!=0){
            array_push($query,['manager_id','=',$manager_id]);
        }

//        if ($date) {
//            $query = $query + ['repair_date','=', $date];
//        }




        if (empty($query)) {
            $leads=Lead::all();
        }
        else{
            $leads=Lead::where($query)->get();
        }

        $result=[];

        foreach ($leads as $lead){
            if ($lead->repair){
                if ($status&&!$master_id){
                    if($lead->repair->status==$status){
                        array_push($result,$lead->repair);
                        continue 1;
                    }
                }
                elseif ($master_id&&!$status){
                    if($lead->repair->master_id==$master_id){
                        array_push($result,$lead->repair);
                        continue 1;
                    }
                }
                elseif ($master_id&&$status){
                    if($lead->repair->master_id==$master_id&&$lead->repair->status==$status){
                        array_push($result,$lead->repair);
                        continue 1;
                    }
                }
                else{
                    array_push($result,$lead->repair);
                }
            }
        }

        return $result;
    }

}
