<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chats {{ chatRooms }}
            </h2>
            <div>
                <!-- v-on:roomChanged="setRoom($event)" -->

                <chat-room-section
                    :rooms="chatRooms"
                    :currentRoom="currentRoom"
                />
            </div>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <message-container
                        :messages="messages"
                    />
                    <input-message
                        :room="currentRoom"
                        v-on:messagesend="getMessages()"

                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>

    import AppLayout from '@/Layouts/AppLayout.vue';
    import chatRoomSection from '../Chats/chatRoomSection.vue';
    import MessageContainer from '../Chats/MessageContainer.vue';
    import InputMessage from '../Chats/InputMessage.vue';
    import axios from 'axios';

    export default {
        components:{
            AppLayout,
            chatRoomSection,
            MessageContainer,
            InputMessage
        },
        methods:{
            data: function() {
                return {
                   chatRooms: Array,
                   currentRoom: Array,
                   messages: Array
                }
            },
            getRooms(){
               // alert(1);
                axios.get('/chatrooms/chat/rooms')
                .then(response => {
                    let chatRooms = response.data.rooms
                    if (chatRooms.length) {

                        //this.chatRooms = cloneDeep(chatRooms)
                        // this.setRoom(chatRooms[0]);
                    }
                    console.log(chatRooms);

                   // console.log(response.data[0]);
                   // Object.assign({}, result.products);

                    // Object.assign({}, response.data);
                   ;
                })
                .catch(error => {
                    console.log(error);
                });
            },
            setRoom(room){
                this.currentRoom = room;
                this.getMessages();
            },
            getMessages(props){
                console.log(this.currentRoom.id)
                axios.get('/chatrooms/chat/room/'+this.currentRoom.id+'/messages')
                .then(response => {
                   // console.log(response.data);
                   this.messages = response.data;
                  // console.log(this.messages );
                })
                .catch(error => {
                    console.log(error);
                });
            }
        },
        created(){
            this.getRooms();
        }
    }
</script>


