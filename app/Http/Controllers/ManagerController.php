<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function changeManagerStatus(User $user, $status)
    {
        $user->update(['status' => $status]);
    }
}
