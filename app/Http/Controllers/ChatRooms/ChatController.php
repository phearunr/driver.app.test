<?php

namespace App\Http\Controllers\ChatRooms;

use Inertia\Inertia;
use App\Models\ChatRoom;
use App\Models\ChatMessege;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Chats\ChatRoomResource;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Chats/IndexChats', [
            'rooms' => ChatRoom::all()
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rooms()
    {
        // return response()->json(
        //     ChatRoom::get([
        //         'id',
        //         'name'
        //     ])
        // );
        return response()->json([
            'rooms' =>  ChatRoom::get([
                'id',
                'name'
            ])
        ], Response::HTTP_OK);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function newMessage(Request $request, $roomId)
    {
        ChatMessege::create([
            'user_id' => auth()->id(),
            'chat_room_id' => $roomId,
            'message' => $request->message
        ]);
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function messages(Request $request, $roomId)
    {
        return ChatMessege::query()
            ->where('chat_room_id', $roomId)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
