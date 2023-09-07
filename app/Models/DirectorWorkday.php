<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectorWorkday extends Model
{
    use HasFactory;

    protected $fillable = ['director_id'];
}
