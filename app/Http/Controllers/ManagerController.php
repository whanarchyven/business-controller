<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Psr7;
use GuzzleHttp;

class ManagerController extends Controller
{
    public function changeManagerStatus($user, Request $request)
    {

    $links = ["meeting-accepted" => 'https://i.ibb.co/pzq1fBs/accepted.png', "free" => 'https://i.ibb.co/CWHv2SM/free.png', "weekend" => 'https://i.ibb.co/HYqZfnV/weekend.png', "meeting-managed" => "https://i.ibb.co/8zsXb74/managed.png", "dinner" => "https://i.ibb.co/0mNJQLz/dinner.png", "on-meeting" => "https://i.ibb.co/chyhmjN/on-meeting.png", "delaying" => 'https://i.ibb.co/xfgpKH5/delaying.png'];

        $user = User::where([['id', '=', $user]])->first();
        $user->status = $request['status'];
        if ($user->chat_bot_id) {
            $client = new GuzzleHttp\Client();
            $response = $client->request('POST', 'https://api.telegram.org/bot6384276235:AAEGyfBmhCSgizgLa3_vRbZ1VSFcPtYZAHk/setChatPhoto?chat_id=' . $user->chat_bot_id, [
                'multipart' => [
                    [
                        'name' => 'photo',
                        'contents' => fopen($links[$request['status']], 'r')
                    ],
                ]
            ]);
        }
        $user->save();
        return redirect()->back();
    }
}
