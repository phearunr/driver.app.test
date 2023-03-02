<template>
    <div>
        <div v-for="(message, i) in messages">
            <message-item :message="message" />
        </div>
    </div>
</template>

<script>
import MessageItem from '../Chats/MessageItem.vue';

export default {
    props: ['messages'],
    components: { MessageItem }
}
</script>
