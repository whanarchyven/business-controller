<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    /**
     * @var mixed|string
     */
    protected $guarded = [];

    public function budget()
    {
        return $this->hasOne(Budget::class, 'city_id')->first();
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Budget::class, 'city_id', 'budget_id')->get();
    }

    public function transactionsPaginate()
    {
        return $this->hasManyThrough(Transaction::class, Budget::class, 'city_id', 'budget_id')->orderBy('id','desc');
    }

    public function getTransactionQuery(string $date='',string $description='',int $state=0,string $type='',int $responsible=0)
    {
        $query = [];
        if ($date!='') {
            array_push($query,['transactions.created_at','LIKE', '%'.$date.'%']);
        }
        if($description!=''){
            array_push($query,['description','LIKE', '%'.$description.'%']);
        }
        if($state){
            array_push($query,['state_id','=', $state]);
        }
        if($responsible){
            array_push($query,['responsible','=', $responsible]);
        }
        if($type!=''){
            array_push($query,['type','=',$type]);
        }

//        dd($query);

        if (empty($query)) {
            $result=$this->hasManyThrough(Transaction::class, Budget::class, 'city_id', 'budget_id')->get();
        }
        else{
            $result=$this->hasManyThrough(Transaction::class, Budget::class, 'city_id', 'budget_id')->where($query)->get();
        }


        return $result;
    }

}
