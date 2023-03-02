<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatRooms\ChatController;

Route::middleware([
    'auth:sanctum',
])->prefix('chatrooms')
    ->name('chatrooms.')
    ->group(function () {

        Route::controller(ChatController::class)->group(function ($router) {
            $router->get('/chat', 'index')->name('chat.index');
            $router->get('/chat/rooms', 'rooms')->name('chat.rooms');
            $router->get('/chat/room/{roomId}/messages', 'messages')->name('chat.room.messages');
            $router->post('/chat/room/{roomId}/message', 'newMessage')->name('chat.room.newmessage');

            // $router->get('/chat/room/{roomId}/messages', function(){
            //     return ChatMessege::create([
            //         'user_id' => 1,
            //         'chat_room_id' => 1,
            //         'message' => 'sdjjjsd'
            //     ]);
            // });
        });
    });
