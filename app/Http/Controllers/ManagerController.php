<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    public function changeManagerStatus($user, Request $request)
    {

        $user = User::where([['id', '=', $user]])->first();
        $user->status = $request['status'];
        $user->save();
        return redirect()->back();
    }
}
