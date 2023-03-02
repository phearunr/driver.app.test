<template>
    <div class="relative h-10 m-1">
        <div style="border-top:1px solid #e6e6e6" class="grid grid-cols-6">
            <input
                type="text"
                v-model="message"
                @keyup.enter="sendMessage()"
                placeholder="say somthing ..."
                class="col-span outline-none p-1"
            />
            <button
                @click="sendMessage()"
                class="place-self-end"
            >
                Send
            </button>

        </div>
    </div>
</template>

<script>

    import axios from 'axios';

    export default {
        props:['room'],
        data: function(){
            return {
                message: null
            }
        },
        methods:{
            sendMessage(props){

                if(this.message == null){
                    return;
                }
                axios.post('/chatrooms/chat/room/' + 2 + '/message', {
                    message:this.message
                })
                .then(response => {
                    if(response.data == 201){
                        this.message = null;
                        this.$emit('messagesend');
                    }
                }).catch(error => {
                    console.log(error);
                });
            }
        }
    }
</script>
