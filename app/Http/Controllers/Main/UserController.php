<?php

namespace App\Http\Controllers\Main;

use App\Events\Main\Chat\NewChatCreated;
use App\Events\Main\NewMessageReceived;
use App\Http\Controllers\Controller;
use App\Http\Requests\Main\UserRegisterRequest;
use App\Http\Resources\Main\ConversationResource;
use App\Http\Resources\Main\MessageResource;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = [];
        if ($request->search) {
            $users = User::where('name', 'like', "%$request->search%")
                ->orWhere('email', 'like', "%$request->search%")
                ->get();
        }
        return response()->json(['users' => $users]);
    }
    public function store(UserRegisterRequest $request)
    {
        $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $request->email . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('/images/users', $filename, 'public');
        }
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'image' => $path
        ]);
        Auth::login($user);
        return $user;
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            return response()->json(['visitor' => $user]);
        } else {
            return response()->json(['errors' => ['email' => 'Wrong email or password']], 422);
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json(['user' => $user]);
    }
    public function showChat($userId)
    {
        $chat = Auth::user()->customChat($userId);
        if (!$chat) {
            $chat = Conversation::create([
                'initiator_id' => Auth::user()->id,
                'receiver_id' => $userId,
                'last_message' => now()->toDateTimeString(),
            ]);

            broadcast(new NewChatCreated(new ConversationResource($chat)));
        }
        $messages = MessageResource::collection($chat->messages);
        if ($chat->initiator_id == Auth::user()->id) {
            $partner = $chat->receiver();
        } else {
            $partner = $chat->initiator();
        }
        return response()->json(compact('chat', 'messages', 'partner'));
    }
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}