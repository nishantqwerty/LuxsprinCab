<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Chat;
use App\Models\UserChat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $chats = Chat::with('user')->get();
        return view('admin.messages.index', compact('chats'));
    }

    public function show()
    {
        $chats = UserChat::where('chat_room_id', $_GET['id'])->get();
        return view('admin.messages.add', compact('chats'));
    }

    public function saveChat(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'msg'     =>  'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            // $UserChat = Userchat::find(Auth::user()->id);
            $userchat_data  = [

                'message' => $data['msg'],
                'chat_room_id' => $_GET['id'],
                'user_id' => Auth::user()->id,
                'user_role' => SUPER_ADMIN,
            ];
            $UserChat = UserChat::create($userchat_data);

            $update_chat = Chat::where('chat_room_id', $_GET['id'])->first();
            if ($update_chat) {
                $update_chat->update([
                    'message'   => $data['msg']
                ]);
            } else {
                $chat_data = [
                    'message' => $data['msg'],
                    'chat_room_id' => $_GET['id'],
                    'user_id' => $_GET['id']
                ];
                $chat = Chat::create($chat_data);
            }
            if ($UserChat) {
                return back();
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }
}
